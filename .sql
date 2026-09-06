ALTER TABLE `student_intakes` DROP `status`;

ALTER TABLE `student_courses` ADD `intake_id` BIGINT(10) NULL AFTER `course_id`;

DROP TABLE `student_intakes`

ALTER TABLE `student_courses` ADD `intake_id` BIGINT(10) NULL AFTER `course_id`;

ALTER TABLE `item_sell_batches`
ADD COLUMN `invoice_number` VARCHAR(255) NULL AFTER `payment_method`,
ADD COLUMN `bank_payment_amount` DECIMAL(10,2) NULL AFTER `invoice_number`,
ADD COLUMN `bank_payment_date` DATE NULL AFTER `bank_payment_amount`,
ADD COLUMN `bank_payment_document` VARCHAR(255) NULL AFTER `bank_payment_date`;

ALTER TABLE `item_receive`
ADD COLUMN `invoice_number` VARCHAR(255) NULL AFTER `payment_method`,
ADD COLUMN `bank_payment_amount` DECIMAL(10,2) NULL AFTER `invoice_number`,
ADD COLUMN `bank_payment_date` DATE NULL AFTER `bank_payment_amount`,
ADD COLUMN `bank_payment_document` VARCHAR(255) NULL AFTER `bank_payment_date`;


-- 11/14
ALTER TABLE `student_courses` DROP `enrolled_at`;


-- 11/17
CREATE TABLE `user_branches` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `branch_id` BIGINT UNSIGNED NOT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `user_branches_user_id_foreign`
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  CONSTRAINT `user_branches_branch_id_foreign`
    FOREIGN KEY (`branch_id`) REFERENCES `branches`(`id`) ON DELETE CASCADE,
  UNIQUE KEY `user_branch_unique` (`user_id`, `branch_id`)
);

ALTER TABLE `item_sell_batches`
ADD `branch_id` BIGINT UNSIGNED NULL AFTER `id`;

ALTER TABLE `item_sell_batches`
ADD CONSTRAINT `item_sell_batches_branch_id_foreign`
    FOREIGN KEY (`branch_id`) REFERENCES `branches`(`id`)
    ON DELETE SET NULL;

ALTER TABLE `students`
ADD `create_user_id` BIGINT UNSIGNED NULL,
ADD `update_user_id` BIGINT UNSIGNED NULL;
   

-- 11/18
ALTER TABLE `student_courses`
ADD `user_id` BIGINT UNSIGNED NULL AFTER `branch_id`;

