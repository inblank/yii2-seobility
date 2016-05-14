/* Replace this file with actual dump of your database */

DROP TABLE IF EXISTS `model_seo`;
DROP TABLE IF EXISTS `model`;
DROP TABLE IF EXISTS `model2_seo`;
DROP TABLE IF EXISTS `model2`;

CREATE TABLE `model` (
  `id`   INT                   AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(250) NOT NULL DEFAULT ''
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_general_ci;

CREATE TABLE `model_seo` (
  `model_id`   INT  NOT NULL DEFAULT 0,
  `condition`  INT  NOT NULL DEFAULT 0,
  `title`      TEXT NOT NULL,
  `keywords`   TEXT NOT NULL,
  `description` TEXT NOT NULL,

  PRIMARY KEY (`model_id`, `condition`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_general_ci;

ALTER TABLE `model_seo`
  ADD CONSTRAINT `fk__model_seo__model` FOREIGN KEY (`model_id`) REFERENCES `model` (`id`)
  ON DELETE CASCADE
  ON UPDATE RESTRICT;

CREATE TABLE `model2` (
    `id`   INT                   AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(250) NOT NULL DEFAULT ''
)
    ENGINE = InnoDB
    DEFAULT CHARSET = utf8
    COLLATE = utf8_general_ci;

CREATE TABLE `model2_seo` (
  `model_id`   INT  NOT NULL DEFAULT 0,
  `condition`  INT  NOT NULL DEFAULT 0,
  `title`      TEXT NOT NULL,
  `keywords`   TEXT NOT NULL,
  `description` TEXT NOT NULL,

  PRIMARY KEY (`model_id`, `condition`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_general_ci;

ALTER TABLE `model2_seo`
  ADD CONSTRAINT `fk__model2_seo__model` FOREIGN KEY (`model_id`) REFERENCES `model2` (`id`)
  ON DELETE CASCADE
  ON UPDATE RESTRICT;
