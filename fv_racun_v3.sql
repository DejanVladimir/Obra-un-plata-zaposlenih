-- Adminer 4.3.1 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

USE `fv_racun_v3`;

SET NAMES utf8mb4;

DROP TABLE IF EXISTS `benefit`;
CREATE TABLE `benefit` (
  `benefit_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tax` decimal(10,0) NOT NULL,
  `disability_and_pension` decimal(10,0) NOT NULL,
  `health_insurance` decimal(10,0) NOT NULL,
  `unemployement` decimal(10,0) NOT NULL,
  PRIMARY KEY (`benefit_id`),
  UNIQUE KEY `uq_title` (`title`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `benefit` (`benefit_id`, `title`, `tax`, `disability_and_pension`, `health_insurance`, `unemployement`) VALUES
(1,	'Benefits Category 1',	10,	14,	5,	1);

DROP TABLE IF EXISTS `employee`;
CREATE TABLE `employee` (
  `employee_id` int(11) NOT NULL AUTO_INCREMENT,
  `first_name` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_name` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
  `born_at` date NOT NULL,
  `workplace_title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_archived` int(1) NOT NULL,
  `pay_grade_id` int(11) NOT NULL,
  PRIMARY KEY (`employee_id`),
  UNIQUE KEY `born_at` (`born_at`),
  KEY `first_name` (`first_name`),
  KEY `last_name` (`last_name`),
  KEY `workplace_title` (`workplace_title`(191)),
  KEY `is_archived` (`is_archived`),
  KEY `pay_grade_id` (`pay_grade_id`),
  CONSTRAINT `fk_employee_pay_grade_id` FOREIGN KEY (`pay_grade_id`) REFERENCES `pay_grade` (`pay_grade_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `employee` (`employee_id`, `first_name`, `last_name`, `born_at`, `workplace_title`, `is_archived`, `pay_grade_id`) VALUES
(1,	'Petar',	'Petrović',	'1996-08-14',	'Web Developer',	0,	1);

DROP TABLE IF EXISTS `payment`;
CREATE TABLE `payment` (
  `payment_id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `month_worked_at` varchar(7) COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount_paid` decimal(10,0) NOT NULL,
  `paid_at` date NOT NULL,
  `is_paid_fully` int(1) NOT NULL,
  `payment_detail` text COLLATE utf8mb4_unicode_ci NOT NULL,
  KEY `employee_id` (`employee_id`),
  KEY `amount_paid` (`amount_paid`),
  KEY `month_worked_at` (`month_worked_at`),
  KEY `paid_at` (`paid_at`),
  KEY `is_paid_fully` (`is_paid_fully`),
  CONSTRAINT `fk_payment_employee_id` FOREIGN KEY (`employee_id`) REFERENCES `employee` (`employee_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `payment` (`payment_id`, `employee_id`, `month_worked_at`, `amount_paid`, `paid_at`, `is_paid_fully`, `payment_detail`) VALUES
(0,	1,	'2018-06',	11000,	'2018-09-11',	1,	'test account');

DROP TABLE IF EXISTS `pay_grade`;
CREATE TABLE `pay_grade` (
  `pay_grade_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
  `max_hours` int(4) NOT NULL,
  `max_pay` decimal(10,0) NOT NULL,
  `benefit_id` int(11) NOT NULL,
  PRIMARY KEY (`pay_grade_id`),
  UNIQUE KEY `uq_title` (`title`),
  KEY `max_hours` (`max_hours`),
  KEY `max_pay` (`max_pay`),
  KEY `benefit_id` (`benefit_id`),
  CONSTRAINT `fk_benefit_id` FOREIGN KEY (`benefit_id`) REFERENCES `benefit` (`benefit_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `pay_grade` (`pay_grade_id`, `title`, `max_hours`, `max_pay`, `benefit_id`) VALUES
(1,	'Pay grade 1',	180,	35000,	1);

DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `uq_username` (`username`),
  KEY `password` (`password`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `user` (`user_id`, `username`, `password`) VALUES
(1,	'user',	'3071a8f1d1a6081b5fddd9d99a2599ccccc31921c337f40ce33ed498d427d073d8d2ec8848f03e9f1268157ed51e71beae1c946359fb5e17acc0d6bf5f097ab2');

DROP TABLE IF EXISTS `work_hour`;
CREATE TABLE `work_hour` (
  `work_hour_id` int(11) NOT NULL AUTO_INCREMENT,
  `checked_in_at` time NOT NULL,
  `checked_out_at` time NOT NULL,
  `checked_at` date NOT NULL,
  `employee_id` int(11) NOT NULL,
  PRIMARY KEY (`work_hour_id`),
  KEY `checked_in_at` (`checked_in_at`),
  KEY `checked_out_at` (`checked_out_at`),
  KEY `checked_at` (`checked_at`),
  KEY `employee_id` (`employee_id`),
  CONSTRAINT `fk_work_hour_employee_id` FOREIGN KEY (`employee_id`) REFERENCES `employee` (`employee_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `work_hour` (`work_hour_id`, `checked_in_at`, `checked_out_at`, `checked_at`, `employee_id`) VALUES
(1,	'11:53:11',	'12:54:22',	'2018-09-05',	1),
(2,	'10:00:00',	'18:12:11',	'2018-09-04',	1),
(4,	'11:05:49',	'11:07:04',	'2018-09-11',	1);

-- 2018-09-12 06:37:20
