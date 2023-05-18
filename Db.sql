CREATE SCHEMA `yandex-parser`;

CREATE TABLE `yandex-parser`.`tracks` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `title` VARCHAR(255) NOT NULL DEFAULT 'Title',
    `album_id` INT NULL,
    `artist_id` INT NOT NULL,
    `duration` TIME NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`));

CREATE TABLE `yandex-parser`.`artists` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `title` VARCHAR(255) NOT NULL DEFAULT 'Artist name',
    `month_listeners` INT NULL DEFAULT 0,
    `followers` INT NULL DEFAULT 0,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`));

CREATE TABLE `yandex-parser`.`albums` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `title` VARCHAR(255) NOT NULL,
    `artist_id` INT NOT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`));

SET FOREIGN_KEY_CHECKS=0;

ALTER TABLE `yandex-parser`.`albums`
    ADD CONSTRAINT `albums_artist_id_foreign`
        FOREIGN KEY (`artist_id`)
            REFERENCES `yandex-parser`.`artists` (`id`)
            ON DELETE CASCADE
            ON UPDATE CASCADE;

ALTER TABLE `yandex-parser`.`tracks`
    ADD CONSTRAINT `tracks_artist_id_foreign`
        FOREIGN KEY (`artist_id`)
            REFERENCES `yandex-parser`.`artists` (`id`)
            ON DELETE CASCADE
            ON UPDATE CASCADE,
ADD CONSTRAINT `tracks_album_id_foreign`
  FOREIGN KEY (`album_id`)
  REFERENCES `yandex-parser`.`albums` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;
