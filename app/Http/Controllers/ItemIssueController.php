<?php

namespace App\Http\Controllers;

use App\Models\ItemIssue;
use App\Models\Role;
use App\Models\Category;
use App\Models\Inventory_item;
use App\Models\Student;
use App\Models\User;
use App\Models\Course; // Assuming Course model for classes
use App\Models\Intake; // Assuming Intake model for sections
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\DB;

class ItemIssueController extends Controller
{
    public function index()
    {
        try {
            $roles = Role::where('status', 1)->get();
            // $itemCat = Category::get();
            $issuedItems = ItemIssue::where('active_status', 1)->get();
            $classes = Course::get(); // Fetch  classes
            $staffs = User::get(); // Fetch users
            $inventoryItems = Inventory_item::get();//item
            $intakes = Intake::get();//section
            $students = Student::get();//student
            $categories = Category::get();

            return view('pages.account.inventory.item-issue', compact('roles',  'issuedItems', 'classes', 'staffs','intakes','students','inventoryItems' ,'categories'));
        } catch (Exception $e) {
            return response()->json(['error' => 'Operation Failed: ' . $e->getMessage()], 500);
        }
    }

   
    }

