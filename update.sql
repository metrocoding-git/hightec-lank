CREATE TABLE topics ( id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, name VARCHAR(255) NOT NULL, intake_id BIGINT UNSIGNED, subject_id BIGINT UNSIGNED, course_id BIGINT UNSIGNED, lesson_id INT UNSIGNED, created_at TIMESTAMP NULL DEFAULT NULL, updated_at TIMESTAMP NULL DEFAULT NULL, CONSTRAINT fk_topics_intake FOREIGN KEY (intake_id) REFERENCES intakes(id) ON DELETE SET NULL, CONSTRAINT fk_topics_subject FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE SET NULL, CONSTRAINT fk_topics_course FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE SET NULL, CONSTRAINT fk_topics_lesson FOREIGN KEY (lesson_id) REFERENCES lessons(id) ON DELETE SET NULL ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE student_categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_name VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY (category_name)
);

CREATE TABLE genders ( id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, name VARCHAR(50) NOT NULL UNIQUE, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP );



CREATE TABLE `students` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `branch` bigint(20) UNSIGNED NOT NULL,
  `admission_number` varchar(255) NOT NULL,
  `admission_date` date NOT NULL,
  `student_category_id` int(11) NOT NULL,
  `gender` int(10) UNSIGNED NOT NULL,
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
  `sibling_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `students_admission_number_unique` (`admission_number`),
  UNIQUE KEY `students_email_address_unique` (`email_address`),
  UNIQUE KEY `students_phone_number_unique` (`phone_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


ALTER TABLE `student_categories` 
MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `genders` 
MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `branches` MODIFY COLUMN `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `branches` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;