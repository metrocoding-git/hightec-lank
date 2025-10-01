<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('item_issues', function (Blueprint $table) {
            // Drop old foreign keys
            $table->dropForeign(['role_id']);
            $table->dropForeign(['class_id']);
            $table->dropForeign(['section_id']);

            // Drop the role_id column
            $table->dropColumn('role_id');

            // Rename columns
            $table->renameColumn('class_id', 'course_id');
            $table->renameColumn('section_id', 'intake_id');

            // Add new branch_id column
            $table->unsignedBigInteger('branch_id')->after('id');

            // Add new foreign keys with updated column names
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
            $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
            $table->foreign('intake_id')->references('id')->on('intakes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('item_issues', function (Blueprint $table) {
            // Drop new foreign keys
            $table->dropForeign(['branch_id']);
            $table->dropForeign(['course_id']);
            $table->dropForeign(['intake_id']);

            // Drop the branch_id column
            $table->dropColumn('branch_id');

            // Rename columns back to original names
            $table->renameColumn('course_id', 'class_id');
            $table->renameColumn('intake_id', 'section_id');

            // Add back the role_id column
            $table->unsignedBigInteger('role_id')->after('id');

            // Add original foreign keys
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
            $table->foreign('class_id')->references('id')->on('courses')->onDelete('cascade');
            $table->foreign('section_id')->references('id')->on('intakes')->onDelete('cascade');
        });
    }
};
