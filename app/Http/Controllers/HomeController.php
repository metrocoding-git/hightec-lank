<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Content;
use App\CheckSection;
use App\Models\Course;
use App\Models\Intake;
use App\Models\SmToDo;
use App\Models\SmClass;
use App\Models\SmEvent;
use App\Models\Student;
use App\Models\SmSchool;
use App\Models\FeeInvoice;
use App\Models\SmHoliday;
use App\Models\SmSection;
use App\Models\SmStudent;
use App\Models\SmUserLog;
use App\Models\VideoList;
use App\Models\YearCheck;
use App\InfixModuleManager;
use App\Models\BankAccount;
use App\Models\SmAddIncome;
use Illuminate\Support\Str;
use App\Models\SmAddExpense;
use Illuminate\Http\Request;
use App\Models\SmNoticeBoard;
use App\Models\GlobalVariable;
use App\Models\InventoryItem;
use App\Models\SmAcademicYear;
use App\Models\SmClassSection;
use App\Models\ShareContentList;
use App\Models\SmCalendarSetting;
use App\Models\SmGeneralSettings;
use App\Models\DueFeesLoginPrevent;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Session;
use Modules\Saas\Entities\SmPackagePlan;
use Modules\RolePermission\Entities\InfixRole;
use Modules\Wallet\Entities\WalletTransaction;
use Modules\Saas\Entities\SmSubscriptionPayment;
use App\Models\Branch; // Keep this single import for Branch
use App\Models\FeeInvoices;
use App\Models\InvoicesPayments;
use App\Models\StudentVerification;
use App\Models\Category;
use App\Models\ItemSellBatch;
use App\Models\ItemIssueBatch;

class HomeController extends Controller
{


