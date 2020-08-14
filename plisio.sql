CREATE TABLE IF NOT EXISTS `plisio_order` (
  `plisio_invoice_id` varchar(40) DEFAULT NULL,
  `amount` varchar(40) DEFAULT '',
  `pending_amount` varchar(40) DEFAULT '',
  `wallet_hash` varchar(120) DEFAULT '',
  `psys_cid` varchar(10) DEFAULT '',
  `currency` varchar(10) DEFAULT '',
  `status` varchar(10) DEFAULT 'new',
  `source_currency` varchar(10) DEFAULT '',
  `source_rate` varchar(40) DEFAULT '',
  `expire_utc` datetime DEFAULT NULL,
  `qr_code` blob,
  `confirmations` tinyint(2) DEFAULT '0',
  `expected_confirmations` tinyint(2) DEFAULT '0',
  `tx_urls` text,
  PRIMARY KEY (`plisio_invoice_id`)
)