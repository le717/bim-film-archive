-- Create a new table to store film advisories
CREATE TABLE `films_advisories` (
  `film_id` INT(12) UNSIGNED NOT NULL,
  `sex` ENUM('0','1','2','3') NOT NULL DEFAULT '0',
  `language` ENUM('0','1','2','3') NOT NULL DEFAULT '0',
  `violence` ENUM('0','1','2','3') NOT NULL DEFAULT '0',
  PRIMARY KEY (`film_id`)
)
COLLATE='utf8mb4_general_ci'
ENGINE=InnoDB;

-- Remove all null values from the existing data
UPDATE `films`
SET `warn_sex` = 0
WHERE `warn_sex` IS NULL;

UPDATE `films`
SET `warn_lang` = 0
WHERE `warn_lang` IS NULL;

UPDATE `films`
SET `warn_vio` = 0
WHERE `warn_vio` IS NULL;

-- Copy the ratings into the new table
INSERT INTO `films_advisories`(`film_id`, `language`, `sex`, `violence`)
SELECT `id`, CAST(`warn_lang` AS CHAR), CAST(`warn_sex` AS CHAR), CAST(`warn_vio` AS CHAR)
FROM `films`
ORDER BY `films`.`id`;
