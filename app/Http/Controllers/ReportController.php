<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\PaidCourse;
use App\Models\ItemSell;
use App\Models\ItemIssueBatch;
use App\Models\Fee_invoices;
use App\Models\ItemSellBatch;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    

    public function sellItems()
    {
        $itemSells = ItemSellBatch::with(['item', 'student', 'user'])->get();
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
}
