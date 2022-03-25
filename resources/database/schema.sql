--
-- Table structure for table `users`
--
DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE (`email`)
);

--
-- Seed admin: admin@gmail.com, password: password 
--
INSERT INTO `users`(`username`,`email`,`password`) 
VALUES("Administrador","admin@domain.com","$2y$10$hbMFu2OtL60r4QA37bLSXOboZU5XM6Pd8f9UHp7JToYvttMItc0RS");