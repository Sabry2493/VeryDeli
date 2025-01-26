-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 06-11-2024 a las 21:32:30
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
-- Estructura de tabla para la tabla `denuncias`
--

CREATE TABLE `denuncias` (
  `de_id` int(11) NOT NULL,
  `de_fk_pu_id` int(11) NOT NULL,
  `de_fk_u_id` int(11) NOT NULL,
  `de_tags` varchar(50) NOT NULL,
  `de_mensaje` varchar(250) NOT NULL,
  `de_estado` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `denuncias`
--

INSERT INTO `denuncias` (`de_id`, `de_fk_pu_id`, `de_fk_u_id`, `de_tags`, `de_mensaje`, `de_estado`) VALUES
(1, 1, 14, 'Perfil Falso ', '                              El dueño de esta publicacion tiene un perfil falso, asi que puede ser una estafa', 'descartada'),
(2, 2, 14, 'Perfil Falso ', 'asdadasdsad', 'descartada'),
(3, 4, 14, 'Perfil Falso ', '                                asdasd', 'descartada');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `denuncias`
--
ALTER TABLE `denuncias`
  ADD PRIMARY KEY (`de_id`),
  ADD KEY `de_fk_pu_id` (`de_fk_pu_id`),
  ADD KEY `de_fk_u_id` (`de_fk_u_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `denuncias`
--
ALTER TABLE `denuncias`
  MODIFY `de_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `denuncias`
--
ALTER TABLE `denuncias`
  ADD CONSTRAINT `denuncias_ibfk_1` FOREIGN KEY (`de_fk_pu_id`) REFERENCES `publicacion` (`pu_id`),
  ADD CONSTRAINT `denuncias_ibfk_2` FOREIGN KEY (`de_fk_u_id`) REFERENCES `usuario` (`u_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
