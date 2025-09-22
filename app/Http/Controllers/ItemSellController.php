<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Intake;
use App\Models\Inventory_item;
use App\Models\ItemSell;
use App\Models\Role;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ItemSellController extends Controller
{
    public function itemSell()
    {
        $items = Inventory_item::all();
        $classes = Course::all();
        $sections = Intake::all();
        $roles = Role::all();
        $students = Student::all();
        $users = User::all();
        return view('pages.account.inventory.item-sell', compact('items', 'classes', 'sections', 'roles', 'students', 'users'));
    }

    public function saveItemSellData(Request $request)
    {
        // Validate incoming data
        $request->validate([
            'payment_method' => 'required|string',
            'role_id' => 'required|integer|exists:roles,id',
            'sell_date' => 'required|string', // will parse m/d/Y -> Y-m-d
            'item_id' => 'required|array|min:1',
            'item_id.*' => 'required|integer|exists:inventory_items,id',
            'unit_price' => 'required|array|min:1',
            'unit_price.*' => 'required|numeric',
            'quantity' => 'required|array|min:1',
            'quantity.*' => 'required|numeric',
            'totalPaid' => 'nullable|numeric',
            'subTotal' => 'nullable|numeric',
            'totalDue' => 'nullable|numeric',
        ]);

        try {
            // Parse date from the UI (m/d/Y) to database format (Y-m-d)
            $sellDate = Carbon::createFromFormat('m/d/Y', $request->sell_date)->format('Y-m-d');
        } catch (\Throwable $e) {
            // Fallback: try to parse a standard Y-m-d or Y/m/d if user typed differently
            try {
                $sellDate = Carbon::parse($request->sell_date)->format('Y-m-d');
            } catch (\Throwable $e2) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid sell date format.',
                ], 422);
            }
        }

        try {
            // Create one row per item, duplicating header fields as per existing schema
            foreach ($request->item_id as $index => $itemId) {
                ItemSell::create([
                    'payment_method' => $request->payment_method,
                    'role_id' => $request->role_id,
                    'course_id' => $request->class ?? null, // Map 'class' to 'course_id'
                    'intake_id' => $request->section ?? null, // Map 'section' to 'intake_id'
                    'student_id' => $request->student ?? null, // Map 'student' to 'student_id'
                    'user_id' => $request->staff_id ?? null, // Map 'staff_id' to 'user_id'
                    'reference_no' => $request->reference_no,
                    'sell_date' => $sellDate,
                    'description' => $request->description,
                    'item_id' => $itemId,
                    'total_quantity' => $request->quantity[$index],
                    'sell_price' => $request->unit_price[$index],
                    'total_paid' => $request->totalPaid ?? 0,
                    'total_due' => $request->totalDue ?? 0,
                ]);
            }

            return response()->json([
                'success' => 'success',
                'message' => 'Item sold successfully!'
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to save item sell.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
