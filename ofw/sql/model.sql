/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;

CREATE TABLE `tag` (
  `id` INT(11) NOT NULL AUTO_INCREMENT COMMENT 'Id único de cada tag',
  `name` VARCHAR(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Nombre de la tag',
  `id_user` INT(11) NOT NULL COMMENT 'Id del usuario',
  `created_at` DATETIME NOT NULL COMMENT 'Fecha de creación del registro',
  `updated_at` DATETIME NULL COMMENT 'Fecha de última modificación del registro',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


CREATE TABLE `user` (
  `id` INT(11) NOT NULL AUTO_INCREMENT COMMENT 'Id único de un usuario',
  `user` VARCHAR(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Nombre de usuario',
  `pass` VARCHAR(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Contraseña del usuario',
  `num_photos` INT(11) NOT NULL DEFAULT '0' COMMENT 'Número de fotos de un usuario',
  `score` FLOAT NOT NULL DEFAULT '0' COMMENT 'Puntuación del usuario',
  `active` TINYINT(1) NOT NULL DEFAULT '1' COMMENT 'Usuario activo 1 o no 0',
  `last_login` DATETIME NOT NULL COMMENT 'Fecha de la última vez que inició sesión',
  `notes` TEXT NOT NULL DEFAULT '' COMMENT 'Notas sobre el usuario',
  `created_at` DATETIME NOT NULL COMMENT 'Fecha de creación del registro',
  `updated_at` DATETIME NULL COMMENT 'Fecha de última modificación del registro',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


CREATE TABLE `photo` (
  `id` INT(11) NOT NULL AUTO_INCREMENT COMMENT 'Id único de cada foto',
  `id_user` INT(11) NOT NULL COMMENT 'Id del usuario',
  `ext` VARCHAR(5) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Extensión de la foto',
  `created_at` DATETIME NOT NULL COMMENT 'Fecha de creación del registro',
  `updated_at` DATETIME NULL COMMENT 'Fecha de última modificación del registro',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


CREATE TABLE `photo_tag` (
  `id_photo` INT(11) NOT NULL COMMENT 'Id de la foto',
  `id_tag` INT(11) NOT NULL COMMENT 'Id de la tag',
  `created_at` DATETIME NOT NULL COMMENT 'Fecha de creación del registro',
  PRIMARY KEY (`id_photo`,`id_tag`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


ALTER TABLE `tag`
  ADD KEY `fk_tag_user_idx` (`id_user`),
  ADD CONSTRAINT `fk_tag_user` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;




ALTER TABLE `photo`
  ADD KEY `fk_photo_user_idx` (`id_user`),
  ADD CONSTRAINT `fk_photo_user` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;


ALTER TABLE `photo_tag`
  ADD KEY `fk_photo_tag_photo_idx` (`id_photo`),
  ADD KEY `fk_photo_tag_tag_idx` (`id_tag`),
  ADD CONSTRAINT `fk_photo_tag_photo` FOREIGN KEY (`id_photo`) REFERENCES `photo` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_photo_tag_tag` FOREIGN KEY (`id_tag`) REFERENCES `tag` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;


/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
