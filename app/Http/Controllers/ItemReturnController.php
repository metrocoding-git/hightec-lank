<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\ItemIssueBatch;
use App\Models\ItemIssueItem;
use App\Models\ItemInventory;
use App\Models\Intake;
use App\Models\Course;
use App\Models\StudentCourse;
use Carbon\Carbon;

class ItemReturnController extends Controller
{
    public function returnItem(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:item_issue_batches,id',
        ]);

        try {
            DB::beginTransaction();

            $batch = ItemIssueBatch::findOrFail($request->id);

            $intake = Intake::find($batch->intake_id);
            $course = Course::find($batch->course_id);
            $expireDate = Carbon::parse($intake->start_date)->addDays((int)$course->expiration_date);

            $canAdd = now()->lte($expireDate);
            if ($canAdd) {
                // Get all items in this batch before updating status.
                $batchItems = ItemIssueItem::where('batch_id', $batch->id)->get();

                // Track inventory changes for each item return (increase inventory).
                foreach ($batchItems as $batchItem) {
                    $currentBalance = ItemInventory::getCurrentBalance($batchItem->item_id, $batch->branch_id);
                    $newBalance = $currentBalance + $batchItem->quantity;

                    ItemInventory::create([
                        'item_id' => $batchItem->item_id,
                        'transaction_type' => 'issue_return',
                        'branch_id' => $batch->branch_id,
                        'transaction_id' => $batch->id,
                        'quantity' => $batchItem->quantity,
                        'current_balance' => $newBalance,
                        'reference_no' => 'BATCH-' . $batch->id,
                        'transaction_date' => now()->toDateString(),
                        'description' => 'Items returned from batch',
                    ]);
                }

                $batch->issue_status = 'R';
                $batch->save();

                $batchFind = ItemIssueBatch::where('branch_id', $batch->branch_id)
                    ->where('student_id', $batch->student_id)
                    ->where('intake_id', $batch->intake_id)
                    ->where('course_id', $batch->course_id)
                    ->where('issue_status', 'I')
                    ->first();

                if (!$batchFind) {
                    $student_course = StudentCourse::where('student_id', $batch->student_id)
                        ->where('course_id', $batch->course_id)
                        ->where('intake_id', $batch->intake_id)
                        ->where('branch_id', $batch->branch_id)
                        ->first();

                    if ($student_course) {
                        $student_course->status = 'return';
                        $student_course->save();
                    }
                }

                DB::commit();

                return redirect()->route('item-issue-list')->with('success', 'Batch returned successfully.');
            }

            return redirect()->back()->with('error', 'Course Period has expired');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Operation Failed: ' . $e->getMessage());
        }
    }
}
