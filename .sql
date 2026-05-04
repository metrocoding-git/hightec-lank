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