CREATE TABLE IF NOT EXISTS plisio_order (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `order_id` INT(11) NOT NULL,
    `plisio_invoice_id` VARCHAR(40),
    `amount` VARCHAR(40) DEFAULT '',
    `pending_amount` VARCHAR(40) DEFAULT '',
    `wallet_hash` VARCHAR(120) DEFAULT '',
    `psys_cid` VARCHAR(10) DEFAULT '',
    `currency` VARCHAR(10) DEFAULT '',
    `status` VARCHAR(10) DEFAULT 'new',
    `source_currency` VARCHAR(10) DEFAULT '',
    `source_rate` VARCHAR(40) DEFAULT '',
    `expire_utc` DATETIME DEFAULT NULL,
    `qr_code` BLOB DEFAULT NULL,
    `confirmations` TINYINT(2) DEFAULT 0,
    `expected_confirmations` TINYINT(2) DEFAULT 0,
    `tx_urls` TEXT DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT COLLATE=utf8_general_ci;