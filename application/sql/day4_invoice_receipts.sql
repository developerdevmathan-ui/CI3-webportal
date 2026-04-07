-- Day 4 invoice and receipt schema

CREATE TABLE IF NOT EXISTS `invoices` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `order_id` INT UNSIGNED NOT NULL,
    `invoice_number` VARCHAR(30) NOT NULL,
    `amount` DECIMAL(10,2) NOT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_invoices_order_id` (`order_id`),
    UNIQUE KEY `uk_invoices_invoice_number` (`invoice_number`),
    CONSTRAINT `fk_invoices_order_id` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `receipts` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `payment_id` INT UNSIGNED NOT NULL,
    `receipt_number` VARCHAR(30) NOT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_receipts_payment_id` (`payment_id`),
    UNIQUE KEY `uk_receipts_receipt_number` (`receipt_number`),
    CONSTRAINT `fk_receipts_payment_id` FOREIGN KEY (`payment_id`) REFERENCES `payments` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
