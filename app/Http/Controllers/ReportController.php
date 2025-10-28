<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\PaidCourse;
use App\Models\ItemSell;
use App\Models\ItemIssueBatch;
use App\Models\Fee_invoices;
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

    public function studentsPaid()
    {
        $feeInvoices = Fee_invoices::with(['student'])->where('payment_status', '!=', 'not')->get();
        return view('pages.reports.students-paid', compact('feeInvoices'));
    }

    public function coursePaid()
    {
        $paidCourses = PaidCourse::with(['course', 'invoice.student'])->get();
        return view('pages.reports.course-paid', compact('paidCourses'));
    }
}
