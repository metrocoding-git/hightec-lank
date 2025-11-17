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
   



