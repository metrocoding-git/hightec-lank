<?php

namespace App\Http\Controllers;

use App\Models\AssessmentTemplate;
use App\Models\AssessmentTemplateSubTitle;
use App\Models\AssessmentTemplateTitle;
use App\Models\Course;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AssessmentTemplateController extends Controller
{
    public function index()
    {
        $courses = Course::orderBy('name')->get(['id', 'name']);

        return view('pages.administration.assessment.template-index', compact('courses'));
    }

    /**
     * AJAX: templates for one course, for the builder + list. ACTIVE ONLY —
     * a deleted (disabled) template must never show up again, per request.
     */
    public function courseData($courseId)
    {
        $templates = AssessmentTemplate::with(['titles.subTitles', 'createUser:id,name'])
            ->where('course_id', $courseId)
            ->active()
            ->orderByDesc('id')
            ->get();

        return response()->json(['templates' => $templates]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'module_name' => 'required|string|max:255',
            'ncs_code' => 'nullable|string|max:100',
            'titles' => 'required|array|min:1',
            'titles.*.title' => 'required|string|max:255',
            'titles.*.max_marks' => 'required|integer|min:0',
            'titles.*.sub_titles' => 'required|array|min:1',
            'titles.*.sub_titles.*.sub_title' => 'required|string|max:255',
        ]);

        DB::transaction(function () use ($validated) {
            $template = AssessmentTemplate::create([
                'course_id' => $validated['course_id'],
                'module_name' => $validated['module_name'],
                'ncs_code' => $validated['ncs_code'] ?? null,
                'is_active' => true,
                'created_by' => Auth::id(),
            ]);

            foreach ($validated['titles'] as $titleIndex => $titleData) {
                $title = $template->titles()->create([
                    'title' => $titleData['title'],
                    'max_marks' => $titleData['max_marks'],
                    'sort_order' => $titleIndex,
                ]);

                foreach ($titleData['sub_titles'] as $subIndex => $subData) {
                    $title->subTitles()->create([
                        'sub_title' => $subData['sub_title'],
                        'sort_order' => $subIndex,
                    ]);
                }
            }
        });

        return response()->json(['success' => true, 'message' => 'Assessment template created successfully.']);
    }

    public function update(Request $request, $id)
    {
        $template = AssessmentTemplate::findOrFail($id);

        $validated = $request->validate([
            'module_name' => 'required|string|max:255',
            'ncs_code' => 'nullable|string|max:100',
            'titles' => 'required|array|min:1',
            'titles.*.id' => 'nullable|exists:assessment_template_titles,id',
            'titles.*.title' => 'required|string|max:255',
            'titles.*.max_marks' => 'required|integer|min:0',
            'titles.*.sub_titles' => 'required|array|min:1',
            'titles.*.sub_titles.*.id' => 'nullable|exists:assessment_template_sub_titles,id',
            'titles.*.sub_titles.*.sub_title' => 'required|string|max:255',
        ]);

        $locked = [];

        DB::transaction(function () use ($template, $validated, &$locked) {
            $template->update([
                'module_name' => $validated['module_name'],
                'ncs_code' => $validated['ncs_code'] ?? null,
                'updated_by' => Auth::id(),
            ]);

            $keptTitleIds = [];

            foreach ($validated['titles'] as $titleIndex => $titleData) {
                if (!empty($titleData['id'])) {
                    $title = AssessmentTemplateTitle::where('assessment_template_id', $template->id)
                        ->findOrFail($titleData['id']);
                    $title->update([
                        'title' => $titleData['title'],
                        'max_marks' => $titleData['max_marks'],
                        'sort_order' => $titleIndex,
                    ]);
                } else {
                    $title = $template->titles()->create([
                        'title' => $titleData['title'],
                        'max_marks' => $titleData['max_marks'],
                        'sort_order' => $titleIndex,
                    ]);
                }

                $keptTitleIds[] = $title->id;
                $keptSubTitleIds = [];

                foreach ($titleData['sub_titles'] as $subIndex => $subData) {
                    if (!empty($subData['id'])) {
                        $sub = AssessmentTemplateSubTitle::where('assessment_template_title_id', $title->id)
                            ->findOrFail($subData['id']);
                        $sub->update([
                            'sub_title' => $subData['sub_title'],
                            'sort_order' => $subIndex,
                        ]);
                    } else {
                        $sub = $title->subTitles()->create([
                            'sub_title' => $subData['sub_title'],
                            'sort_order' => $subIndex,
                        ]);
                    }

                    $keptSubTitleIds[] = $sub->id;
                }

                // Sub-titles dropped from the form get removed — unless a
                // student has already been marked against one, in which
                // case we keep it rather than lose that history.
                $removable = $title->subTitles()->whereNotIn('id', $keptSubTitleIds)->get();
                foreach ($removable as $sub) {
                    try {
                        $sub->delete();
                    } catch (QueryException $e) {
                        $locked[] = $sub->sub_title;
                    }
                }
            }

            $removableTitles = $template->titles()->whereNotIn('id', $keptTitleIds)->get();
            foreach ($removableTitles as $title) {
                try {
                    $title->delete();
                } catch (QueryException $e) {
                    $locked[] = $title->title;
                }
            }
        });

        $message = 'Assessment template updated successfully.';
        if (!empty($locked)) {
            $message .= ' (Kept — already used in saved assessments: ' . implode(', ', $locked) . ')';
        }

        return response()->json(['success' => true, 'message' => $message]);
    }

    /**
     * "Delete" — one-way only, matches how Courses/Intake/Subjects/Student
     * Category already work in this app: it flips is_active to false and
     * never back. There is deliberately no "Enable" anywhere in the UI —
     * a deleted template just stops appearing in courseData() above.
     * Still a soft flag under the hood (never a hard delete), because a
     * template already used in saved student_assessments must never be
     * destroyable.
     */
    public function destroy($id)
    {
        $template = AssessmentTemplate::findOrFail($id);
        $template->update([
            'is_active' => false,
            'updated_by' => Auth::id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Template deleted.',
        ]);
    }
}