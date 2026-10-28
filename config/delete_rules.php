<?php

/*
|--------------------------------------------------------------------------
| What "Delete" really does in each section
|--------------------------------------------------------------------------
| Used by components/partials/permanent-delete-note.blade.php to show a
| warning on every page whose Delete removes data permanently, and as the
| source for the "Delete behaviour" PDF.
|
|   permanent   : true  = row is removed from the database (cannot be undone)
|   also_deleted: removed automatically together with it (database cascade)
|   affected    : other records that stay but lose their link / show blank
|   safe_when   : deleting does no harm
|   risky_when  : deleting damages other data
|   guard       : protection already in the code
*/

return [

    'course' => [
        'name' => 'Courses',
        'menu' => 'Academic › Courses',
        'permanent' => true,
        'action' => 'Deletes the course.',
        'also_deleted' => [
            'ALL attendance of this course — Daily, Theory and Practical (scan attendance)',
            'Assessment templates of the course and ALL marks entered with them',
            'Course ↔ intake links, certificate-template links and staff course links',
        ],
        'affected' => [
            'Student Intake & Courses enrolments stay but show no course',
            'Course invoices / paid courses stay but show no course name (Collection, Course Paid reports)',
            'Subject assignments, item issues and reports for this course lose the course name',
        ],
        'safe_when' => 'The course was just created: no students, no invoices, no attendance, no assessments.',
        'risky_when' => 'Any student is enrolled, has paid, or has attendance/marks. If the course has food payments the database refuses the delete and an error is shown.',
        'guard' => 'None — no check before deleting.',
    ],

    'intake' => [
        'name' => 'Intakes',
        'menu' => 'Academic › Intake',
        'permanent' => true,
        'action' => 'Deletes the intake.',
        'also_deleted' => [
            'ALL attendance recorded under this intake (Daily, Theory, Practical)',
            'The list of courses linked to this intake',
        ],
        'affected' => [
            'Student enrolments in this intake stay but show no intake / start date (certificate start date is lost)',
            'Subject assignments and item issues for this intake lose the intake',
        ],
        'safe_when' => 'No students were added to the intake and no subjects were assigned.',
        'risky_when' => 'Students are enrolled — their attendance is wiped and certificates can no longer find the course start date.',
        'guard' => 'None — no check before deleting.',
    ],

    'subject' => [
        'name' => 'Subjects',
        'menu' => 'Academic › Subjects',
        'permanent' => true,
        'action' => 'Deletes the subject.',
        'also_deleted' => [],
        'affected' => [
            'Past theory/practical attendance of this subject loses the subject and then looks like Daily Attendance',
        ],
        'safe_when' => 'The subject was never assigned and never used for attendance.',
        'risky_when' => 'The subject was used before (even if its assignment was later removed).',
        'guard' => 'Delete is blocked while the subject is assigned to any course/intake.',
    ],

    'assign_subject' => [
        'name' => 'Assign Subject',
        'menu' => 'Academic › Assign Subject',
        'permanent' => true,
        'action' => 'Deletes ALL subjects assigned to that intake + course (the whole group, not one row).',
        'also_deleted' => [],
        'affected' => [
            'Scan Attendance stops offering these subjects for that intake/course',
            'Practical Card, certificate Theory/Practical counts and the NVQ report show 0/0 for those students',
            'Attendance already recorded stays, but is no longer counted',
        ],
        'safe_when' => 'Attendance has not started for that intake/course.',
        'risky_when' => 'Students of that intake/course are already attending.',
        'guard' => 'None — no check before deleting.',
    ],

    'lesson' => [
        'name' => 'Lessons',
        'menu' => 'Lesson Plan › Lesson',
        'permanent' => true,
        'action' => 'Deletes ALL lessons of the same intake + course + subject.',
        'also_deleted' => [],
        'affected' => ['Topics under these lessons stay but lose their lesson'],
        'safe_when' => 'No topics were added under these lessons.',
        'risky_when' => 'Topics exist under them.',
        'guard' => 'None.',
    ],

    'topic' => [
        'name' => 'Topics',
        'menu' => 'Lesson Plan › Topic',
        'permanent' => true,
        'action' => 'Deletes ALL topics of the same lesson (intake + course + subject + lesson).',
        'also_deleted' => [],
        'affected' => [],
        'safe_when' => 'Always — nothing else uses topics.',
        'risky_when' => '—',
        'guard' => 'None.',
    ],

    'content_type' => [
        'name' => 'Content Types',
        'menu' => 'Download Center › Content Type',
        'permanent' => true,
        'action' => 'Deletes the content type.',
        'also_deleted' => [],
        'affected' => ['Contents of this type stay but show no type'],
        'safe_when' => 'No content uses this type.',
        'risky_when' => 'Contents are using it.',
        'guard' => 'None.',
    ],

    'content' => [
        'name' => 'Content List',
        'menu' => 'Download Center › Content List',
        'permanent' => true,
        'action' => 'Deletes the content AND its uploaded file.',
        'also_deleted' => ['The uploaded file on the server'],
        'affected' => ['Shared content lists that included it no longer show it'],
        'safe_when' => 'The content is not shared with anyone.',
        'risky_when' => 'It is shared — students/staff lose access to the file.',
        'guard' => 'None.',
    ],

    'shared_content' => [
        'name' => 'Shared Content List',
        'menu' => 'Download Center › Shared Content List',
        'permanent' => true,
        'action' => 'Deletes the share (who it was shared with). The content itself is kept.',
        'also_deleted' => ['Links to the shared contents, users and courses'],
        'affected' => [],
        'safe_when' => 'Always — only the sharing is removed.',
        'risky_when' => '—',
        'guard' => 'None.',
    ],

    'video' => [
        'name' => 'Video List',
        'menu' => 'Download Center › Video List',
        'permanent' => true,
        'action' => 'Deletes the video entry.',
        'also_deleted' => [],
        'affected' => ['Courses it was linked to no longer show it'],
        'safe_when' => 'Always — nothing else depends on it.',
        'risky_when' => '—',
        'guard' => 'None.',
    ],

    'student_category' => [
        'name' => 'Student Category',
        'menu' => 'Student Info › Student Category',
        'permanent' => true,
        'action' => 'Deletes the category.',
        'also_deleted' => [],
        'affected' => [
            'Students, enrolments, invoices, item issues and item sells keep pointing to it and show the category as blank',
            'Category columns in Collection and other reports become blank for those records',
        ],
        'safe_when' => 'No student or invoice uses the category.',
        'risky_when' => 'Any student has this category.',
        'guard' => 'None.',
    ],

    'fee_group' => [
        'name' => 'Invoice Item Group (Fee Group)',
        'menu' => 'Fees › Invoice Item Group',
        'permanent' => true,
        'action' => 'Deletes the fee group.',
        'also_deleted' => ['ALL fee types (invoice types) in this group'],
        'affected' => ['Invoices using those fee types lose the fee name (shows N/A in Collection report)'],
        'safe_when' => 'The group has no fee types, or its fee types were never invoiced.',
        'risky_when' => 'Any invoice was made with one of its fee types.',
        'guard' => 'None.',
    ],

    'fee_type' => [
        'name' => 'Invoice Type (Fee Type)',
        'menu' => 'Fees › Invoice Type',
        'permanent' => true,
        'action' => 'Deletes the fee type.',
        'also_deleted' => [],
        'affected' => ['Invoices/receipts that used it show no fee name (N/A) — amounts stay'],
        'safe_when' => 'It was never used on an invoice.',
        'risky_when' => 'Invoices were made with it.',
        'guard' => 'None.',
    ],

    'item_category' => [
        'name' => 'Item Category',
        'menu' => 'Inventory › Item Category',
        'permanent' => true,
        'action' => 'Deletes the item category.',
        'also_deleted' => [],
        'affected' => ['Items and item issues in this category show no category (stock report: "-")'],
        'safe_when' => 'No items are in the category.',
        'risky_when' => 'Items belong to it.',
        'guard' => 'None.',
    ],

    'item' => [
        'name' => 'Item List',
        'menu' => 'Inventory › Item List',
        'permanent' => true,
        'action' => 'Deletes the item.',
        'also_deleted' => [],
        'affected' => [
            'Stock, receive, issue and sell records stay but show no item name',
            'The item disappears from the Item Stock Balance report; old receipts/bills show blank',
        ],
        'safe_when' => 'The item was never received, issued or sold.',
        'risky_when' => 'It has any stock movement.',
        'guard' => 'None.',
    ],

    'supplier' => [
        'name' => 'Supplier',
        'menu' => 'Inventory › Supplier',
        'permanent' => true,
        'action' => 'Deletes the supplier.',
        'also_deleted' => [],
        'affected' => ['Item receive records show no supplier'],
        'safe_when' => 'No items were received from this supplier.',
        'risky_when' => 'Receives exist for this supplier.',
        'guard' => 'None.',
    ],

    'item_receive' => [
        'name' => 'Item Receive List',
        'menu' => 'Inventory › Item Receive List',
        'permanent' => true,
        'action' => 'Deletes the receive record.',
        'also_deleted' => [],
        'affected' => [
            'The stock that was added is NOT taken back — stock balance stays as if the items were received',
        ],
        'safe_when' => 'Never fully safe — stock is not corrected.',
        'risky_when' => 'Always: the stock balance no longer matches the receive records.',
        'guard' => 'None.',
    ],

    'role' => [
        'name' => 'Roles',
        'menu' => 'Role Management › Roles',
        'permanent' => true,
        'action' => 'Deletes the role.',
        'also_deleted' => [],
        'affected' => ['Every user with this role immediately loses access to all pages'],
        'safe_when' => 'No user has this role.',
        'risky_when' => 'Users are assigned to it.',
        'guard' => 'None.',
    ],

    'enrolment' => [
        'name' => 'Student Intake & Courses (remove enrolment)',
        'menu' => 'Student Info › Add Intake & Courses',
        'permanent' => true,
        'action' => 'Removes a PENDING enrolment.',
        'also_deleted' => [],
        'affected' => ['Invoices for the course are not touched'],
        'safe_when' => 'Status is Pending (only then is Delete offered), so no attendance exists yet.',
        'risky_when' => '—',
        'guard' => 'Only Pending enrolments; blocked when a certificate exists.',
    ],

    'assessment_undo' => [
        'name' => 'Final Assessment Marks (Undo)',
        'menu' => 'Student Info › Final Assessment Marks',
        'permanent' => true,
        'action' => 'Undo deletes the assessment.',
        'also_deleted' => ['All marks entered for that assessment'],
        'affected' => [],
        'safe_when' => 'Marks were entered wrongly and no certificate exists.',
        'risky_when' => '—',
        'guard' => 'Only the staff member who created it; blocked once a certificate is generated.',
    ],

];
