-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 07, 2026 at 03:55 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `clinicdesk_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `appointments`
--

CREATE TABLE `appointments` (
  `id` int(10) UNSIGNED NOT NULL,
  `patient_id` int(10) UNSIGNED NOT NULL,
  `doctor_id` int(10) UNSIGNED NOT NULL,
  `appt_date` date NOT NULL,
  `appt_time` time NOT NULL,
  `status` enum('pending','confirmed','completed','cancelled') NOT NULL DEFAULT 'pending',
  `reason` text DEFAULT NULL,
  `doctor_notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `appointments`
--

INSERT INTO `appointments` (`id`, `patient_id`, `doctor_id`, `appt_date`, `appt_time`, `status`, `reason`, `doctor_notes`, `created_at`) VALUES
(1, 4, 1, '2026-06-10', '09:00:00', 'completed', 'Routine cardiac check-up and ECG review.', 'Patient BP slightly elevated. Ordered lipid panel. Follow-up in 4 weeks.', '2026-06-06 06:57:35'),
(2, 5, 1, '2026-06-10', '10:00:00', 'completed', 'Chest pains and shortness of breath during mild exercise.', '', '2026-06-06 06:57:35'),
(3, 6, 2, '2026-06-11', '11:00:00', 'confirmed', 'Recurring migraines for the past three months.', 'Prescribed sumatriptan. Recommended MRI if symptoms persist.', '2026-06-06 06:57:35'),
(4, 4, 2, '2026-06-13', '14:00:00', 'completed', 'Follow-up for previous dizziness episode.', 'Vestibular testing normal. No further intervention needed.', '2026-06-06 06:57:35'),
(5, 5, 1, '2026-06-17', '09:30:00', 'completed', 'Annual physical with cardiac screening.', '', '2026-06-06 06:57:35'),
(6, 4, 2, '2026-06-15', '11:30:00', 'confirmed', 'i am vary ill', '', '2026-06-06 17:21:16');

-- --------------------------------------------------------

--
-- Table structure for table `doctors`
--

CREATE TABLE `doctors` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `specialization_id` int(10) UNSIGNED NOT NULL,
  `bio` text DEFAULT NULL,
  `consultation_fee` decimal(10,2) NOT NULL DEFAULT 0.00,
  `available_days` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `doctors`
--

INSERT INTO `doctors` (`id`, `user_id`, `specialization_id`, `bio`, `consultation_fee`, `available_days`) VALUES
(1, 2, 2, 'Board-certified cardiologist with 12 years of experience in interventional cardiology and heart failure management.', 120.00, 'Sun,Mon'),
(2, 3, 6, 'Neurologist specialising in epilepsy, stroke rehabilitation, and neurodegenerative diseases.', 150.00, 'Sun,Mon,Tue,Wed,Thu,Fri,Sat');

-- --------------------------------------------------------

--
-- Table structure for table `prescriptions`
--

CREATE TABLE `prescriptions` (
  `id` int(10) UNSIGNED NOT NULL,
  `appointment_id` int(10) UNSIGNED NOT NULL,
  `diagnosis` text DEFAULT NULL,
  `medications` text DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `prescriptions`
--

INSERT INTO `prescriptions` (`id`, `appointment_id`, `diagnosis`, `medications`, `notes`, `file_path`, `created_at`) VALUES
(1, 1, 'Stage 1 hypertension with borderline hypercholesterolaemia.', 'Lisinopril 10 mg – once daily (morning)\nAtorvastatin 20 mg – once daily (evening)', 'Reduce sodium intake. Avoid strenuous activity until follow-up. Return immediately if chest pain occurs.', NULL, '2026-06-06 06:57:36'),
(2, 3, 'Chronic migraine without aura.', 'Sumatriptan 50 mg – take at onset of migraine, may repeat after 2 hours if needed (max 200 mg/day)\nIbuprofen 400 mg – as needed for mild headaches', 'Keep a headache diary. Avoid known triggers (bright lights, caffeine). Schedule MRI if frequency increases.', NULL, '2026-06-06 06:57:36'),
(3, 2, 'Chest pain - mild angina', 'Aspirin 100mg daily', '', NULL, '2026-06-06 17:54:50'),
(4, 5, 'سيس', 'يسيس', '', NULL, '2026-06-07 13:43:56');

-- --------------------------------------------------------

--
-- Table structure for table `specializations`
--

CREATE TABLE `specializations` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `specializations`
--

INSERT INTO `specializations` (`id`, `name`) VALUES
(2, 'Cardiology'),
(3, 'Dermatology'),
(8, 'ENT'),
(1, 'General Practice'),
(6, 'Neurology'),
(7, 'Ophthalmology'),
(5, 'Orthopedics'),
(4, 'Pediatrics'),
(9, 'Psychiatry');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','doctor','patient') NOT NULL DEFAULT 'patient',
  `phone` varchar(20) DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `phone`, `avatar`, `is_active`, `created_at`) VALUES
