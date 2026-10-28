<?php


return [

    'categories' => [

        [
            'name' => 'Dashboard',
            'windows' => [
                [
                    'name' => 'Dashboard',
                    'window' => ['key' => 'dashboard', 'label' => 'View'],
                    'buttons' => [],
                ],
            ],
        ],

        [
            'name' => 'Academic',
            'windows' => [
                [
                    'name' => 'Courses',
                    'window' => ['key' => 'course.index', 'label' => 'View'],
                    'buttons' => [
                        ['key' => 'course.store', 'label' => 'Add'],
                        ['key' => 'course.update', 'label' => 'Edit', 'flag' => 'Also covers the course.edit GET form-load route — same button, no separate permission needed for that.'],
                        ['key' => 'course.destroy', 'label' => 'Delete', 'flag' => 'Replaces course.toggle-status per your confirmed decision — Activate/Deactivate is being removed from the UI, plain Delete restored. Code revert not done yet, needs the current controller/blade/routes pasted.'],
                    ],
                ],
                [
                    'name' => 'Intake',
                    'window' => ['key' => 'intake.index', 'label' => 'View'],
                    'buttons' => [
                        ['key' => 'intake.store', 'label' => 'Add'],
                        ['key' => 'intake.update', 'label' => 'Edit', 'flag' => 'Also covers intake.edit form-load.'],
                        ['key' => 'intake.destroy', 'label' => 'Delete', 'flag' => 'Replaces intake.toggle-status per your confirmed decision — same as Courses.'],
                    ],
                ],
                [
                    'name' => 'Subjects',
                    'window' => ['key' => 'subject.index', 'label' => 'View'],
                    'buttons' => [
                        ['key' => 'subject.store', 'label' => 'Add'],
                        ['key' => 'subject.update', 'label' => 'Edit', 'flag' => 'Also covers subject.edit form-load.'],
                        ['key' => 'subject.destroy', 'label' => 'Delete', 'flag' => 'Replaces subject.toggle-status per your confirmed decision — same as Courses. Note: the existing isAssigned() in-use guard (blocks Edit/Delete for subjects currently assigned) still applies and was never removed.'],
                    ],
                ],
                [
                    'name' => 'Assign Subject',
                    'window' => ['key' => 'assign-subject.index', 'label' => 'View'],
                    'buttons' => [
                        ['key' => 'assign-subject.store', 'label' => 'Add'],
                        ['key' => 'assign-subject.update', 'label' => 'Edit', 'flag' => 'Also covers assign-subject.edit form-load.'],
                        ['key' => 'assign-subject.destroy', 'label' => 'Delete', 'flag' => 'Replaces assign-subject.toggle-status per your confirmed decision — deletes the whole intake/course group at once, same blast radius as before Sections 2-6.'],
                    ],
                ],
                [
                    'name' => 'Assessment Template',
                    'window' => ['key' => 'assessment-template.index', 'label' => 'View'],
                    'buttons' => [
                        ['key' => 'assessment-template.store', 'label' => 'Add'],
                        ['key' => 'assessment-template.update', 'label' => 'Edit'],
                        ['key' => 'assessment-template.destroy', 'label' => 'Delete', 'flag' => 'Only ever soft-disables (is_active toggle) — there is no hard-delete route, by design, since a template already used in saved assessments must never be destroyable.'],
                    ],
                ],
                 [
                    'name' => 'Certificate Template',
                    'window' => ['key' => 'certificate-template.index', 'label' => 'View'],
                    'buttons' => [
                        ['key' => 'certificate-template.store', 'label' => 'Add'],
                        ['key' => 'certificate-template.update', 'label' => 'Edit'],
                        ['key' => 'certificate-template.destroy', 'label' => 'Delete', 'flag' => 'Only ever soft-disables (is_active toggle) — there is no hard-delete route, by design, since a template already used in saved assessments must never be destroyable.'],
                    ],
                ],
            ],
        ],

        [
            'name' => 'Lesson Plan',
            'windows' => [
                [
                    'name' => 'Lesson',
                    'window' => ['key' => 'lesson.index', 'label' => 'View'],
                    'buttons' => [
                        ['key' => 'lesson.store', 'label' => 'Add'],
                        ['key' => 'lesson.update', 'label' => 'Edit', 'flag' => 'Also covers lesson.edit form-load.'],
                        ['key' => 'lesson.destroy', 'label' => 'Delete'],
                    ],
                ],
                [
                    'name' => 'Topic',
                    'window' => ['key' => 'topic.index', 'label' => 'View'],
                    'buttons' => [
                        ['key' => 'topic.store', 'label' => 'Add'],
                        ['key' => 'topic.update', 'label' => 'Edit', 'flag' => 'Also covers topic.edit form-load.'],
                        ['key' => 'topic.destroy', 'label' => 'Delete'],
                    ],
                ],
                [
                    'name' => 'Topic Overview',
                    'window' => ['key' => 'topic-overview.index', 'label' => 'View'],
                    'buttons' => [
                        ['key' => 'topic-overview.store', 'label' => 'Add', 'flag' => 'UPDATE: previously recommended leaving this ungated since you only said "view" — now added as a real permission per your "give all" instruction. Confirm you actually want an Add gate here.'],
                    ],
                ],
            ],
        ],

        [
            'name' => 'Download Center',
            'windows' => [
                [
                    'name' => 'Content-Type',
                    'window' => ['key' => 'content-type', 'label' => 'View'],
                    'buttons' => [
                        ['key' => 'content-type-store', 'label' => 'Add'],
                        ['key' => 'content-type-update', 'label' => 'Edit', 'flag' => 'Also covers content-type-edit form-load.'],
                        ['key' => 'content-type-delete', 'label' => 'Delete'],
                    ],
                ],
                [
                    'name' => 'Content List',
                    'window' => ['key' => 'download-center.content-list', 'label' => 'View'],
                    'buttons' => [
                        ['key' => 'download-center.content-store', 'label' => 'Add'],
                        ['key' => 'download-center.content-update', 'label' => 'Edit', 'flag' => 'Also covers download-center.content-edit form-load.'],
                        ['key' => 'download-center.content-delete', 'label' => 'Delete'],
                        ['key' => 'download-center.content-share-list-save', 'label' => 'Share'],
                        ['key' => 'download-center.content-generate-url-save', 'label' => 'Generate URL'],
                    ],
                ],
                [
                    'name' => 'Shared Content List',
                    'window' => ['key' => 'download-center.shared-content-list', 'label' => 'View'],
                    'buttons' => [
                        ['key' => 'download-center.shared-content-delete', 'label' => 'Delete'],
                        ['key' => 'download-center.shared-content-view', 'label' => 'View One', 'flag' => 'Viewing a single shared item — not in your original list, not excluded.'],
                    ],
                ],
                [
                    'name' => 'Video List',
                    'window' => ['key' => 'download-center.video-list', 'label' => 'View'],
                    'buttons' => [
                        ['key' => 'download-center.video-list-update', 'label' => 'Edit'],
                        ['key' => 'download-center.video-list-destroy', 'label' => 'Delete'],
                        ['key' => 'download-center.video-list-save', 'label' => 'Add', 'flag' => 'Not in your wanted list, but the upload route exists — confirm before seeding.'],
                    ],
                ],
            ],
        ],

        [
            'name' => 'Student Info',
            'windows' => [
                [
                    'name' => 'Student Category',
                    'window' => ['key' => 'student-category.index', 'label' => 'View'],
                    'buttons' => [
                        ['key' => 'student-category.store', 'label' => 'Add'],
                        ['key' => 'student-category.update', 'label' => 'Edit', 'flag' => 'Also covers student-category.edit form-load.'],
                        ['key' => 'student-category.destroy', 'label' => 'Delete', 'flag' => 'Replaces student-category.toggle-status per your confirmed decision — same as Courses.'],
                    ],
                ],
                [
                    'name' => 'Add Student',
                    'window' => ['key' => 'add-student.index', 'label' => 'View'],
                    'buttons' => [
                        ['key' => 'add-student.store', 'label' => 'Add', 'flag' => 'IMPORTANT — this was missing entirely before this pass. The window key add-student.index is only the GET page load; the actual POST that creates the student (add-student.store) never had its own permission anywhere, including your live 130-row table. Anyone who could reach the window could always submit it. Added now as its own gate.'],
                        ['key' => 'add-student.update', 'label' => 'Edit', 'flag' => 'Described as add-only originally, but this route exists under the same prefix — confirm it is reachable. Also covers add-student.edit form-load.'],
                        ['key' => 'add-student.destroy', 'label' => 'Delete', 'flag' => 'Described as add-only originally, but this route exists under the same prefix — confirm it is reachable.'],
                        ['key' => 'add-student.verify', 'label' => 'Verify', 'flag' => 'Described as add-only originally, but this route exists under the same prefix — confirm it is reachable.'],
                        ['key' => 'add-student.verify.submit', 'label' => 'Verify Submit', 'flag' => 'POST companion to add-student.verify — normally granted together with it. NOTE: as of this pass, this permission alone is no longer sufficient once the student has a certificate generated — see add-student.verify.submit.certified below.'],
                        ['key' => 'add-student.verify.submit.certified', 'label' => 'Verify Submit (After Certificate Generated)', 'flag' => "NEW — per your request: \"i want new permition 'after certificate genarate Verify Submit' then. if student genarated any one cerificate that stundet Verify Submit can be do that permition only.\" This is a SEPARATE, stricter permission from add-student.verify.submit above. The moment a student has any certificate generated (checked across ALL their courses/assessments via a new studentHasAnyCertificate() helper, not just one course), submitting Verify for that student requires THIS permission specifically — holding only add-student.verify.submit is no longer enough. Enforced in two places: verify.blade.php picks whichever of the two permissions applies before deciding whether to show the submit form, and verifySubmit() in the controller re-checks the same thing authoritatively (same defense-in-depth pattern already used elsewhere in this controller, e.g. studentCourseHasCertificate()). The verify page also shows a 'Certificate already generated — please check' warning banner to anyone who opens it for such a student, regardless of which permission they hold."],
                    ],
                ],
                [
                    'name' => 'Student List',
                    'window' => ['key' => 'student-list', 'label' => 'View'],
                    'buttons' => [
                        ['key' => 'student-card.show', 'label' => 'Generate Card', 'flag' => 'Currently withoutMiddleware(permission) — ungated for everyone until that exclusion is removed. Same for the 3 sibling routes: student-card.pdf, student-card.bulk-preview, student-card.bulk-pdf.'],
                        ['key' => 'student-list.verification-history', 'label' => 'Verification Check'],
                        ['key' => 'student.update-status', 'label' => 'Disable'],
                        ['key' => 'student-details', 'label' => 'View Student Details', 'flag' => 'Full student detail page (student-view/{id}) — not in your original list.'],
                        ['key' => 'student-list.search', 'label' => 'Search/Filter', 'flag' => 'Filter AJAX for this list, not excluded.'],
                        ['key' => 'student-practical-card.show', 'label' => 'Practical Card'],
                        ['key' => 'student-practical-card.index', 'label' => 'Attendance Card'],

                        


                    ],
                ],
                [
                    'name' => 'Disabled Students',
                    'window' => ['key' => 'disabled-students', 'label' => 'View'],
                    'buttons' => [
                        ['key' => 'disabled-students.search', 'label' => 'Search/Filter', 'flag' => 'Filter AJAX for this list, not excluded — not in your original "view only" description.'],
                    ],
                ],
                [
                    'name' => 'Scan Attendance',
                    'window' => ['key' => 'scan-attendance', 'label' => 'View', 'flag' => "RENAMED (Section 19) from 'student-attendance' to 'scan-attendance' — this key must equal the real route name for the plain (unparameterized) 'permission' middleware to match it, and the real route is named 'scan-attendance', not 'student-attendance'. This is what actually fixed the 403-for-everyone bug from Section 18; back to the same route-name-matching convention every other window/button in this file already uses, no more shared-permission-with-a-parameter trick needed here."],
                    'buttons' => [
                        ['key' => 'scan-attendance.course.scan', 'label' => 'Course Scan'],
                        ['key' => 'scan-attendance.subject.scan', 'label' => 'Subject Scan'],
                        ['key' => 'scan-attendance.practical.scan', 'label' => 'Practical Scan'],
                        ['key' => 'scan-attendance.undo', 'label' => 'Undo', 'flag' => 'Undo the last scan.'],
                    ],
                ],
                [
                    'name' => 'Student Add Intake & Courses',
                    'window' => ['key' => 'student.add-intake-with-courses.index', 'label' => 'View'],
                    'buttons' => [
                        ['key' => 'student.add-intake-with-courses.store', 'label' => 'Add'],
                        ['key' => 'student.add-intake.update-category', 'label' => 'Edit Category'],
                        ['key' => 'student.add-intake.update', 'label' => 'Edit', 'flag' => 'Was missing — a real, non-excluded PUT route separate from Edit Category. Confirm what this button actually does on the page vs. update-category.'],
                        ['key' => 'student.add-intake.destroy', 'label' => 'Delete'],
                        ['key' => 'student.add-intake.update-status', 'label' => 'Status Change (start/return/complete/restart)'],
                        ['key' => 'student.add-intake.search', 'label' => 'Search', 'flag' => 'Was missing — a real, non-excluded POST search route.'],
                        ['key' => 'student.add-intake-with-courses.change-course', 'label' => 'Change Course', 'flag' => 'Currently withoutMiddleware(permission) — remove that exclusion to actually gate this.'],
                    ],
                ],
                [
                    'name' => 'Final Assessment Marks',
                    'window' => ['key' => 'student-assessment.index', 'label' => 'View'],
                    'buttons' => [
                        ['key' => 'student-assessment.store', 'label' => 'Save Assessment'],
                        ['key' => 'student-assessment.show', 'label' => 'View One Assessment'],
                        ['key' => 'student-assessment.destroy', 'label' => 'Undo Assessment', 'flag' => 'One-way soft-delete — the controller itself also enforces "creator only, within 2 days" on top of this permission; this key just controls who can reach the Undo button at all.'],
                        ['key' => 'student-assessment.marks-popup', 'label' => 'Enter Marks (popup window)', 'flag' => 'This is really a second page load (the marks-entry popup opened after selecting a course), not a button — kept here since it lives under the same Final Assessment Marks feature, not as its own sidebar item.'],
                    ],
                ],
                [
                    'name' => 'Certificate Management',
                    'window' => ['key' => 'certificate.index', 'label' => 'View', 'flag' => 'NEW (Section 65) — the "Manage Certificates" window, opened via a link from Final Assessment Marks. Lists every PASSed assessment, certificate generated or not, with filters by signing stage and downloaded status.'],
                    'buttons' => [
                        ['key' => 'certificate.generate', 'label' => 'Generate Certificate(s)'],
                        ['key' => 'certificate.sign-conductor', 'label' => 'Sign — Assessment Conductor', 'flag' => 'Step 1 of signing. The controller rejects signing as Owner on a certificate the Conductor hasn\'t signed yet, regardless of what this permission allows.'],
                        ['key' => 'certificate.sign-owner', 'label' => 'Sign — Owner', 'flag' => 'Step 2 — finalizes the certificate and unlocks download. Replaces certificate.approve from the first version of this feature, which no longer exists.'],
                        ['key' => 'certificate.undo', 'label' => 'Undo', 'flag' => 'Undo the step by step process.'],
                    ],
                ],
            ],
        ],
        [
            'name' => 'Fees',
            'windows' => [
                [
                    'name' => 'Invoice Item Group',
                    'window' => ['key' => 'fee-group', 'label' => 'View'],
                    'buttons' => [
                        ['key' => 'fee-group-create', 'label' => 'Add'],
                        ['key' => 'fee-group-update', 'label' => 'Edit', 'flag' => 'Also covers fee-group-edit form-load.'],
                        ['key' => 'fee-group-delete', 'label' => 'Delete'],
                    ],
                ],
                [
                    'name' => 'Invoice Type',
                    'window' => ['key' => 'fees.fees-type', 'label' => 'View'],
                    'buttons' => [
                        ['key' => 'fees.fees-type-store', 'label' => 'Add'],
                        ['key' => 'fees.fees-type-update', 'label' => 'Edit', 'flag' => 'Also covers fees.fees-type-edit form-load.'],
                        ['key' => 'fees.fees-type-delete', 'label' => 'Delete'],
                    ],
                ],
                [
                    'name' => 'Registration & Other Payments',
                    'window' => ['key' => 'fees.add-invoice', 'label' => 'View / Add'],
                    'buttons' => [
                        ['key' => 'fees.invoice.store', 'label' => 'Add submit'],
                    ],
                ],
                [
                    'name' => 'Course, NVQ & Food Payment',
                    'window' => ['key' => 'fees.course-payment', 'label' => 'View / Add', 'flag' => 'PRODUCTION URL MISMATCH — see the top-level note in permission-naming-plan.md. No route in this file matches /student/fees/course-paid; the closest and only real route is this one, at /student/fees/course-payment.'],
                    'buttons' => [
                        ['key' => 'fees.course-payment.store', 'label' => 'Add submit'],
                    ],
                ],
                [
                    'name' => 'Invoice List',
                    'window' => ['key' => 'fees.fee-invoice', 'label' => 'View'],
                    'buttons' => [
                        ['key' => 'fees.add-payment', 'label' => 'Add Payment'],
                        ['key' => 'fees.mark-as-paid', 'label' => 'Mark as Paid'],
                        ['key' => 'fees.refund-payment', 'label' => 'Refund'],
                        ['key' => 'fees.invoice-item-print', 'label' => 'Print'],
                        ['key' => 'fees.invoice-print', 'label' => 'Re-print', 'flag' => 'Currently withoutMiddleware(permission) — inconsistent with invoice-item-print.'],
                        ['key' => 'fees.return-fee-data', 'label' => 'Return', 'flag' => 'Missed in an earlier pass — your wanted-actions list said "return" for Invoice List specifically (not just Cancelled Invoice), and this route (return-fee-data/{invoiceId}, in the same route group as Invoice List) is the match.'],
                        ['key' => 'fees.confirm-invoice-payment', 'label' => 'Confirm Payment', 'flag' => 'Not in your original list, not excluded.'],
                        ['key' => 'fees.payment-history', 'label' => 'Payment History', 'flag' => 'Not in your original list, not excluded.'],
                    ],
                ],
                [
                    'name' => 'Cancelled Invoice',
                    'window' => ['key' => 'fees.fee-return.search', 'label' => 'View', 'flag' => "Real index route is named '/' — recommend renaming to fees.fee-return.index before seeding."],
                    'buttons' => [
                        ['key' => 'return-fees.store', 'label' => 'Return', 'flag' => 'Not in your wanted list, but this is the actual return/cancel submit route.'],
                        ['key' => 'fees.fee-invoice-view', 'label' => 'View One Invoice', 'flag' => 'Not in your original list, not excluded.'],
                    ],
                ],
                [
                    'name' => 'Credit Invoice',
                    'window' => ['key' => 'invoice-pending-payments.index', 'label' => 'View'],
                    'buttons' => [
                        ['key' => 'invoice-payments.bulk-not-open', 'label' => 'Bulk Update', 'flag' => 'Two bulk routes exist (bulk-not-open, bulk-pending) — confirm if these should be one permission or two.'],
                        ['key' => 'invoice-payments.bulk-pending', 'label' => 'Bulk Update (pending)'],
                        ['key' => 'invoice-payments.search', 'label' => 'Search/Filter', 'flag' => 'Not excluded.'],
                    ],
                ],
                [
                    'name' => 'Payment for Credit Invoice',
                    'window' => ['key' => 'invoice-payments', 'label' => 'View'],
                    'buttons' => [
                        ['key' => 'invoice-payments.print', 'label' => 'Print'],
                        ['key' => 'invoice-payments.bulk-back', 'label' => 'Back'],
                    ],
                ],
            ],
        ],

        [
            'name' => 'Inventory',
            'windows' => [
                [
                    'name' => 'Item Category',
                    'window' => ['key' => 'item-category', 'label' => 'View'],
                    'buttons' => [
                        ['key' => 'item-category-store', 'label' => 'Add'],
                        ['key' => 'item-category-update', 'label' => 'Edit', 'flag' => 'Also covers item-category-edit form-load.'],
                        ['key' => 'delete-item-category', 'label' => 'Delete'],
                    ],
                ],
                [
                    'name' => 'Item List',
                    'window' => ['key' => 'item-list', 'label' => 'View'],
                    'buttons' => [
                        ['key' => 'item-store', 'label' => 'Add'],
                        ['key' => 'item-update', 'label' => 'Edit', 'flag' => 'Also covers item-edit form-load.'],
                        ['key' => 'item-delete', 'label' => 'Delete'],
                        ['key' => 'item-view', 'label' => 'View One Item', 'flag' => 'Not in your original list, not excluded (item-history, a sibling route, IS already excluded).'],
                    ],
                ],
                [
                    'name' => 'Supplier',
                    'window' => ['key' => 'supplier', 'label' => 'View'],
                    'buttons' => [
                        ['key' => 'supplier-store', 'label' => 'Add'],
                        ['key' => 'supplier-update', 'label' => 'Edit', 'flag' => 'Also covers supplier-edit form-load.'],
                        ['key' => 'supplier-delete', 'label' => 'Delete'],
                    ],
                ],
                [
                    'name' => 'Item Receive',
                    'window' => ['key' => 'item-receive', 'label' => 'View / Add'],
                    'buttons' => [
                        ['key' => 'save-item-receive-data', 'label' => 'Add submit'],
                        ['key' => 'item-receive.invoic-save', 'label' => 'Save Invoice Number', 'flag' => 'Not in your original list, not excluded.'],
                    ],
                ],
                [
                    'name' => 'Item Receive List',
                    'window' => ['key' => 'item-receive-list', 'label' => 'View'],
                    'buttons' => [
                        ['key' => 'item-receive.destroy', 'label' => 'Delete'],
                        ['key' => 'item-receive.view', 'label' => 'View One Receipt', 'flag' => 'Not in your original list, not excluded.'],
                    ],
                ],
                [
                    'name' => 'Item Issue',
                    'window' => ['key' => 'item-issue', 'label' => 'View / Add'],
                    'buttons' => [
                        ['key' => 'save-item-issue-data', 'label' => 'Add submit'],
                    ],
                ],
                [
                    'name' => 'Item Issue List',
                    'window' => ['key' => 'item-issue-list', 'label' => 'View'],
                    'buttons' => [
                        ['key' => 'return-item', 'label' => 'Return'],
                        ['key' => 'return-back-item', 'label' => 'Return Back'],
                    ],
                ],
                [
                    'name' => 'Item Sell',
                    'window' => ['key' => 'item-sell', 'label' => 'View / Add', 'flag' => 'Described as add-only originally, but 5 more routes (view/return/status/direct-return/return-view) live under this same group. Assigned to Item Sell List below instead, since they act on individual rows in that list — confirm that split is right.'],
                    'buttons' => [
                        ['key' => 'save-item-sell-data', 'label' => 'Add submit'],
                        ['key' => 'item-sell.invoic-save', 'label' => 'Save Invoice Number', 'flag' => 'Not in your original list, not excluded.'],
                    ],
                ],
                [
                    'name' => 'Item Sell List',
                    'window' => ['key' => 'item-sell-list', 'label' => 'View'],
                    'buttons' => [
                        ['key' => 'inventory.item-sell-bill', 'label' => 'Re-print'],
                        ['key' => 'view-item-sell', 'label' => 'View One Sale', 'flag' => 'Part of the 5-route Item Sell ambiguity — see the Item Sell window\'s flag above.'],
                        ['key' => 'return-item-sell', 'label' => 'Return'],
                        // ['key' => 'update-item-sell-status', 'label' => 'Update Status'],
                        // ['key' => 'direct-return-item-sell', 'label' => 'Direct Return'],
                        // ['key' => 'return-item-sell-view', 'label' => 'Return View'],
                    ],
                ],
            ],
        ],

        [
            'name' => 'Reports',
            'windows' => [
                [
                    'name' => 'Sell Items',
                    'window' => ['key' => 'reports.sell-items', 'label' => 'View'],
                    'buttons' => [
                        ['key' => 'reports.sell-items.export', 'label' => 'Export', 'flag' => 'NEW — added per your request to permission-gate report exports. This report already streams the full filtered result set via ->cursor() (Section 45), ignoring on-screen pagination; this key gates the Export CSV button/link.'],
                    ],
                ],
                [
                    'name' => 'Issue Items',
                    'window' => ['key' => 'reports.issue-items', 'label' => 'View'],
                    'buttons' => [
                        ['key' => 'reports.issue-items.export', 'label' => 'Export', 'flag' => 'NEW — see Sell Items flag above, same reasoning.'],
                    ],
                ],
                [
                    'name' => 'Collection Report',
                    'window' => ['key' => 'reports.students-paid', 'label' => 'View'],
                    'buttons' => [
                        ['key' => 'reports.students-paid.export', 'label' => 'Export', 'flag' => 'NEW — see Sell Items flag above, same reasoning.'],
                    ],
                ],
                [
                    'name' => 'Course Paid',
                    'window' => ['key' => 'reports.course-paid', 'label' => 'View'],
                    'buttons' => [
                        ['key' => 'reports.course-paid.export', 'label' => 'Export', 'flag' => 'NEW — see Sell Items flag above, same reasoning.'],
                    ],
                ],
                [
                    'name' => 'Course Started & Course Completed',
                    'window' => ['key' => 'reports.course-dates-payments', 'label' => 'View'],
                    'buttons' => [
                        ['key' => 'reports.course-dates-payments.export', 'label' => 'Export', 'flag' => 'NEW — see Sell Items flag above, same reasoning.'],
                    ],
                ],
                [
                    'name' => 'Course Started & Course Balance Status',
                    'window' => ['key' => 'reports.course-intake-payments', 'label' => 'View'],
                    'buttons' => [
                        ['key' => 'reports.course-intake-payments.export', 'label' => 'Export', 'flag' => 'NEW — see Sell Items flag above, same reasoning.'],
                    ],
                ],
                [
                    'name' => 'Course Fee Balances as Intake Wise',
                    'window' => ['key' => 'reports.students-intake-balance', 'label' => 'View'],
                    'buttons' => [
                        ['key' => 'reports.students-intake-balance.export', 'label' => 'Export', 'flag' => 'NEW — see Sell Items flag above, same reasoning.'],
                    ],
                ],
                [
                    'name' => 'Change Course',
                    'window' => ['key' => 'reports.change-course', 'label' => 'View'],
                    'buttons' => [
                        ['key' => 'reports.change-course.export', 'label' => 'Export', 'flag' => 'NEW — see Sell Items flag above, same reasoning.'],
                    ],
                ],
                [
                    'name' => 'Credit Invoice Payments',
                    'window' => ['key' => 'reports.invoice-payments', 'label' => 'View'],
                    'buttons' => [
                        ['key' => 'reports.invoice-payments.export', 'label' => 'Export'],
                    ],
                ],
                [
                    'name' => 'All Food Fee Details',
                    'window' => ['key' => 'reports.food-payment', 'label' => 'View'],
                    'buttons' => [
                        ['key' => 'reports.food-payment.export', 'label' => 'Export', 'flag' => 'NEW — see Sell Items flag above, same reasoning.'],
                    ],
                ],
                [
                    'name' => 'Student Attendance',
                    'window' => ['key' => 'reports.student-attendance', 'label' => 'View'],
                    'buttons' => [
                        ['key' => 'reports.student-attendance.export', 'label' => 'Export'],
                    ],
                ],
                [
                    'name' => 'NVQ Payment (Completion > 75%)',
                    'window' => ['key' => 'reports.nvq-payment', 'label' => 'View'],
                    'buttons' => [
                        ['key' => 'reports.nvq-payment.export', 'label' => 'Export'],
                    ],
                ],
                [
                    'name' => 'Item Stock Balance (Branch-wise)',
                    'window' => ['key' => 'reports.item-stock', 'label' => 'View'],
                    'buttons' => [
                        ['key' => 'reports.item-stock.export', 'label' => 'Export'],
                    ],
                ],
            ],
        ],

        [
            'name' => 'Leave',
            'windows' => [
                [
                    'name' => 'My Leaves (apply — sees own leaves only)',
                    'window' => ['key' => 'leave.my', 'label' => 'View'],
                    'buttons' => [
                        ['key' => 'leave.store', 'label' => 'Apply'],
                        ['key' => 'leave.cancel', 'label' => 'Cancel own pending'],
                    ],
                ],
                [
                    'name' => 'Leave Requests (review other staff)',
                    'window' => ['key' => 'leave.review-list', 'label' => 'View'],
                    'buttons' => [
                        ['key' => 'leave.review', 'label' => 'Accept / Not Accept'],
                    ],
                ],
            ],
        ],

        [
            'name' => 'Role Management',
            'windows' => [
                [
                    'name' => 'Roles',
                    'window' => ['key' => 'roles.index', 'label' => 'View'],
                    'buttons' => [
                        ['key' => 'roles.update', 'label' => 'Edit', 'flag' => 'Also covers roles.edit form-load.'],
                        ['key' => 'roles.grantPermission', 'label' => 'Generate Permission'],
                        ['key' => 'roles.savePermissions', 'label' => 'Save Permissions', 'flag' => 'IMPORTANT — was missing entirely. roles.grantPermission only covers loading the Grant Permissions screen; this is the actual POST that saves the changes. Normally granted together with roles.grantPermission.'],
                        ['key' => 'roles.store', 'label' => 'Add', 'flag' => 'Not in your wanted list — Roles is one of the most sensitive windows, confirm if this needs gating too.'],
                        ['key' => 'roles.destroy', 'label' => 'Delete', 'flag' => 'Not in your wanted list — same as above.'],
                    ],
                ],
                [
                    'name' => 'Create User',
                    'window' => ['key' => 'role-management.create-user', 'label' => 'View / Add'],
                    'buttons' => [
                        ['key' => 'role-management.store-user', 'label' => 'Add submit'],
                    ],
                ],
                [
                    'name' => 'User List',
                    'window' => ['key' => 'role-management.user-list', 'label' => 'View'],
                    'buttons' => [
                        ['key' => 'role-management.user-update', 'label' => 'Edit'],
                        ['key' => 'role-management.user-change-password', 'label' => 'Change Password'],
                    ],
                ],
            ],
        ],

        [
            'name' => 'Training Vehicles',
            'windows' => [
                [
                    'name' => 'Training Vehicles',
                    'window' => ['key' => 'training-vehicles', 'label' => 'View', 'flag' => 'ENTIRELY NEW — this whole section was not in your original wanted-actions list, not in the sidebar, and the route file itself comments it "not complete". Added here so nothing is missing, but confirm whether this is actually ready to have permissions wired up yet, or should wait.'],
                    'buttons' => [
                        ['key' => 'training-vehicles.store', 'label' => 'Add'],
                        ['key' => 'training-vehicles.update', 'label' => 'Edit'],
                        ['key' => 'training-vehicles.destroy', 'label' => 'Delete'],
                    ],
                ],
            ],
        ],

    ],

];