ALTER TABLE `student_courses`
ADD CONSTRAINT `student_courses_user_id_foreign`
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`)
    ON DELETE SET NULL;


-- 11/19

    -- make district and after run db seeder

CREATE TABLE `districts` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
);

php artisan db:seed --class=DistrictsSeeder

ALTER TABLE `students`
ADD `district_id` BIGINT UNSIGNED NULL;


-- 12/02
CREATE TABLE `student_verifications` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `student_id` BIGINT UNSIGNED NOT NULL,

  `verification_item` VARCHAR(255) NOT NULL,

  `old_value` TEXT NULL,
  `new_value` TEXT NULL,

  `signature_path` VARCHAR(255) NULL,

  `updated_by` BIGINT UNSIGNED NOT NULL,

  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



-- 12/11
ALTER TABLE `item_issue_batches` ADD `created_by` INT NULL AFTER `active_status`;
ALTER TABLE `item_sell_batches` ADD `created_by` INT NULL AFTER `status`;


ALTER TABLE `item_receive` ADD `created_by` INT NULL AFTER `total_paid`;



-- 02/24

ALTER TABLE `invoices_payments` ADD `invoice_payment_confirm` TINYINT NULL DEFAULT NULL AFTER `payment_invoice_number`;





ALTER TABLE `invoices_payments` ADD `customer_name` VARCHAR(255) NULL AFTER `notes`, ADD `invoice_payment_note` TEXT NULL AFTER `customer_name`;


-- 03/24
ALTER TABLE `courses` ADD `has_nvq_payment` TINYINT NOT NULL DEFAULT '0' AFTER `duration`, ADD `nvq_amount` DECIMAL(10,2) NULL AFTER `has_nvq_payment`;


ALTER TABLE `student_courses` ADD `change_course_id` INT NULL DEFAULT NULL AFTER `intake_id`;


ALTER TABLE `invoices_payments` ADD `update_user_id` INT NULL DEFAULT NULL AFTER `create_user_id`;
ALTER TABLE `invoices_payments` ADD `invoice_payment_cheque_number` VARCHAR(255) NULL DEFAULT NULL AFTER `invoice_payment_note`, ADD `invoice_payment_amount` DECIMAL NULL DEFAULT NULL AFTER `invoice_payment_cheque_number`, ADD `invoice_payment_confirm_note` TEXT NULL DEFAULT NULL AFTER `invoice_payment_amount`;



-- 04/19
ALTER TABLE `item_inventory` ADD `branch_id` INT NULL DEFAULT NULL AFTER `item_id`;


ALTER TABLE `student_courses` ADD `updated_by` INT NULL DEFAULT NULL AFTER `change_course_id`;

ALTER TABLE `student_courses` ADD `student_category_id` INT NULL DEFAULT NULL AFTER `notes`;



-- 05/05
CREATE TABLE invoice_payment_receipts (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    receipt_number VARCHAR(100) NOT NULL UNIQUE,
    cheque_number VARCHAR(100) NULL,

    total_amount DECIMAL(12,2) NOT NULL,
    note TEXT NULL,

    created_by BIGINT UNSIGNED NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE invoice_payment_receipt_items (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    receipt_id BIGINT UNSIGNED NOT NULL,
    invoice_payment_id BIGINT UNSIGNED NOT NULL,

    paid_amount DECIMAL(12,2) NOT NULL,

    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (receipt_id) REFERENCES invoice_payment_receipts(id) ON DELETE CASCADE,
    FOREIGN KEY (invoice_payment_id) REFERENCES invoices_payments(id) ON DELETE CASCADE
);


ALTER TABLE `fee_invoices` ADD `student_category_id` INT NULL DEFAULT NULL ;


ALTER TABLE `invoices_payments` ADD `payment_invoice_open_date` TIMESTAMP NULL DEFAULT NULL AFTER `invoice_payment_confirm_note`;



-- 05/23
ALTER TABLE `courses`
    ADD COLUMN `has_food_fee` TINYINT(1) NOT NULL DEFAULT 0 AFTER `nvq_amount`,
    ADD COLUMN `breakfast_amount` DECIMAL(10,2) NULL AFTER `has_food_fee`,
    ADD COLUMN `lunch_amount` DECIMAL(10,2) NULL AFTER `breakfast_amount`,
    ADD COLUMN `dinner_amount` DECIMAL(10,2) NULL AFTER `lunch_amount`;


-- 06/07

CREATE TABLE `food_payments` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `invoice_id` BIGINT UNSIGNED NOT NULL,
    `course_id` BIGINT UNSIGNED NOT NULL,

    -- Meal period
    `from_date` DATE NOT NULL,
    `to_date` DATE NOT NULL,

    -- Meal selections
    `has_breakfast` TINYINT(1) NOT NULL DEFAULT 0,
    `has_lunch` TINYINT(1) NOT NULL DEFAULT 0,
    `has_dinner` TINYINT(1) NOT NULL DEFAULT 0,

    -- Totals
    `total_food_amount` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `paid_amount` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `due_amount` DECIMAL(10,2) NOT NULL DEFAULT 0.00,

    `payment_status` ENUM('not','partial','full') NOT NULL DEFAULT 'not',
    `payment_method` VARCHAR(50) DEFAULT NULL,

    -- Lunchbox number
    `lunchbox_number` VARCHAR(20) DEFAULT NULL,

    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,

    PRIMARY KEY (`id`),

    KEY `idx_food_payments_invoice_id` (`invoice_id`),
    KEY `idx_food_payments_course_id` (`course_id`),

    CONSTRAINT `fk_fp_invoice`
        FOREIGN KEY (`invoice_id`)
        REFERENCES `fee_invoices`(`id`)
        ON DELETE CASCADE,

    CONSTRAINT `fk_fp_course`
        FOREIGN KEY (`course_id`)
        REFERENCES `courses`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



CREATE TABLE `lunchbox_sequences` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `branch_id` BIGINT UNSIGNED NOT NULL,

    `year` SMALLINT UNSIGNED NOT NULL,
    `month` TINYINT UNSIGNED NOT NULL,

    `letter` CHAR(1) NOT NULL DEFAULT 'A',
    `last_number` INT UNSIGNED NOT NULL DEFAULT 0,

    PRIMARY KEY (`id`),

    UNIQUE KEY `uq_branch_ym` (`branch_id`, `year`, `month`),

    CONSTRAINT `fk_ls_branch`
        FOREIGN KEY (`branch_id`)
        REFERENCES `branches`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


ALTER TABLE `paid_courses` ADD `payment_type` VARCHAR(255) NULL AFTER `payment_file`;

ALTER TABLE `item_issue_batches` ADD `student_category_id` INT NULL AFTER `created_by`;
ALTER TABLE `item_sell_batches` ADD `student_category_id` INT NULL AFTER `created_by`;


-- 06/09
ALTER TABLE `food_payments` ADD `status` TINYINT NOT NULL DEFAULT '1' AFTER `lunchbox_number`;


-- 08/05
CREATE TABLE `training_vehicles` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL,
    `vehicle_number` VARCHAR(255) NULL,
    `type` VARCHAR(255) NULL,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `subject_attendance_sessions` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,

    `student_id` BIGINT UNSIGNED NOT NULL,
    `intake_id` BIGINT UNSIGNED NOT NULL,
    `course_id` BIGINT UNSIGNED NOT NULL,
    `subject_id` BIGINT UNSIGNED NULL,

    `attendance_date` DATE NOT NULL,
    `time_in` DATETIME NOT NULL,
    `time_out` DATETIME NULL,

    `vehicle_id` BIGINT UNSIGNED NULL,
    `note` TEXT NULL,

    `marked_in_by` BIGINT UNSIGNED NULL,
    `marked_out_by` BIGINT UNSIGNED NULL,

    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,

    PRIMARY KEY (`id`),

    INDEX `idx_student_date` (`student_id`, `attendance_date`),
    INDEX `idx_attendance` (`intake_id`, `course_id`, `subject_id`, `attendance_date`),

    CONSTRAINT `fk_sas_student`
        FOREIGN KEY (`student_id`)
        REFERENCES `students`(`id`)
        ON DELETE CASCADE,

    CONSTRAINT `fk_sas_intake`
        FOREIGN KEY (`intake_id`)
        REFERENCES `intakes`(`id`)
        ON DELETE CASCADE,

    CONSTRAINT `fk_sas_course`
        FOREIGN KEY (`course_id`)
        REFERENCES `courses`(`id`)
        ON DELETE CASCADE,

    CONSTRAINT `fk_sas_subject`
        FOREIGN KEY (`subject_id`)
        REFERENCES `subjects`(`id`)
        ON DELETE SET NULL,

    CONSTRAINT `fk_sas_vehicle`
        FOREIGN KEY (`vehicle_id`)
        REFERENCES `training_vehicles`(`id`)
        ON DELETE SET NULL,

    CONSTRAINT `fk_sas_marked_in`
        FOREIGN KEY (`marked_in_by`)
        REFERENCES `users`(`id`)
        ON DELETE SET NULL,

    CONSTRAINT `fk_sas_marked_out`
        FOREIGN KEY (`marked_out_by`)
        REFERENCES `users`(`id`)
        ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 08/11
ALTER TABLE `training_vehicles` ADD `branch_id` INT NOT NULL AFTER `type`;


ALTER TABLE `subject_attendance_sessions`
ADD COLUMN `checkpoint_type` VARCHAR(255) NULL AFTER `subject_id`;

ALTER TABLE `subject_attendance_sessions`
ADD INDEX `sas_checkpoint_lookup_idx`
(`student_id`, `course_id`, `attendance_date`, `checkpoint_type`);


-- 08/12
ALTER TABLE `subjects`
    ADD `demonstrator_time` INT UNSIGNED NULL AFTER `type`,
    ADD `practical_time` INT UNSIGNED NULL AFTER `demonstrator_time`;


-- 08/19
ALTER TABLE assign_subjects
ADD COLUMN demonstrator_time VARCHAR(255) NULL AFTER subject_id,
ADD COLUMN practical_time VARCHAR(255) NULL AFTER demonstrator_time;


-- 08/26
ALTER TABLE `courses`
    ADD COLUMN `is_active` TINYINT(1) NOT NULL DEFAULT 1;
 

ALTER TABLE `intakes`
    ADD COLUMN `is_active` TINYINT(1) NOT NULL DEFAULT 1;

ALTER TABLE `subjects`
    ADD COLUMN `is_active` TINYINT(1) NOT NULL DEFAULT 1;

ALTER TABLE `assign_subjects`
    ADD COLUMN `is_active` TINYINT(1) NOT NULL DEFAULT 1;

ALTER TABLE `student_categories`
    ADD COLUMN `is_active` TINYINT(1) NOT NULL DEFAULT 1;

ALTER TABLE `permissions` ADD `label` VARCHAR(255) NULL AFTER `permission_name`;

-- 08/31
CREATE TABLE `user_course` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `course_id` BIGINT UNSIGNED NOT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_course_user_id_course_id_unique` (`user_id`, `course_id`),
  KEY `user_course_user_id_foreign` (`user_id`),
  KEY `user_course_course_id_foreign` (`course_id`),
  CONSTRAINT `user_course_user_id_foreign` FOREIGN KEY (`user_id`)
    REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `user_course_course_id_foreign` FOREIGN KEY (`course_id`)
    REFERENCES `courses` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
 

