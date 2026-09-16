-- phpMyAdmin SQL Dump

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


-- --------------------------------------------------------

--
-- Table structure for table `sb_links`
--

CREATE TABLE `sb_links` (
  `id` int NOT NULL,
  `public_id` varchar(16) COLLATE utf8mb4_general_ci NOT NULL,
  `public_key` text COLLATE utf8mb4_general_ci NOT NULL,
  `password_hash` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `auth_salt` varchar(32) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `message_ttl_seconds` int UNSIGNED DEFAULT NULL COMMENT 'Self-destruct timer applied to new messages sent to this link, in seconds. NULL = off.',
  `account_ttl_seconds` int UNSIGNED DEFAULT NULL COMMENT 'Delete this link (and its messages) after this many seconds of owner inactivity. NULL = off.',
  `last_accessed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Updated whenever the owner checks their inbox — resets the inactivity timer above.',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sb_messages`
--

CREATE TABLE `sb_messages` (
  `id` int NOT NULL,
  `link_public_id` varchar(16) COLLATE utf8mb4_general_ci NOT NULL,
  `encrypted_content` text COLLATE utf8mb4_general_ci NOT NULL,
  `expires_at` timestamp NULL DEFAULT NULL COMMENT 'Self-destruct time for this message, computed at insert time from the link''s message_ttl_seconds. NULL = never expires.',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `sb_links`
--
ALTER TABLE `sb_links`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `public_id` (`public_id`),
  ADD KEY `idx_last_accessed` (`last_accessed_at`);

--
-- Indexes for table `sb_messages`
--
ALTER TABLE `sb_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `link_public_id` (`link_public_id`),
  ADD KEY `idx_expires_at` (`expires_at`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `sb_links`
--
ALTER TABLE `sb_links`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sb_messages`
--
ALTER TABLE `sb_messages`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `sb_messages`
--
ALTER TABLE `sb_messages`
  ADD CONSTRAINT `sb_messages_ibfk_1` FOREIGN KEY (`link_public_id`) REFERENCES `sb_links` (`public_id`) ON DELETE CASCADE;
COMMIT;

