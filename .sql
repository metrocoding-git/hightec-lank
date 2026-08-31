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
 