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