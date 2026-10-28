CREATE TABLE
  topics (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    intake_id BIGINT UNSIGNED,
    subject_id BIGINT UNSIGNED,
    course_id BIGINT UNSIGNED,
    lesson_id INT UNSIGNED,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    CONSTRAINT fk_topics_intake FOREIGN KEY (intake_id) REFERENCES intakes (id) ON DELETE SET NULL,
    CONSTRAINT fk_topics_subject FOREIGN KEY (subject_id) REFERENCES subjects (id) ON DELETE SET NULL,
    CONSTRAINT fk_topics_course FOREIGN KEY (course_id) REFERENCES courses (id) ON DELETE SET NULL,
    CONSTRAINT fk_topics_lesson FOREIGN KEY (lesson_id) REFERENCES lessons (id) ON DELETE SET NULL
  ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

CREATE TABLE
  student_categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_name VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY (category_name)
  );

CREATE TABLE
  genders (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
  );

CREATE TABLE
  `students` (
    `id` bigint (20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `branch` bigint (20) UNSIGNED NOT NULL,
    `admission_number` varchar(255) NOT NULL,
    `admission_date` date NOT NULL,
    `student_category_id` int (11) NOT NULL,
    `gender` int (10) UNSIGNED NOT NULL,
    `first_name` varchar(255) NOT NULL,
    `last_name` varchar(255) NOT NULL,
    `student_name` varchar(255) NOT NULL,
    `date_of_birth` date NOT NULL,
    `photo` varchar(255) DEFAULT NULL,
    `email_address` varchar(255) NOT NULL,
    `phone_number` varchar(20) NOT NULL,
    `current_address` text NOT NULL,
    `permanent_address` text NOT NULL,
    `blood_group` varchar(10) DEFAULT NULL,
    `height` varchar(10) DEFAULT NULL,
    `weight` varchar(10) DEFAULT NULL,
    `guardians_name` varchar(255) NOT NULL,
    `relation_with_guardian` varchar(50) NOT NULL,
    `guardians_email` varchar(255) NOT NULL,
    `guardians_photo` varchar(255) DEFAULT NULL,
    `guardians_phone` varchar(20) NOT NULL,
    `guardians_occupation` varchar(255) NOT NULL,
    `guardians_address` text NOT NULL,
    `national_id_number` varchar(12) NOT NULL,
    `birth_certificate_number` varchar(50) NOT NULL,
    `additional_notes` text DEFAULT NULL,
    `document_title_1` varchar(255) DEFAULT NULL,
    `document_file_1` varchar(255) DEFAULT NULL,
    `document_title_2` varchar(255) DEFAULT NULL,
    `document_file_2` varchar(255) DEFAULT NULL,
    `document_title_3` varchar(255) DEFAULT NULL,
    `document_file_3` varchar(255) DEFAULT NULL,
    `document_title_4` varchar(255) DEFAULT NULL,
    `document_file_4` varchar(255) DEFAULT NULL,
    `previous_school_details` text DEFAULT NULL,
    `dormitory_name` varchar(100) DEFAULT NULL,
    `sibling_id` bigint (20) UNSIGNED DEFAULT NULL,
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `students_admission_number_unique` (`admission_number`),
    UNIQUE KEY `students_email_address_unique` (`email_address`),
    UNIQUE KEY `students_phone_number_unique` (`phone_number`)
  ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

ALTER TABLE `student_categories` MODIFY `id` bigint (20) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `genders` MODIFY `id` bigint (20) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `branches` MODIFY COLUMN `id` bigint (20) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `branches` CONVERT TO CHARACTER
SET
  utf8mb4 COLLATE utf8mb4_unicode_ci;

ALTER TABLE `item_sell_batches`
ADD COLUMN `invoice_number` VARCHAR(255) NULL AFTER `payment_method`,
ADD COLUMN `bank_payment_amount` DECIMAL(10, 2) NULL AFTER `invoice_number`,
ADD COLUMN `bank_payment_date` DATE NULL AFTER `bank_payment_amount`,
ADD COLUMN `bank_payment_document` VARCHAR(255) NULL AFTER `bank_payment_date`;

ALTER TABLE `item_receive`
ADD COLUMN `invoice_number` VARCHAR(255) NULL AFTER `payment_method`,
ADD COLUMN `bank_payment_amount` DECIMAL(10, 2) NULL AFTER `invoice_number`,
ADD COLUMN `bank_payment_date` DATE NULL AFTER `bank_payment_amount`,
ADD COLUMN `bank_payment_document` VARCHAR(255) NULL AFTER `bank_payment_date`;

-- 02/04/2026
ALTER TABLE `students` ADD `is_old_stu` TINYINT NOT NULL DEFAULT '0' AFTER `district_id`;

ALTER TABLE `fee_invoices` ADD `notes` TEXT NULL AFTER `payment_file`;

-- 02/10/2026
ALTER TABLE `invoices_payments` ADD `notes` TEXT NULL AFTER `payment_file`;

-- 04/04/2026
CREATE TABLE
  student_attendance_imports (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    intake_id BIGINT UNSIGNED NOT NULL,
    course_id BIGINT UNSIGNED NOT NULL,
    attendance_date DATE NOT NULL,
    file_name VARCHAR(255) NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    uploaded_by BIGINT UNSIGNED NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    INDEX idx_student_attendance_imports_intake_id (intake_id),
    INDEX idx_student_attendance_imports_course_id (course_id),
    INDEX idx_student_attendance_imports_uploaded_by (uploaded_by),
    CONSTRAINT fk_student_attendance_imports_intake FOREIGN KEY (intake_id) REFERENCES intakes (id) ON DELETE CASCADE,
    CONSTRAINT fk_student_attendance_imports_course FOREIGN KEY (course_id) REFERENCES courses (id) ON DELETE CASCADE,
    CONSTRAINT fk_student_attendance_imports_uploaded_by FOREIGN KEY (uploaded_by) REFERENCES users (id) ON DELETE SET NULL
  )


-- 28/10/2026 — NVQ Payment report + Item Stock Balance report permissions
-- (granted to Admin, role_id 1; grant other roles from Role Management)
INSERT INTO `permissions` (`permission_name`, `label`, `created_at`, `updated_at`)
SELECT * FROM (
    SELECT 'reports.nvq-payment' AS permission_name, 'View' AS label, NOW() AS c, NOW() AS u UNION ALL
    SELECT 'reports.nvq-payment.export', 'Export', NOW(), NOW() UNION ALL
    SELECT 'reports.item-stock', 'View', NOW(), NOW() UNION ALL
    SELECT 'reports.item-stock.export', 'Export', NOW(), NOW()
) AS t
WHERE NOT EXISTS (SELECT 1 FROM `permissions` p WHERE p.permission_name = t.permission_name);

INSERT INTO `role_permissions` (`role_id`, `permission_id`, `created_at`, `updated_at`)
SELECT 1, p.id, NOW(), NOW()
FROM `permissions` p
WHERE p.permission_name IN ('reports.nvq-payment', 'reports.nvq-payment.export', 'reports.item-stock', 'reports.item-stock.export')
  AND NOT EXISTS (SELECT 1 FROM `role_permissions` rp WHERE rp.role_id = 1 AND rp.permission_id = p.id);


-- 28/10/2026 — Leave requests
CREATE TABLE IF NOT EXISTS `leave_requests` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `branch_id` BIGINT UNSIGNED NULL,
  `leave_type` VARCHAR(50) NOT NULL,
  `from_date` DATE NOT NULL,
  `to_date` DATE NOT NULL,
  `day_part` VARCHAR(20) NOT NULL DEFAULT 'full',
  `days` DECIMAL(5,1) NOT NULL DEFAULT 0,
  `reason` TEXT NOT NULL,
  `contact_during_leave` VARCHAR(255) NULL,
  `attachment` VARCHAR(255) NULL,
  `status` VARCHAR(20) NOT NULL DEFAULT 'pending',
  `reviewed_by` BIGINT UNSIGNED NULL,
  `reviewed_at` TIMESTAMP NULL,
  `review_note` TEXT NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`),
  INDEX `leave_requests_user_status` (`user_id`, `status`),
  INDEX `leave_requests_status_from` (`status`, `from_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `permissions` (`permission_name`, `label`, `created_at`, `updated_at`)
SELECT * FROM (
    SELECT 'leave.my' AS permission_name, 'View' AS label, NOW() AS c, NOW() AS u UNION ALL
    SELECT 'leave.store', 'Apply', NOW(), NOW() UNION ALL
    SELECT 'leave.cancel', 'Cancel own pending', NOW(), NOW() UNION ALL
    SELECT 'leave.review-list', 'View', NOW(), NOW() UNION ALL
    SELECT 'leave.review', 'Accept / Not Accept', NOW(), NOW()
) AS t
WHERE NOT EXISTS (SELECT 1 FROM `permissions` p WHERE p.permission_name = t.permission_name);

-- Admin (role 1) gets everything; grant "My Leaves" + Apply to staff roles
-- and "Leave Requests" + Accept to reviewer roles from Role Management.
INSERT INTO `role_permissions` (`role_id`, `permission_id`, `created_at`, `updated_at`)
SELECT 1, p.id, NOW(), NOW()
FROM `permissions` p
WHERE p.permission_name IN ('leave.my', 'leave.store', 'leave.cancel', 'leave.review-list', 'leave.review')
  AND NOT EXISTS (SELECT 1 FROM `role_permissions` rp WHERE rp.role_id = 1 AND rp.permission_id = p.id);
