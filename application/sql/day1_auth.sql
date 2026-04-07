-- Day 1 authentication schema for the CI3 assessment

CREATE TABLE IF NOT EXISTS `users` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(100) NOT NULL,
    `email` VARCHAR(255) NOT NULL,
    `password` VARCHAR(255) NOT NULL,
    `api_token` VARCHAR(64) DEFAULT NULL,
    `role` ENUM('admin', 'user') NOT NULL DEFAULT 'user',
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_users_email` (`email`),
    UNIQUE KEY `uk_users_api_token` (`api_token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Seed admin account for assessment/demo
-- Email: admin@example.com
-- Password: Admin@123
INSERT INTO `users` (`name`, `email`, `password`, `role`, `created_at`)
VALUES ('Portal Admin', 'admin@example.com', '$2y$12$adnjxOewsnSJZ9qNMPOVF.Lucv8Fmsoxjr3whBgzeLUQypTZ5QxVa', 'admin', NOW())
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);
