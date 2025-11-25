<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Course;
use App\Models\PaidCourse;
use App\Models\ItemSell;
use App\Models\ItemIssueBatch;
use App\Models\Fee_invoices;
use App\Models\Invoices_payments;
use App\Models\ItemSellBatch;
use App\Models\ItemSellItem;
use Illuminate\Http\Request;

class ReportController extends Controller
{


    public function sellItems()
    {
        $itemSells = ItemSellItem::with(['item', 'itemSellBatch.student', 'itemSellBatch.user'])->get();
        return view('pages.reports.sell-items', compact('itemSells'));
    }

    public function issueItems()
    {
        $itemIssues = ItemIssueBatch::with(['student', 'staff', 'items.item'])->get();
        return view('pages.reports.issue-items', compact('itemIssues'));
    }

    public function studentsPaid(Request $request)
{
    $branches = Branch::all();

    $query = Invoices_payments::with([
        'invoice',
        'invoice.branch',
        'invoice.invoice_item',
        'invoice.invoice_item.fee_type',
        'invoice.paidCourses',
        'invoice.paidCourses.course'
    ]);

    // ----- DATE RANGE FILTER -----
    if ($request->date_range) {
        $dates = explode(' to ', $request->date_range);

        $from = $dates[0] ?? null;
        $to = $dates[1] ?? null;

        if ($from) {
            $query->whereDate('created_at', '>=', $from);
        }
        if ($to) {
            $query->whereDate('created_at', '<=', $to);
        }
    }

    // ----- PAYMENT METHOD FILTER -----
    if ($request->payment_method) {
        $query->where('payment_method', $request->payment_method);
    }

    // ----- BRANCH FILTER -----
    if ($request->branch_id) {
        $query->whereHas('invoice', function ($q) use ($request) {
            $q->where('branch_id', $request->branch_id);
        });
    }

    // Results
    $feeInvoices = $query->orderBy('created_at', 'DESC')->get();

    return view('pages.reports.students-paid', compact('feeInvoices', 'branches'));
}

    public function coursePaid()
    {
        $paidCourses = PaidCourse::with(relations: ['course', 'invoice.student'])->get();
        return view('pages.reports.course-paid', compact('paidCourses'));
    }
}
