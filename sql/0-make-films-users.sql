-- Create a new table to store director info
CREATE TABLE IF NOT EXISTS `films_users` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT(10) UNSIGNED NOT NULL,
  `user_name` VARCHAR(60) NOT NULL,
  `real_name` VARCHAR(60) NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  INDEX `user_id` (`user_id`),
  INDEX `real_name` (`real_name`)
)
COLLATE='utf8mb4_general_ci'
ENGINE=InnoDB;

-- Using data from the forums_users table, populate the new table
-- with the users that have directly posted in the directory
INSERT INTO `films_users` (`user_id`, `user_name`, `real_name`)
SELECT `id`, `username`, `realname`
FROM `forums_users`
WHERE `id` IN (
  SELECT DISTINCT `forums_users`.`id`
  FROM `films` INNER JOIN `forums_users`
    ON `films`.`user_id` = `forums_users`.`id`
  ORDER BY `forums_users`.`id` ASC
);

-- These are the IDs of users listed in the directory
-- but are not directors, and are missed by the last query.
-- We need these people too
INSERT INTO `films_users` (`user_id`, `user_name`, `real_name`)
SELECT `id`, `username`, `realname`
FROM `forums_users`
WHERE `id` IN (
SELECT DISTINCT `forums_users`.`id`
  FROM `forums_users` INNER JOIN films_castcrew
    ON forums_users.id = films_castcrew.user_id
  WHERE forums_users.id NOT IN (
  SELECT user_id FROM films_users
  )
  ORDER BY forums_users.id
);

-- Finally, if a user has no real/director's name defined,
-- their username into the real_name column.
-- This makes pulling the data _much_ easier
UPDATE `films_users`
SET `real_name` = (SELECT `user_name` FROM `forums_users` WHERE `real_name` IS NULL LIMIT 1)
WHERE `real_name` IS NULL;