(1, 'Admin', 'admin@gmail.com', '$2y$10$8niduRzEf5daW6OICg9JvOYH9kx5S3yIjWiF2b0IJ4zGPubS3aYai', 'admin', NULL, NULL, 1, '2026-06-06 06:57:35'),
(2, 'Dr. Sarah', 'doctor@gmail.com', '$2y$10$vJ7UStFqczxyyI8xFYS/7OwdhZOeJCLc8EGwytQSBi5LxhxjYMAo2', 'doctor', '555-0101', NULL, 1, '2026-06-06 06:57:35'),
(3, 'Dr. Jamal', 'james.okonkwo@clinic.local', '$2y$10$iiIl7ol2jvSEIFnk2JVuJuSp5ucT.ySVsROu5m8l7U0jUT9Kub9uC', 'doctor', '555-0102', NULL, 1, '2026-06-06 06:57:35'),
(4, 'ahmed', 'sufferer@gmail.com', '$2y$10$Ps6t3t5H/pRFjgRGOyl8gumOiFEA/WfLkagbwDVPxDGGtAz5upOOu', 'patient', '555-0201', NULL, 1, '2026-06-06 06:57:35'),
(5, 'Marco Rivera', 'marco.rivera@example.com', '$2y$10$B5EC6QOp61WfzFuUXD8TNuViSCmP7lBdN7uQFmLDMKhWy99t/iE7m', 'patient', '555-0202', NULL, 1, '2026-06-06 06:57:35'),
(6, 'Aisha Nwosu', 'aisha.nwosu@example.com', '$2y$10$B5EC6QOp61WfzFuUXD8TNuViSCmP7lBdN7uQFmLDMKhWy99t/iE7m', 'patient', '555-0203', NULL, 1, '2026-06-06 06:57:35'),
(7, 'ali', 'alishanti98@gmail.com', '$2y$10$Wf49R1q2X4eyr1rckXka0evoOxgApBKuc3pcA0uMFHuQQBg8PGgSK', 'admin', '123456789', NULL, 1, '2026-06-06 18:32:41');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_appointments_slot` (`doctor_id`,`appt_date`,`appt_time`),
  ADD KEY `fk_appointments_patient` (`patient_id`);

--
-- Indexes for table `doctors`
--
ALTER TABLE `doctors`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_doctors_user_id` (`user_id`),
  ADD KEY `fk_doctors_specialization` (`specialization_id`);

--
-- Indexes for table `prescriptions`
--
ALTER TABLE `prescriptions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_prescriptions_appointment` (`appointment_id`);

--
-- Indexes for table `specializations`
--
ALTER TABLE `specializations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_specializations_name` (`name`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_users_email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `appointments`
--
ALTER TABLE `appointments`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `doctors`
--
ALTER TABLE `doctors`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `prescriptions`
--
ALTER TABLE `prescriptions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `specializations`
--
ALTER TABLE `specializations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `appointments`
--
ALTER TABLE `appointments`
  ADD CONSTRAINT `fk_appointments_doctor` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_appointments_patient` FOREIGN KEY (`patient_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `doctors`
--
ALTER TABLE `doctors`
  ADD CONSTRAINT `fk_doctors_specialization` FOREIGN KEY (`specialization_id`) REFERENCES `specializations` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_doctors_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `prescriptions`
--
ALTER TABLE `prescriptions`
  ADD CONSTRAINT `fk_prescriptions_appointment` FOREIGN KEY (`appointment_id`) REFERENCES `appointments` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
