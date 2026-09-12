-- SecretGate Production Database Schema

CREATE TABLE IF NOT EXISTS `sb_links` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `public_id` VARCHAR(16) NOT NULL,
  `public_key` TEXT NOT NULL,
  `password_hash` VARCHAR(255) NOT NULL,
  `message_ttl_seconds` INT UNSIGNED DEFAULT NULL COMMENT 'Self-destruct timer applied to new messages sent to this link, in seconds. NULL = off.',
  `account_ttl_seconds` INT UNSIGNED DEFAULT NULL COMMENT 'Delete this link (and its messages) after this many seconds of owner inactivity. NULL = off.',
  `last_accessed_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Updated whenever the owner checks their inbox — resets the inactivity timer above.',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `public_id` (`public_id`),
  KEY `idx_last_accessed` (`last_accessed_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `sb_messages` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `link_public_id` VARCHAR(16) NOT NULL,
  `encrypted_content` TEXT NOT NULL,
  `expires_at` TIMESTAMP NULL DEFAULT NULL COMMENT 'Self-destruct time for this message, computed at insert time from the link''s message_ttl_seconds. NULL = never expires.',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `link_public_id` (`link_public_id`),
  KEY `idx_expires_at` (`expires_at`),
  CONSTRAINT `sb_messages_ibfk_1` FOREIGN KEY (`link_public_id`) REFERENCES `sb_links` (`public_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;