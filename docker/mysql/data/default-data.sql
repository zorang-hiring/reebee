CREATE TABLE IF NOT EXISTS `sample_table` (
    `id` int unsigned NOT NULL AUTO_INCREMENT,
    `name` varchar(50) DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `sample_table` (`name`) VALUES ('world');