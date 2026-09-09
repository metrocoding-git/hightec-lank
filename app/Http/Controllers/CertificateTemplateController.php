<?php

namespace App\Http\Controllers;

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
        $templates = CertificateTemplate::with('course')
            ->orderByDesc('id')
            ->get();

        $courseOptions = Course::orderBy('name')->get(['id', 'name']);

        return view('pages.administration.certificate.template.index', compact('templates', 'courseOptions'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'               => 'required|string|max:255',
            'course_id'          => 'nullable|integer|exists:courses,id',
            'course_name_line1'  => 'required|string|max:255',
            'course_name_line2'  => 'nullable|string|max:255',
            'duration'           => 'nullable|string|max:255',
            'back_side_content'  => 'nullable|string',
            'bottom_image'       => 'nullable|image|mimes:png,jpg,jpeg|max:4096',
        ]);

        $data['created_by'] = Auth::id();
        $data['is_active'] = true;

        if ($request->hasFile('bottom_image')) {
            $data['bottom_image_path'] = $this->storeBottomImage($request->file('bottom_image'));
        }

        $template = CertificateTemplate::create($data);

        return response()->json([
            'status'   => 'ok',
            'message'  => 'Certificate template created.',
            'template' => $template,
        ]);
    }

    public function update(Request $request, $id)
    {
        $template = CertificateTemplate::findOrFail($id);

        $data = $request->validate([
            'name'               => 'required|string|max:255',
            'course_id'          => 'nullable|integer|exists:courses,id',
            'course_name_line1'  => 'required|string|max:255',
            'course_name_line2'  => 'nullable|string|max:255',
            'duration'           => 'nullable|string|max:255',
            'back_side_content'  => 'nullable|string',
            'bottom_image'       => 'nullable|image|mimes:png,jpg,jpeg|max:4096',
        ]);

        if ($request->hasFile('bottom_image')) {
            if ($template->bottom_image_path) {
                Storage::disk('public')->delete($template->bottom_image_path);
            }
            $data['bottom_image_path'] = $this->storeBottomImage($request->file('bottom_image'));
        }

        $template->update($data);

        return response()->json([
            'status'   => 'ok',
            'message'  => 'Certificate template updated.',
            'template' => $template->fresh(),
        ]);
    }

    /**
     * One-way soft-disable — same convention as everywhere else in this
     * app. A template already used by generated certificates stays
     * attached to them (certificates.certificate_template_id is not
     * touched here), it just stops appearing as a choice for NEW
     * certificates going forward (see the certificate-index.blade.php
     * Generate modal, which only lists ->active() templates).
     */
    public function destroy($id)
    {
        $template = CertificateTemplate::findOrFail($id);
        $template->is_active = false;
        $template->save();

        return response()->json([
            'status'  => 'ok',
            'message' => 'Certificate template disabled.',
        ]);
    }

    private function storeBottomImage($file): string
    {
        $extension = $file->getClientOriginalExtension() ?: 'png';
        $path = 'certificate-templates/bottom-images/' . Str::uuid() . '.' . $extension;
        Storage::disk('public')->put($path, file_get_contents($file->getRealPath()));

        return $path;
    }
}