-- 09/01
 INSERT INTO `permissions` (`id`, `permission_name`, `label`, `created_at`, `updated_at`) VALUES (NULL, 'student-practical-card.show', 'practical Card', NULL, NULL);


CREATE TABLE `assessment_templates` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `course_id` BIGINT UNSIGNED NOT NULL,
  `module_name` VARCHAR(255) NOT NULL,
  `ncs_code` VARCHAR(100) NULL,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_by` BIGINT UNSIGNED NULL,
  `updated_by` BIGINT UNSIGNED NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `assessment_templates_course_id_index` (`course_id`),
  CONSTRAINT `assessment_templates_course_id_foreign`
    FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE,
  CONSTRAINT `assessment_templates_created_by_foreign`
    FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `assessment_templates_updated_by_foreign`
    FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
 

CREATE TABLE `assessment_template_titles` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `assessment_template_id` BIGINT UNSIGNED NOT NULL,
  `title` VARCHAR(255) NOT NULL,
  `max_marks` INT UNSIGNED NOT NULL DEFAULT 0,
  `sort_order` INT UNSIGNED NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `assessment_template_titles_template_id_index` (`assessment_template_id`),
  CONSTRAINT `assessment_template_titles_template_id_foreign`
    FOREIGN KEY (`assessment_template_id`) REFERENCES `assessment_templates` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
 
CREATE TABLE `assessment_template_sub_titles` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `assessment_template_title_id` BIGINT UNSIGNED NOT NULL,
  `sub_title` VARCHAR(255) NOT NULL,
  `sort_order` INT UNSIGNED NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `assessment_template_sub_titles_title_id_index` (`assessment_template_title_id`),
  CONSTRAINT `assessment_template_sub_titles_title_id_foreign`
    FOREIGN KEY (`assessment_template_title_id`) REFERENCES `assessment_template_titles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
 