    // for display dashboard
    public function dashboard()
    {
        $user = Auth::user();

        // PERMISSIONS (Section 40): the dashboard has only ever had a single
        // window-level key ('dashboard' — see config/permissions.php), so
        // there is no dedicated button-level key per widget to gate on.
        // Rather than invent new keys (against this project's standing
        // rule), each widget below is gated on the existing permission for
        // the feature area its data actually comes from — e.g. the Students
        // card uses the same 'add-student.index' key that guards the actual
        // student list page. Anyone who can't see a feature's own page won't
        // see its dashboard summary either.
        $canViewStudents = Auth::user()->hasPermission('add-student.index');
        $canViewStaff = Auth::user()->hasPermission('role-management.user-list');
        $canViewFinance = Auth::user()->hasPermission('fees.fee-invoice');
        $canViewAcademic = Auth::user()->hasPermission('course.index') || Auth::user()->hasPermission('intake.index');
        // Section 42: same reuse-the-existing-key approach, extended to the
        // Inventory/Account section of the sidebar.
        $canViewInventory = Auth::user()->hasPermission('item-list')
            || Auth::user()->hasPermission('item-sell-list')
            || Auth::user()->hasPermission('item-issue-list');
        // Section 43: "Amount only display all report access pertions only
        // count display no problem" — every currency figure on the dashboard
        // (totals, chart values, the Price column) is now gated on having
        // access to at least one of the Reports section's own permission
        // keys, same OR-list side-menu.blade.php uses to decide whether to
        // show the Reports menu item at all. Counts (student/staff/item
        // counts, etc.) are unaffected and keep using their existing gates.
        $canViewAmounts = Auth::user()->hasPermission('reports.sell-items')
            || Auth::user()->hasPermission('reports.issue-items')
            || Auth::user()->hasPermission('reports.students-paid')
            || Auth::user()->hasPermission('reports.course-paid')
            || Auth::user()->hasPermission('reports.course-dates-payments')
            || Auth::user()->hasPermission('reports.course-intake-payments')
            || Auth::user()->hasPermission('reports.students-intake-balance')
            || Auth::user()->hasPermission('reports.change-course')
            || Auth::user()->hasPermission('reports.invoice-payments')
            || Auth::user()->hasPermission('reports.food-payment')
            || Auth::user()->hasPermission('reports.student-attendance');

        // Fetch counts
        $studentsCount = Student::count();
        $teachersCount = User::whereHas('role', function ($q) {
            $q->where('role_name', 'Teacher');
        })->count();
        $labourCount = User::whereHas('role', function ($q) {
            $q->where('role_name', 'Labour');
        })->count();
        $adminCount = User::whereHas('role', function ($q) {
            $q->where('role_name', 'Admin');
        })->count();

        // Fetch financial data using existing models.
        // BUG FIX (Section 40): the old "Total Expenses" / "Total Profit"
        // cards always showed $0 expenses and profit == income, because no
        // expense model exists in this app at all — that wasn't a real
        // profit figure, it was income relabeled, which is misleading on a
        // dashboard admins make decisions from. Replaced with two numbers
        // that ARE real and already tracked: total invoiced vs. total
        // collected, so the outstanding balance is honest instead of implied
        // to be zero.
        $totalIncome = (float) FeeInvoices::sum('paid_amount');
        $totalInvoiced = (float) FeeInvoices::sum('total_amount');
        $totalOutstanding = max($totalInvoiced - $totalIncome, 0);

        // Invoice status breakdown (Section 42, fixed in Section 43): the
        // Section 42 version used a `status` column that doesn't exist on
        // fee_invoices (that P/PP/U/R code set belongs to ItemSellBatch, a
        // different table) — it looks like this got hand-edited afterwards
        // to `invoice_type` to stop a "column not found" error, but that's
        // not a real column either, and the P/PP/U/R labels never matched
        // what FeeInvoices actually stores. The real, confirmed column is
        // `payment_status`, set directly in ItemSellController to 'partial'
        // or 'full' on creation and 'returned' on a return; the 'not' value
        // comes from the payment_status filter dropdown in course-paid.blade
        // (@lang('fees.not_paid') => 'not'), so it's a real status this
        // column can hold even though this session hasn't seen it get set
        // that way directly.
        $statusLabels = ['not' => 'Not Paid', 'partial' => 'Partially Paid', 'full' => 'Fully Paid', 'returned' => 'Returned'];
        $invoiceStatusRaw = FeeInvoices::selectRaw('payment_status, count(*) as cnt')
            ->groupBy('payment_status')
            ->pluck('cnt', 'payment_status');
        $invoiceStatusBreakdown = collect($statusLabels)
            ->map(function ($label, $code) use ($invoiceStatusRaw) {
                return ['label' => $label, 'value' => (int) ($invoiceStatusRaw[$code] ?? 0)];
            })
            ->filter(fn($row) => $row['value'] > 0)
            ->values();
        $invoiceStatusTotal = $invoiceStatusBreakdown->sum('value');

        // Monthly income trend (last 6 months) for the income chart, built
        // from InvoicesPayments so it reflects actual payment activity over
        // time rather than a single lifetime total.
        $monthKeys = collect();
        for ($i = 5; $i >= 0; $i--) {
            $monthKeys->push(now()->subMonths($i)->format('Y-m'));
        }
        $paymentsByMonth = InvoicesPayments::where('created_at', '>=', now()->subMonths(5)->startOfMonth())
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as ym, SUM(amount) as total")
            ->groupBy('ym')
            ->pluck('total', 'ym');
        $incomeTrend = $monthKeys->map(function ($ym) use ($paymentsByMonth) {
            return [
                'month' => Carbon::createFromFormat('Y-m', $ym)->format('M Y'),
                'income' => (float) ($paymentsByMonth[$ym] ?? 0),
            ];
        })->values();

        // Fetch branches with intakes and courses
        $branches = Branch::with(['intakes.courses'])->get();

        // Academic overview (Section 42): the old dashboard listed every
        // branch -> intake -> course as nested cards, which turns into a
        // very long scroll once there's more than a handful of branches —
        // flagged as "smart method easily analized" wanted instead. Replaced
        // with three at-a-glance totals plus a flat, searchable/sortable
        // table (DataTables — already used this way everywhere else in the
        // app) instead of a deep nested list.
        $totalBranchesCount = $branches->count();
        $totalIntakesCount = Intake::count();
        $totalCoursesCount = Course::count();
        $academicRows = collect();
        foreach ($branches as $branch) {
            foreach ($branch->intakes as $intake) {
                foreach ($intake->courses as $course) {
                    $academicRows->push([
                        'branch' => $branch->name,
                        'intake' => $intake->name,
                        'course' => $course->name,
                        'price' => (float) $course->price,
                    ]);
                }
            }
        }

        // Students per branch, for the enrollment-by-branch chart. One
        // grouped query instead of a per-branch count to avoid N+1.
        $studentsPerBranchRaw = Student::selectRaw('branch_id, count(*) as cnt')
            ->groupBy('branch_id')
            ->pluck('cnt', 'branch_id');
        $studentsPerBranch = $branches->map(function ($branch) use ($studentsPerBranchRaw) {
            return [
                'branch' => $branch->name,
                'count' => (int) ($studentsPerBranchRaw[$branch->id] ?? 0),
            ];
        })->values();

        // Staff composition (Teachers / Labour / Admin), for the staff donut.
        // Built already in the {label, value} shape Morris.Donut expects, and
        // passed through @json() in the view rather than interpolated by
        // hand into a JS array literal, so labels are properly JSON-escaped.
        $staffComposition = [
            ['label' => 'Teachers', 'value' => $teachersCount],
            ['label' => 'Labour', 'value' => $labourCount],
            ['label' => 'Admin', 'value' => $adminCount],
        ];
        $staffTotal = $teachersCount + $labourCount + $adminCount;

        // Inventory / Account snapshot (Section 42) — covers the sidebar's
        // Inventory section, which the dashboard had no presence for at all
        // before. Only using models already confirmed elsewhere in this
        // codebase (InventoryItem, Category, ItemSellBatch, ItemIssueBatch);
        // a Supplier count was left out because no Supplier model class is
        // confirmed anywhere in the code I have access to, and guessing one
        // risks a hard crash on this page if the class name is wrong.
        $totalInventoryItems = InventoryItem::count();
        $totalItemCategories = Category::count();
        $itemSellCountThisMonth = ItemSellBatch::whereBetween('sell_date', [now()->startOfMonth(), now()->endOfMonth()])->count();
        $itemIssueCountThisMonth = ItemIssueBatch::whereBetween('issue_date', [now()->startOfMonth(), now()->endOfMonth()])->count();

        $itemSellByMonth = ItemSellBatch::where('sell_date', '>=', now()->subMonths(5)->startOfMonth())
            ->selectRaw("DATE_FORMAT(sell_date, '%Y-%m') as ym, SUM(total_sell_price) as total")
            ->groupBy('ym')
            ->pluck('total', 'ym');
        $itemSellTrend = $monthKeys->map(function ($ym) use ($itemSellByMonth) {
            return [
                'month' => Carbon::createFromFormat('Y-m', $ym)->format('M Y'),
                'revenue' => (float) ($itemSellByMonth[$ym] ?? 0),
            ];
        })->values();

        return view('pages.dashboard.dashboard', compact(
            'user',
            'canViewStudents',
            'canViewStaff',
            'canViewFinance',
            'canViewAcademic',
            'canViewInventory',
            'canViewAmounts',
            'studentsCount',
            'teachersCount',
            'labourCount',
            'adminCount',
            'totalIncome',
            'totalInvoiced',
            'totalOutstanding',
            'incomeTrend',
            'invoiceStatusBreakdown',
            'invoiceStatusTotal',
            'branches',
            'totalBranchesCount',
            'totalIntakesCount',
            'totalCoursesCount',
            'academicRows',
            'studentsPerBranch',
            'staffComposition',
            'staffTotal',
            'totalInventoryItems',
            'totalItemCategories',
            'itemSellCountThisMonth',
            'itemIssueCountThisMonth',
            'itemSellTrend'
        ));
    }



}