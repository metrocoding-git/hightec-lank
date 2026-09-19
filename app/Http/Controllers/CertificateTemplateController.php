<?php

namespace App\Http\Controllers;

use App\Models\CertificateSerialPrefix;
use App\Models\CertificateTemplate;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CertificateTemplateController extends Controller
{
    public function index(Request $request)
    {
        // Requirement #6 — disabled ("deleted") templates must never show
        // up in this list again, even though their row/links stay intact
        // underneath for old certificates. Hence ->active() here, where
        // the original had no filter at all.
        $templates = CertificateTemplate::with(['courses', 'serialPrefix'])
            ->active()
            ->orderByDesc('id')
            ->get();

        $courseOptions = Course::orderBy('name')->get(['id', 'name']);

        // Feeds the "Serial Number Prefix" <datalist> in the form, so
        // typing an existing prefix visibly matches one already in use
        // (requirement #4's "if it exists, auto select that one").
        $serialPrefixOptions = CertificateSerialPrefix::orderBy('prefix')->get(['id', 'prefix']);

        return view('pages.administration.certificate.template.index', compact('templates', 'courseOptions', 'serialPrefixOptions'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'course_ids' => 'nullable|array',
            'course_ids.*' => 'integer|exists:courses,id',
            'course_name_line1' => 'required|string|max:255',
            'course_name_line2' => 'nullable|string|max:255',
            'duration' => 'nullable|string|max:255',
            'serial_prefix' => 'nullable|string|max:50',
            'bottom_image' => 'nullable|image|mimes:png,jpg,jpeg|max:4096',
            'qr_code_image' => 'nullable|image|mimes:png,jpg,jpeg|max:4096',
            'back_side_pdf' => 'nullable|file|mimes:pdf|max:10240',
        ]);

        $courseIds = $data['course_ids'] ?? [];

        // Requirement #2 — "one course can have one template only" (one
        // ACTIVE template, that is — see the migration's doc-comment for
        // why this can't just be a DB unique constraint).
        $conflict = $this->findCourseConflict($courseIds);
        if ($conflict) {
            return response()->json(['status' => 'conflict', 'message' => $conflict], 422);
        }

        $payload = [
            'name' => $data['name'],
            'course_name_line1' => $data['course_name_line1'],
            'course_name_line2' => $data['course_name_line2'] ?? null,
            'duration' => $data['duration'] ?? null,
            'created_by' => Auth::id(),
            'is_active' => true,
        ];

        if (!empty($data['serial_prefix'])) {
            $payload['serial_prefix_id'] = $this->resolveSerialPrefix($data['serial_prefix']);
        }

        if ($request->hasFile('bottom_image')) {
            $payload['bottom_image_path'] = $this->storeUploadedFile($request->file('bottom_image'), 'bottom-images');
        }

        if ($request->hasFile('qr_code_image')) {
            $payload['qr_code_image_path'] = $this->storeUploadedFile($request->file('qr_code_image'), 'qr-codes');
        }

        if ($request->hasFile('back_side_pdf')) {
            $payload['back_side_pdf_path'] = $this->storeUploadedFile($request->file('back_side_pdf'), 'back-side-pdfs');
        }

        $template = CertificateTemplate::create($payload);

        if (!empty($courseIds)) {
            $template->courses()->sync($courseIds);
        }

        return response()->json([
            'status' => 'ok',
            'message' => 'Certificate template created.',
            'template' => $template->load(['courses', 'serialPrefix']),
        ]);
    }

    public function update(Request $request, $id)
    {
        $template = CertificateTemplate::findOrFail($id);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'course_ids' => 'nullable|array',
            'course_ids.*' => 'integer|exists:courses,id',
            'course_name_line1' => 'required|string|max:255',
            'course_name_line2' => 'nullable|string|max:255',
            'duration' => 'nullable|string|max:255',
            'serial_prefix' => 'nullable|string|max:50',
            'bottom_image' => 'nullable|image|mimes:png,jpg,jpeg|max:4096',
            'qr_code_image' => 'nullable|image|mimes:png,jpg,jpeg|max:4096',
            'back_side_pdf' => 'nullable|file|mimes:pdf|max:10240',
        ]);

        $courseIds = $data['course_ids'] ?? [];

        $conflict = $this->findCourseConflict($courseIds, $template->id);
        if ($conflict) {
            return response()->json(['status' => 'conflict', 'message' => $conflict], 422);
        }

        $payload = [
            'name' => $data['name'],
            'course_name_line1' => $data['course_name_line1'],
            'course_name_line2' => $data['course_name_line2'] ?? null,
            'duration' => $data['duration'] ?? null,
            // Blank field = intentionally unlinked from any serial series;
            // a non-empty value resolves (or creates) the matching series.
            'serial_prefix_id' => !empty($data['serial_prefix'])
                ? $this->resolveSerialPrefix($data['serial_prefix'])
                : null,
        ];

        if ($request->hasFile('bottom_image')) {
            if ($template->bottom_image_path) {
                Storage::disk('public')->delete($template->bottom_image_path);
            }
            $payload['bottom_image_path'] = $this->storeUploadedFile($request->file('bottom_image'), 'bottom-images');
        }

        if ($request->hasFile('qr_code_image')) {
            if ($template->qr_code_image_path) {
                Storage::disk('public')->delete($template->qr_code_image_path);
            }
            $payload['qr_code_image_path'] = $this->storeUploadedFile($request->file('qr_code_image'), 'qr-codes');
        }

        if ($request->hasFile('back_side_pdf')) {
            if ($template->back_side_pdf_path) {
                Storage::disk('public')->delete($template->back_side_pdf_path);
            }
            $payload['back_side_pdf_path'] = $this->storeUploadedFile($request->file('back_side_pdf'), 'back-side-pdfs');
        }

        $template->update($payload);
        $template->courses()->sync($courseIds);

        return response()->json([
            'status' => 'ok',
            'message' => 'Certificate template updated.',
            'template' => $template->fresh(['courses', 'serialPrefix']),
        ]);
    }

    /**
     * One-way soft-disable — same convention as before (requirement #6
     * confirms this is exactly right: "delete button ok, but don't
     * delete, disable"). A template already used by generated
     * certificates stays attached to them, and its course links in
     * certificate_template_course are left untouched too, so an old
     * template keeps working for whatever already points at it. index()
     * above is what actually hides it from the list going forward.
     */
    public function destroy($id)
    {
        $template = CertificateTemplate::findOrFail($id);
        $template->is_active = false;
        $template->save();

        return response()->json([
            'status' => 'ok',
            'message' => 'Certificate template disabled.',
        ]);
    }

    /**
     * Requirement #2 — a course may only be linked to ONE active
     * template at a time. Finds any of the given $courseIds that are
     * already linked to a DIFFERENT active template, and returns a
     * human-readable message naming the clash(es), or null if there's
     * no conflict. $excludeTemplateId is the template being edited (so
     * it isn't flagged as conflicting with its own existing links).
     */
    private function findCourseConflict(array $courseIds, ?int $excludeTemplateId = null): ?string
    {
        if (empty($courseIds)) {
            return null;
        }

        $clashes = CertificateTemplate::query()
            ->active()
            ->when($excludeTemplateId, fn($q) => $q->where('id', '!=', $excludeTemplateId))
            ->whereHas('courses', fn($q) => $q->whereIn('courses.id', $courseIds))
            ->with(['courses' => fn($q) => $q->whereIn('courses.id', $courseIds)])
            ->get();

        if ($clashes->isEmpty()) {
            return null;
        }

        $lines = [];
        foreach ($clashes as $other) {
            foreach ($other->courses as $c) {
                $lines[] = "\"{$c->name}\" is already linked to \"{$other->name}\"";
            }
        }

        return 'A course can only be linked to one active template at a time — remove it there first: ' . implode(', ', $lines) . '.';
    }

    /**
     * Requirement #4 — resolves a typed prefix (e.g. "STB-NW") to its
     * certificate_serial_prefixes row, reusing the existing row (and
     * therefore its running counter) if that prefix already exists,
     * otherwise creating a fresh one starting at 0.
     */
    private function resolveSerialPrefix(string $prefix): int
    {
        $prefix = strtoupper(trim($prefix));

        return CertificateSerialPrefix::firstOrCreate(
            ['prefix' => $prefix],
            ['last_number' => 0]
        )->id;
    }

    private function storeUploadedFile($file, string $subfolder): string
    {
        $extension = $file->getClientOriginalExtension() ?: $file->extension();
        $path = "certificate-templates/{$subfolder}/" . Str::uuid() . '.' . $extension;
        Storage::disk('public')->put($path, file_get_contents($file->getRealPath()));

        return $path;
    }
}