
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


CREATE TABLE `lectures` (
  `id` int(11) NOT NULL,
  `message` longtext DEFAULT NULL,
  `audio_url` varchar(255) DEFAULT NULL,
  `teacher_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `chapter` int(11) NOT NULL,
  `class` varchar(50) NOT NULL,
  `subject` enum('English','Wiskunde A','Wiskunde B','Wiskunde','Natuurkunde','Biology','Scheikunde','Beco','Nederlands','NT2','Spaans','Duits','Geschiedenis','PHE','ECO','I&S') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `school_email` varchar(100) NOT NULL,
  `class` varchar(10) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `subject` enum('English','Wiskunde A','Wiskunde B','Wiskunde','Natuurkunde','Biology','Scheikunde','Beco','Nederlands','NT2','Spaans','Duits','Geschiedenis','PHE','ECO','I&S') DEFAULT NULL,
  `role` varchar(20) NOT NULL DEFAULT 'student'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

ALTER TABLE `lectures`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `lectures`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;
COMMIT;

