CREATE TABLE `tabla` (
  `id` int(11) NOT NULL COMMENT 'Clave primaria' AUTO_INCREMENT,
  `num` int(11) NOT NULL COMMENT 'Campo numérico' ,
  `texto` varchar(100) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Campo de texto, si len > 255 sera TEXT, sino VARCHAR' ,
  `fecha` datetime NOT NULL COMMENT 'Campo de fecha' ,
  `booleano` tinyint(1) NOT NULL COMMENT 'Campo booleano, true/false' ,
  `created_at` datetime NOT NULL COMMENT 'Fecha de creación del registro' ,
  `updated_at` datetime NOT NULL COMMENT 'Fecha de última modificación del registro' ,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;


