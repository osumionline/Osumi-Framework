-- Osumi Framework

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `db_name`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `photo`
--

DROP TABLE IF EXISTS `photo`;
CREATE TABLE `photo` (
  `id` int(11) NOT NULL COMMENT 'Id único de cada foto',
  `id_user` int(11) NOT NULL COMMENT 'Id del usuario',
  `ext` varchar(5) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Extensión de la foto',
  `created_at` datetime NOT NULL COMMENT 'Fecha de creación del registro',
  `updated_at` datetime DEFAULT NULL COMMENT 'Fecha de última modificación del registro'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `photo`
--

INSERT INTO `photo` (`id`, `id_user`, `ext`, `created_at`, `updated_at`) VALUES
(1, 1, 'jpg', '2018-11-23 14:34:21', '2018-11-23 14:34:21'),
(2, 1, 'jpg', '2018-11-23 14:34:21', '2018-11-23 14:34:21');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `photo_tag`
--

DROP TABLE IF EXISTS `photo_tag`;
CREATE TABLE `photo_tag` (
  `id_photo` int(11) NOT NULL COMMENT 'Id de la foto',
  `id_tag` int(11) NOT NULL COMMENT 'Id de la tag',
  `created_at` datetime NOT NULL COMMENT 'Fecha de creación del registro'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `photo_tag`
--

INSERT INTO `photo_tag` (`id_photo`, `id_tag`, `created_at`) VALUES
(1, 1, '2018-11-23 14:34:21'),
(2, 2, '2018-11-23 14:34:21');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tag`
--

DROP TABLE IF EXISTS `tag`;
CREATE TABLE `tag` (
  `id` int(11) NOT NULL COMMENT 'Id único de cada tag',
  `name` varchar(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Nombre de la tag',
  `id_user` int(11) NOT NULL COMMENT 'Id del usuario',
  `created_at` datetime NOT NULL COMMENT 'Fecha de creación del registro',
  `updated_at` datetime DEFAULT NULL COMMENT 'Fecha de última modificación del registro'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `tag`
--

INSERT INTO `tag` (`id`, `name`, `id_user`, `created_at`, `updated_at`) VALUES
(1, 'Beach', 1, '2018-11-23 09:35:16', '2018-11-23 09:35:16'),
(2, 'Mountain', 1, '2018-11-23 09:35:16', '2018-11-23 09:35:16');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `user`
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `id` int(11) NOT NULL COMMENT 'Id único de un usuario',
  `user` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Nombre de usuario',
  `pass` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Contraseña del usuario',
  `num_photos` int(11) NOT NULL DEFAULT '0' COMMENT 'Número de fotos de un usuario',
  `score` float NOT NULL DEFAULT '0' COMMENT 'Puntuación del usuario',
  `active` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Usuario activo 1 o no 0',
  `last_login` datetime NOT NULL COMMENT 'Fecha de la última vez que inició sesión',
  `notes` text COLLATE utf8_unicode_ci NOT NULL COMMENT 'Notas sobre el usuario',
  `created_at` datetime NOT NULL COMMENT 'Fecha de creación del registro',
  `updated_at` datetime DEFAULT NULL COMMENT 'Fecha de última modificación del registro'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `user`
--

INSERT INTO `user` (`id`, `user`, `pass`, `num_photos`, `score`, `active`, `last_login`, `notes`, `created_at`, `updated_at`) VALUES
(1, 'igorosabel', 'qwertyuiop...', 2, 10.5, 1, '2018-11-23 10:35:06', 'Test user 1', '2018-11-23 09:35:16', '2018-11-23 09:35:16');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `photo`
--
ALTER TABLE `photo`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_photo_user_idx` (`id_user`);

--
-- Indices de la tabla `photo_tag`
--
ALTER TABLE `photo_tag`
  ADD PRIMARY KEY (`id_photo`,`id_tag`),
  ADD KEY `fk_photo_tag_photo_idx` (`id_photo`),
  ADD KEY `fk_photo_tag_tag_idx` (`id_tag`);

--
-- Indices de la tabla `tag`
--
ALTER TABLE `tag`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_tag_user_idx` (`id_user`);

--
-- Indices de la tabla `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `photo`
--
ALTER TABLE `photo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Id único de cada foto', AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `tag`
--
ALTER TABLE `tag`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Id único de cada tag', AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Id único de un usuario', AUTO_INCREMENT=2;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `photo`
--
ALTER TABLE `photo`
  ADD CONSTRAINT `fk_photo_user` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `photo_tag`
--
ALTER TABLE `photo_tag`
  ADD CONSTRAINT `fk_photo_tag_photo` FOREIGN KEY (`id_photo`) REFERENCES `photo` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_photo_tag_tag` FOREIGN KEY (`id_tag`) REFERENCES `tag` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `tag`
--
ALTER TABLE `tag`
  ADD CONSTRAINT `fk_tag_user` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;