CREATE TABLE `student_assessments` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `student_id` BIGINT UNSIGNED NOT NULL,
  `student_course_id` BIGINT UNSIGNED NOT NULL,
  `assessment_template_id` BIGINT UNSIGNED NOT NULL,
  `vehicle_id` BIGINT UNSIGNED NULL,
  `equipment_model` VARCHAR(255) NULL,
  `branch_id` BIGINT UNSIGNED NULL,
  `assessed_at` DATETIME NULL,
  `total_marks` INT UNSIGNED NOT NULL DEFAULT 0,
  `max_marks` INT UNSIGNED NOT NULL DEFAULT 0,
  `grade` VARCHAR(20) NULL,
  `is_qualified` TINYINT(1) NOT NULL DEFAULT 0,
  `comments` TEXT NULL,
  `evaluator_id` BIGINT UNSIGNED NULL,
  `operator_signature` VARCHAR(255) NULL,
  `status` ENUM('draft','submitted') NOT NULL DEFAULT 'draft',
  `created_by` BIGINT UNSIGNED NULL,
  `updated_by` BIGINT UNSIGNED NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
 

CREATE TABLE `student_assessment_marks` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `student_assessment_id` BIGINT UNSIGNED NOT NULL,
  `assessment_template_title_id` BIGINT UNSIGNED NOT NULL,
  `assessment_template_sub_title_id` BIGINT UNSIGNED NOT NULL,
  `competency` ENUM('C','NC') NULL,
  `marks` DECIMAL(6,2) NULL,
  `remarks` VARCHAR(500) NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `student_assessment_marks_unique` (`student_assessment_id`, `assessment_template_sub_title_id`),
  KEY `student_assessment_marks_title_id_index` (`assessment_template_title_id`),
  CONSTRAINT `student_assessment_marks_assessment_id_foreign`
    FOREIGN KEY (`student_assessment_id`) REFERENCES `student_assessments` (`id`) ON DELETE CASCADE,
  CONSTRAINT `student_assessment_marks_title_id_foreign`
    FOREIGN KEY (`assessment_template_title_id`) REFERENCES `assessment_template_titles` (`id`) ON DELETE CASCADE,
  CONSTRAINT `student_assessment_marks_sub_title_id_foreign`
    FOREIGN KEY (`assessment_template_sub_title_id`) REFERENCES `assessment_template_sub_titles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `permissions` (`permission_name`, `label`, `created_at`, `updated_at`) VALUES
('assessment-template.index',         'Assessment Template - View',                     NOW(), NOW()),
('assessment-template.store',         'Assessment Template - Add',                      NOW(), NOW()),
('assessment-template.update',        'Assessment Template - Edit',                     NOW(), NOW()),
('assessment-template.destroy', 'Assessment Template - Enable/Disable',           NOW(), NOW()),
('student-assessment.index',          'Final Assessment Marks - View',                  NOW(), NOW()),
('student-assessment.store',          'Final Assessment Marks - Save Assessment',       NOW(), NOW()),
('student-assessment.show',           'Final Assessment Marks - View One Assessment',   NOW(), NOW());
 


INSERT INTO `permissions` (`permission_name`, `label`, `created_at`, `updated_at`) VALUES
('student-assessment.destroy', 'Final Assessment Marks - Undo (2-day window)', NOW(), NOW());
 

 ALTER TABLE `student_assessments`
    ADD COLUMN `deleted_at` TIMESTAMP NULL DEFAULT NULL AFTER `updated_at`;
 


 
CREATE TABLE `certificates` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `student_assessment_id` BIGINT UNSIGNED NOT NULL,
  `certificate_number` VARCHAR(50) NOT NULL,
  `status` ENUM('generated','conductor_signed','approved') NOT NULL DEFAULT 'generated',
 
  `conductor_signature_path` VARCHAR(255) NULL,
  `conductor_signed_by` BIGINT UNSIGNED NULL,
  `conductor_signed_at` TIMESTAMP NULL,
 
  `owner_signature_path` VARCHAR(255) NULL,
  `owner_signed_by` BIGINT UNSIGNED NULL,
  `owner_signed_at` TIMESTAMP NULL,
 
  `pdf_path` VARCHAR(255) NULL,
  `generated_by` BIGINT UNSIGNED NULL,
  `generated_at` TIMESTAMP NULL,
 
  `downloaded_by` BIGINT UNSIGNED NULL,
  `downloaded_at` TIMESTAMP NULL,
  `download_count` INT UNSIGNED NOT NULL DEFAULT 0,
 
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
 
  UNIQUE KEY `uniq_student_assessment` (`student_assessment_id`),
  UNIQUE KEY `uniq_certificate_number` (`certificate_number`),
  CONSTRAINT `fk_cert_assessment` FOREIGN KEY (`student_assessment_id`) REFERENCES `student_assessments`(`id`),
  CONSTRAINT `fk_cert_generated_by` FOREIGN KEY (`generated_by`) REFERENCES `users`(`id`),
  CONSTRAINT `fk_cert_conductor_signed_by` FOREIGN KEY (`conductor_signed_by`) REFERENCES `users`(`id`),
  CONSTRAINT `fk_cert_owner_signed_by` FOREIGN KEY (`owner_signed_by`) REFERENCES `users`(`id`),
  CONSTRAINT `fk_cert_downloaded_by` FOREIGN KEY (`downloaded_by`) REFERENCES `users`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
 

 INSERT INTO `permissions` (`permission_name`, `label`, `created_at`, `updated_at`) VALUES
('certificate.index',          'Certificate - Manage (view certificate window)', NOW(), NOW()),
('certificate.generate',       'Certificate - Generate', NOW(), NOW()),
('certificate.sign-conductor', 'Certificate - Sign (Assessment Conductor)', NOW(), NOW()),
('certificate.sign-owner',     'Certificate - Sign (Owner) — finalizes & unlocks download', NOW(), NOW());


-- 09/04
INSERT INTO `permissions` (`id`, `permission_name`, `label`, `created_at`, `updated_at`) VALUES (NULL, 'student-practical-card.index', 'Attendance Card', NULL, NULL);


 
ALTER TABLE `student_assessments`
    MODIFY COLUMN `grade` VARCHAR(10) NULL;


-- 09/07
CREATE TABLE certificate_templates (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,                 -- internal label shown in the Generate template dropdown, e.g. "Fork Lift Operator"
    course_id BIGINT UNSIGNED NULL,             -- optional convenience link, see note above
    course_name_line1 VARCHAR(255) NOT NULL,    -- e.g. "Heavy Vehicle Operator"
    course_name_line2 VARCHAR(255) NULL,        -- e.g. "(Fork Lift Truck)"
    bottom_image_path VARCHAR(500) NULL,        -- equipment/course photo, bottom-right of the certificate
    back_side_content LONGTEXT NULL,            -- rendered as a literal page 2 of the generated PDF when non-empty
    is_active TINYINT(1) NOT NULL DEFAULT 1,    -- one-way soft-disable, same convention as everything else in this app — no re-enable route
    created_by BIGINT UNSIGNED NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    KEY certificate_templates_course_id_index (course_id),
    KEY certificate_templates_is_active_index (is_active)
);

ALTER TABLE certificates
    ADD COLUMN certificate_template_id BIGINT UNSIGNED NULL AFTER student_assessment_id,
    ADD COLUMN issue_date DATE NULL AFTER certificate_template_id,
    ADD KEY certificates_certificate_template_id_index (certificate_template_id);
 
INSERT INTO `permissions` (`id`, `permission_name`, `label`, `created_at`, `updated_at`) VALUES (NULL, 'certificate-template.store', 'Add', NULL, NULL), (NULL, 'certificate-template.update', 'Update', NULL, NULL);

INSERT INTO `permissions` (`id`, `permission_name`, `label`, `created_at`, `updated_at`) VALUES (NULL, 'certificate-template.destroy', 'Delete', NULL, NULL), (NULL, 'certificate-template.index', 'View', NULL, NULL);