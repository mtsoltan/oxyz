CREATE TABLE IF NOT EXISTS `users` (
  `id` SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `state` TINYINT UNSIGNED NOT NULL, -- ENUM: Disabled, Enabled
  `username` VARCHAR(20) NOT NULL,
  `passhash` CHAR(60) NOT NULL,
  `force_reset` INT UNSIGNED NOT NULL, -- Used to store recovery_key expiration time.
  `recovery_key` CHAR(32) NOT NULL,
  `last_login` INT UNSIGNED NOT NULL,
  `last_access` INT UNSIGNED NOT NULL,
  `ip` VARCHAR(255) NOT NULL,
  `session_id` CHAR(32),
  `class` TINYINT UNSIGNED NOT NULL, -- ENUM: Root, Admin, User(reserved)
  `state_text` TEXT NOT NULL,
  `permission` INT UNSIGNED NOT NULL,
  `create_timestamp` INT UNSIGNED NOT NULL,
  `update_timestamp` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `session_id` (`session_id`),
  KEY `last_access` (`last_access`),
  KEY `class` (`class`),
  KEY `state` (`state`),
  KEY `ip` (`ip`)
  ) ENGINE=InnoDB CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `files` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `state` TINYINT UNSIGNED NOT NULL, -- ENUM: Disabled, Enabled
  `entity_type` TINYINT UNSIGNED NOT NULL, -- ENUM: Product, Order, Gallery, Admin
  `entity_id` INT UNSIGNED NOT NULL, -- IDREF: Product / Order / 0
  `name` VARCHAR(255) NOT NULL,
  `salt` CHAR(16) NOT NULL,
  `hash` CHAR(64) NOT NULL,
  `salted_name` CHAR(64) NOT NULL,
  `ext` VARCHAR(255) NOT NULL,
  `size` INT UNSIGNED NOT NULL,
  `uploader_ip` VARCHAR(255) NOT NULL,
  `create_timestamp` INT UNSIGNED NOT NULL,
  `update_timestamp` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `entity_type` (`entity_type`),
  KEY `entity_id` (`entity_id`),
  UNIQUE KEY `salted_name` (`salted_name`),
  KEY `hash` (`hash`),
  KEY `ext` (`ext`),
  KEY `size` (`size`),
  KEY `uploader_ip` (`uploader_ip`)
  ) ENGINE=InnoDB CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `login_attempts` (
  `id` SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` SMALLINT UNSIGNED NOT NULL, -- IDREF: User
  `ip` VARCHAR(255) NOT NULL,
  `device` VARCHAR(255) NOT NULL,
  `browser` VARCHAR(255) NOT NULL,
  `create_timestamp` INT UNSIGNED NOT NULL,
  `update_timestamp` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `ip` (`ip`),
  KEY `create_timestamp` (`create_timestamp`)
  ) ENGINE=InnoDB CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `sessions` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `session_id` CHAR(32),
  `user_id` SMALLINT UNSIGNED NOT NULL,
  `ip` VARCHAR(255) NOT NULL,
  `device` VARCHAR(255) NOT NULL,
  `browser` VARCHAR(255) NOT NULL,
  `last_update` INT UNSIGNED NOT NULL,
  `create_timestamp` INT UNSIGNED NOT NULL,
  `update_timestamp` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `session_id` (`session_id`),
  KEY `user_id` (`user_id`)
  ) ENGINE=InnoDB CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `session_data` ( -- TODO: Check integrity and usage of this table.
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `session_id` CHAR(32),
  `last_update` INT UNSIGNED NOT NULL,
  `session_data` LONGTEXT,
  `browser` VARCHAR(255) NOT NULL,
  `create_timestamp` INT UNSIGNED NOT NULL,
  `update_timestamp` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `session_id` (`session_id`)
  ) ENGINE=InnoDB CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `products` (
  `id` SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `state` TINYINT UNSIGNED NOT NULL, -- ENUM: Disabled, Enabled
  `name` VARCHAR(255) NOT NULL,
  `description` TEXT NOT NULL,
  `image` VARCHAR(255) NOT NULL, -- Internal URL starting with /
  `type` TINYINT UNSIGNED NOT NULL, -- ENUM: Product, Service
  `price` SMALLINT UNSIGNED NOT NULL, -- 0 for Services
  `blame_id` SMALLINT UNSIGNED NOT NULL, -- IDREF: Users
  `note` TEXT,
  `create_timestamp` INT UNSIGNED NOT NULL,
  `update_timestamp` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
  ) ENGINE=InnoDB CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `keystore` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `state` TINYINT UNSIGNED NOT NULL, -- ENUM: Disabled, Enabled
  `key` TINYINT UNSIGNED NOT NULL,
  `label` VARCHAR(255) NOT NULL,
  `description` TEXT NOT NULL,
  `value` TEXT NOT NULL, -- Includes strings, numbers, or json serialized arrays.
  `entity_type` TINYINT UNSIGNED NOT NULL, -- ENUM: Site, Order (order vardata), Customer (phone dupes, ip dupes), Product (product vardata), Other
  `entity_id` INT UNSIGNED NOT NULL,
  `on_form` TINYINT UNSIGNED NOT NULL,
  `on_dashboard` TINYINT UNSIGNED NOT NULL,
  `create_timestamp` INT UNSIGNED NOT NULL,
  `update_timestamp` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `key` (`key`),
  KEY `entity_type` (`entity_type`)
  ) ENGINE=InnoDB CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `customers` (
  `id` SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `state` TINYINT UNSIGNED NOT NULL, -- ENUM: Disabled, Enabled
  `phone` CHAR(9) NOT NULL, -- Remove +201.
  `name` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `province` TINYINT UNSIGNED NOT NULL, -- 0 for not set. 1-28 are provinces.
  `flags` TINYINT UNSIGNED NOT NULL, -- BITMASK: bit 0: is_created, bit 1: is_saved, bit 2: is_acknowledged (reserves phone)
  `ip` VARCHAR(255) NOT NULL,
  `device` VARCHAR(255) NOT NULL,
  `browser` VARCHAR(255) NOT NULL,
  `note` TEXT,
  `create_timestamp` INT UNSIGNED NOT NULL,
  `update_timestamp` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `ip` (`ip`),
  KEY `phone` (`phone`),
  KEY `name` (`name`),
  KEY `email` (`email`)
  ) ENGINE=InnoDB CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `orders` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT, -- First 20 are reserved for special financials.
  `state` TINYINT UNSIGNED NOT NULL, -- ENUM: Pending, Cancelled, Finalized, Rolled
  `product_id` SMALLINT UNSIGNED NOT NULL, -- IDREF: Products
  `customer_id` SMALLINT UNSIGNED NOT NULL, -- IDREF: Customers
  `file_id` INT UNSIGNED NOT NULL, -- IDREF: Files. Repeated here for quick table listing.
  `ip` VARCHAR(255) NOT NULL,
  `amount` SMALLINT UNSIGNED NOT NULL,
  `customer_note` TEXT,
  `note` TEXT,
  `create_timestamp` INT UNSIGNED NOT NULL,
  `update_timestamp` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `customer_id` (`customer_id`),
  KEY `file_id` (`file_id`)
  ) ENGINE=InnoDB CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `financials` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `state` TINYINT UNSIGNED NOT NULL, -- ENUM: Pending, Cancelled, Finalized, Rolled
  `customer_id` SMALLINT UNSIGNED NOT NULL,
  `order_id` INT UNSIGNED NOT NULL, -- First 20 are reserved for special financials.
  `item` VARCHAR(255) NOT NULL,
  `item_amount` SMALLINT UNSIGNED NOT NULL,
  `transaction` DECIMAL(9,2) NOT NULL,
  `note` TEXT,
  `create_timestamp` INT UNSIGNED NOT NULL,
  `update_timestamp` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `customer_id` (`customer_id`),
  KEY `order_id` (`order_id`),
  KEY `transaction` (`transaction`)
  ) ENGINE=InnoDB CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `ip_blacklist` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `state` TINYINT UNSIGNED NOT NULL, -- ENUM: Disabled, Enabled
  `ip` VARCHAR(255) NOT NULL,
  `note` TEXT,
  `create_timestamp` INT UNSIGNED NOT NULL,
  `update_timestamp` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`id`)
  ) ENGINE=InnoDB CHARSET=utf8;
