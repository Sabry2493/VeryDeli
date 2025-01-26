-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 31-10-2024 a las 00:05:48
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `verydeli b`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `publicacion`
--

CREATE TABLE `publicacion` (
  `pu_id` int(11) NOT NULL,
  `pu_fk_u_id` int(11) NOT NULL,
  `pu_titulo` varchar(200) NOT NULL,
  `pu_fk_origen_provincia` int(11) NOT NULL,
  `pu_fk_origen_ciudad` varchar(250) NOT NULL,
  `pu_fk_origen_direccion` varchar(250) NOT NULL,
  `pu_fk_destino_provincia` int(11) NOT NULL,
  `pu_fk_destino_ciudad` varchar(250) NOT NULL,
  `pu_fk_destino_direccion` varchar(250) NOT NULL,
  `pu_volumen` int(10) NOT NULL,
  `pu_peso` int(10) NOT NULL,
  `pu_descripcion` varchar(300) NOT NULL,
  `pu_fecha` date DEFAULT NULL,
  `pu_imagen` varchar(250) NOT NULL,
  `pu_estado` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `publicacion`
--

INSERT INTO `publicacion` (`pu_id`, `pu_fk_u_id`, `pu_titulo`, `pu_fk_origen_provincia`, `pu_fk_origen_ciudad`, `pu_fk_origen_direccion`, `pu_fk_destino_provincia`, `pu_fk_destino_ciudad`, `pu_fk_destino_direccion`, `pu_volumen`, `pu_peso`, `pu_descripcion`, `pu_fecha`, `pu_imagen`, `pu_estado`) VALUES
(1, 14, 'Titulo 1', 1, '', '', 19, '', '', 20, 30, 'caja carton', NULL, '', '0'),
(2, 14, 'asdadas', 1, '', '', 19, '', '', 20, 30, 'llll', NULL, '', '8'),
(3, 15, 'paquete a cba', 19, '', '', 13, '', '', 99, 40, 'excelente estado, es fragil', NULL, '', '9'),
(4, 16, 'ayuda con mesa', 19, '', '', 6, '', '', 20, 10, 'jardinería', NULL, '', '10'),
(5, 15, '', 19, '', '', 13, '', '', 60, 7, 'electodomestico', NULL, '', '7'),
(6, 15, '', 19, '', '', 6, '', '', 4, 3, 'paquete pequeño', NULL, '', '0'),
(7, 15, '', 19, '', '', 6, '', '', 5, 3, 'paquete pequeño', NULL, '', '5'),
(8, 14, 'Quiero llevar un paquete', 19, 'San Luis', 'Italia 1840', 6, 'Mina Clavero', 'Ecuador 5438', 23, 123, 'Necesito llevar un paquete a cordoba', '2024-10-27', '', '0'),
(9, 14, 'Asda asdad', 17, 'alsiuba', 'ashvbsdnav', 1, 'asdqw123', 'asdvawvwe2123', 123, 132, 'wavawvsadvsdv', '2024-10-28', '', '0'),
(10, 14, 'dnrgndgfnrnr', 9, 'ascaqwweqw', 'qweadvadvv 123', 6, '1vab absfwga', 's asgwavdav zxcv ', 124214, 13123123, 'snrtndfgnfgngfndfgn', '2024-10-28', 'foto/GKQ0vkpaYAAdPvR.jpeg', '0');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `publicacion`
--
ALTER TABLE `publicacion`
  ADD PRIMARY KEY (`pu_id`),
  ADD KEY `fk_origen` (`pu_fk_origen_provincia`,`pu_fk_destino_provincia`),
  ADD KEY `fk_destino` (`pu_fk_destino_provincia`),
  ADD KEY `pu_pk_u_id` (`pu_fk_u_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `publicacion`
--
ALTER TABLE `publicacion`
  MODIFY `pu_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `publicacion`
--
ALTER TABLE `publicacion`
  ADD CONSTRAINT `publicacion_ibfk_1` FOREIGN KEY (`pu_fk_origen_provincia`) REFERENCES `argentina` (`arg_id`),
  ADD CONSTRAINT `publicacion_ibfk_2` FOREIGN KEY (`pu_fk_destino_provincia`) REFERENCES `argentina` (`arg_id`),
  ADD CONSTRAINT `publicacion_ibfk_3` FOREIGN KEY (`pu_fk_u_id`) REFERENCES `usuario` (`u_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
