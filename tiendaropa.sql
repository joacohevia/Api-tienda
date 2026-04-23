-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Servidor: mysql
-- Tiempo de generación: 22-04-2026 a las 12:52:26
-- Versión del servidor: 8.0.43
-- Versión de PHP: 8.3.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `tiendaropa`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias`
--

CREATE TABLE `categorias` (
  `id_categoria` int NOT NULL,
  `nombre` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `descripcion` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `categorias`
--

INSERT INTO `categorias` (`id_categoria`, `nombre`, `descripcion`) VALUES
(2, 'verano', 'Ropa ligera para altas temperaturas '),
(3, 'invierno', 'Abrigos y prendas térmicas'),
(4, 'otonio', 'Ropa ligera y a su vez abrigada'),
(5, 'primavera', 'Ropa técnica para actividad física');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedidos`
--

CREATE TABLE `pedidos` (
  `id_pedido` int NOT NULL,
  `id_usuario` int NOT NULL,
  `fecha` datetime DEFAULT CURRENT_TIMESTAMP,
  `estado` enum('carrito','pendiente','pagado','enviado','cancelado') COLLATE utf8mb4_general_ci DEFAULT 'carrito',
  `total` decimal(10,2) NOT NULL DEFAULT '0.00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `pedidos`
--

INSERT INTO `pedidos` (`id_pedido`, `id_usuario`, `fecha`, `estado`, `total`) VALUES
(2, 23, '2026-04-22 12:38:40', 'enviado', 23998.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedido_productos`
--

CREATE TABLE `pedido_productos` (
  `id_pedido_producto` int NOT NULL,
  `id_pedido` int NOT NULL,
  `id_variante` int NOT NULL,
  `cantidad` int NOT NULL,
  `precio_unitario` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `pedido_productos`
--

INSERT INTO `pedido_productos` (`id_pedido_producto`, `id_pedido`, `id_variante`, `cantidad`, `precio_unitario`) VALUES
(2, 2, 5, 1, 11999.00),
(3, 2, 4, 1, 11999.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `id_producto` int NOT NULL,
  `id_categoria` int NOT NULL,
  `nombre` varchar(22) COLLATE utf8mb4_general_ci NOT NULL,
  `descripcion` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `precio_base` decimal(10,2) NOT NULL DEFAULT '0.00',
  `marca` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `img` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `activo` tinyint(1) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`id_producto`, `id_categoria`, `nombre`, `descripcion`, `precio_base`, `marca`, `img`, `activo`) VALUES
(11, 2, 'Remera sudadera', 'Prenda deportiva de alta calidad confeccionada con materiales tecnológicos avanzados \nque garantizan máxima comodidad y rendimiento. Perfecta para entrenamientos, correr \no cualquier actividad física. Diseño ergonómico con tecnología de transpirabilidad \n', 11999.00, 'OceanWear', 'https://images.unsplash.com/photo-1641580363896-17f37353efb2?q=80&w=387&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D', 1),
(12, 5, 'Cojunto deportivo larg', 'Prenda deportiva de alta calidad confeccionada con materiales tecnológicos avanzados \nque garantizan máxima comodidad y rendimiento. Perfecta para entrenamientos, correr \no cualquier actividad física. Diseño ergonómico con tecnología de transpirabilidad \n', 350000.00, 'BasicStyle', 'https://images.unsplash.com/photo-1770026136938-e83e02b89bbd?q=80&w=387&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D', 1),
(13, 5, 'Conjunto deportivo ', 'Prenda deportiva de alta calidad confeccionada con materiales tecnológicos avanzados \nque garantizan máxima comodidad y rendimiento. Perfecta para entrenamientos, correr \no cualquier actividad física. Diseño ergonómico con tecnología de transpirabilidad \n', 29996.00, 'WinterPro', 'https://images.unsplash.com/photo-1772450427240-775c8891797d?q=80&w=387&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D', 1),
(14, 2, 'Campera', 'Prenda deportiva de alta calidad confeccionada con materiales tecnológicos avanzados \nque garantizan máxima comodidad y rendimiento. Perfecta para entrenamientos, correr \no cualquier actividad física. Diseño ergonómico con tecnología de transpirabilidad \n', 3299.99, 'ActiveFit', 'https://media.istockphoto.com/id/2172167028/photo/a-beautiful-sportswoman-in-a-white-top-and-soft-blue-jacket-and-sports-leggings-stands-on-a.jpg?s=1024x1024&w=is&k=20&c=AC2g0vLllS2Do10OZIQhI04isdMBcZyJV3zz1LrOXmU=', 1),
(21, 4, 'strindededeedg', 'string', 0.01, 'string', 'string', 0),
(24, 5, 'Top deportivo', 'Prenda deportiva de alta calidad confeccionada con materiales tecnológicos avanzados \nque garantizan máxima comodidad y rendimiento. Perfecta para entrenamientos, correr \no cualquier actividad física. Diseño ergonómico con tecnología de transpirabilidad \n', 300000.00, 'XASXA', 'https://images.unsplash.com/photo-1770026137109-d60587762e8e?q=80&w=387&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D', 1),
(25, 5, 'Top deportivo', 'Prenda deportiva de alta calidad confeccionada con materiales tecnológicos avanzados \nque garantizan máxima comodidad y rendimiento. Perfecta para entrenamientos, correr \no cualquier actividad física. Diseño ergonómico con tecnología de transpirabilidad \n', 14998.00, 'Nike', 'https://images.unsplash.com/photo-1759476530777-8cb366bb6fc8?q=80&w=464&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D', 1),
(26, 5, 'Top deportivo', 'Prenda deportiva de alta calidad confeccionada con materiales tecnológicos avanzados \nque garantizan máxima comodidad y rendimiento. Perfecta para entrenamientos, correr \no cualquier actividad física. Diseño ergonómico con tecnología de transpirabilidad \n', 16000.00, 'Nike', 'https://plus.unsplash.com/premium_photo-1764336108660-2833d926939d?q=80&w=435&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D', 1),
(27, 5, 'Calza deportiva', 'Prenda deportiva de alta calidad confeccionada con materiales tecnológicos avanzados \nque garantizan máxima comodidad y rendimiento. Perfecta para entrenamientos, correr \no cualquier actividad física. Diseño ergonómico con tecnología de transpirabilidad \n', 20000.00, 'Nike', 'https://images.unsplash.com/photo-1584863495140-a320b13a11a8?q=80&w=373&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D', 1),
(28, 5, 'Remera termica', 'Prenda deportiva de alta calidad confeccionada con materiales tecnológicos avanzados \nque garantizan máxima comodidad y rendimiento. Perfecta para entrenamientos, correr \no cualquier actividad física. Diseño ergonómico con tecnología de transpirabilidad \n', 20000.00, 'Nike', 'https://media.istockphoto.com/id/671211766/photo/exercising-outdoors-in-the-morning.jpg?s=1024x1024&w=is&k=20&c=ttw4GPtNOki5zLNzznE6e4fSUoPMA0reHE_B8HbJq7Q=', 1),
(29, 2, 'Remera manga corta', 'Prenda deportiva de alta calidad confeccionada con materiales tecnológicos avanzados \nque garantizan máxima comodidad y rendimiento. Perfecta para entrenamientos, correr \no cualquier actividad física. Diseño ergonómico con tecnología de transpirabilidad \n', 12000.00, 'Nike', 'https://images.unsplash.com/photo-1619474221266-0e23ce248c60?q=80&w=464&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D', 1),
(30, 3, 'Buzo polar', 'Prenda deportiva de alta calidad confeccionada con materiales tecnológicos avanzados \nque garantizan máxima comodidad y rendimiento. Perfecta para entrenamientos, correr \no cualquier actividad física. Diseño ergonómico con tecnología de transpirabilidad \n', 49996.00, 'Nike', 'https://images.unsplash.com/photo-1725745185129-b8047b48d162?q=80&w=387&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D', 1),
(32, 2, 'zzz', 'sqwwqsqw', 218.00, 'sqwsqw', 'saax', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id_usuario` int NOT NULL,
  `nombre` varchar(10) COLLATE utf8mb4_general_ci NOT NULL,
  `apellido` varchar(10) COLLATE utf8mb4_general_ci NOT NULL,
  `dni` int NOT NULL,
  `celular` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `creado` datetime DEFAULT CURRENT_TIMESTAMP,
  `rol` enum('admin','cliente') COLLATE utf8mb4_general_ci DEFAULT 'cliente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id_usuario`, `nombre`, `apellido`, `dni`, `celular`, `email`, `password`, `creado`, `rol`) VALUES
(7, 'Admin', 'Principal', 123456789, '0', 'admin@tienda.com', '$2y$10$9.fCUNRrEmx8rBCNHuVy4uQYJYmGo2AhLJUMiCh6xeoGvZ1woCzQq', '2026-03-23 16:31:48', 'admin'),
(20, 'Juan', 'Perez', 33333333, '1122233355', 'juan@gmail.com', '$2y$10$7tFrTNnUMe/6XPR3ZuMfkeZYZkCd4yI.aLvByJ9RaGUajI4zxcaTu', '2026-04-21 23:54:54', 'cliente'),
(21, 'Ana', 'Lopez', 14585247, '2494564738', 'ana@gmail.com', '$2y$10$EpowtEJpM4YSnQIUOwuohOs65rMwGoMosBwlcPxxJQFIMfp8vNz22', '2026-04-21 23:58:38', 'cliente'),
(22, 'Julian', 'Hernandez', 9789124, '2244876907', 'julia@gmail.com', '$2y$10$rWv2gHFIkFItQQ.YVxCibOvo.ZtMiDQGRsg9DkSQOHllIqRvRt/me', '2026-04-21 23:59:42', 'cliente'),
(23, 'Francisco', 'Vazquez', 87534455, '5234366787', 'fran@gmail.com', '$2y$10$eDoI5y7ZjBBE4m26jjxCruambtrttNL/R6w2fg2QqOCs/U7fydvIa', '2026-04-22 02:05:51', 'cliente');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `variante_productos`
--

CREATE TABLE `variante_productos` (
  `id_variante` int NOT NULL,
  `id_producto` int NOT NULL,
  `talle` varchar(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `color` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `precio` decimal(10,2) NOT NULL DEFAULT '0.00',
  `stock` int DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `variante_productos`
--

INSERT INTO `variante_productos` (`id_variante`, `id_producto`, `talle`, `color`, `precio`, `stock`) VALUES
(1, 14, 'XXL', 'Rojo', 3400.00, 21),
(2, 11, 'M', 'Negro', 11999.00, 15),
(3, 11, 'L', 'Negro', 11999.00, 12),
(4, 11, 'S', 'Azul', 11999.00, 8),
(5, 11, 'M', 'Azul', 11999.00, 13),
(6, 11, 'L', 'Azul', 11999.00, 11),
(7, 11, 'M', 'Gris', 11999.00, 16),
(8, 11, 'L', 'Gris', 11999.00, 10),
(9, 12, 'S', 'Blanco', 350000.00, 8),
(10, 12, 'M', 'Blanco', 350000.00, 12),
(11, 12, 'L', 'Blanco', 350000.00, 9),
(12, 12, 'S', 'Negro', 350000.00, 7),
(13, 12, 'M', 'Negro', 350000.00, 11),
(14, 12, 'L', 'Negro', 350000.00, 8),
(15, 13, 'S', 'Gris', 29996.00, 13),
(16, 13, 'M', 'Gris', 29996.00, 18),
(17, 13, 'L', 'Gris', 29996.00, 14),
(18, 13, 'S', 'Negro', 29996.00, 11),
(19, 13, 'M', 'Negro', 29996.00, 15),
(20, 13, 'L', 'Negro', 29996.00, 12),
(21, 13, 'S', 'Rojo', 29996.00, 10),
(22, 13, 'M', 'Rojo', 29996.00, 14),
(23, 14, 'S', 'Gris', 3299.99, 15),
(24, 14, 'M', 'Gris', 3299.99, 20),
(25, 14, 'L', 'Gris', 3299.99, 18),
(26, 14, 'XXL', 'Gris', 3299.99, 6),
(27, 14, 'S', 'Rojo', 3399.99, 13),
(28, 14, 'M', 'Rojo', 3399.99, 18),
(29, 14, 'L', 'Rojo', 3399.99, 15),
(30, 14, 'XXL', 'Rojo', 3399.99, 5),
(31, 14, 'S', 'Azul', 3299.99, 14),
(32, 14, 'M', 'Azul', 3299.99, 19),
(33, 24, 'S', 'Rojo', 300000.00, 6),
(34, 24, 'M', 'Rojo', 300000.00, 10),
(35, 24, 'L', 'Rojo', 300000.00, 8),
(36, 24, 'S', 'Negro', 300000.00, 7),
(37, 24, 'M', 'Negro', 300000.00, 9),
(38, 25, 'S', 'Negro', 14998.00, 12),
(39, 25, 'M', 'Negro', 14998.00, 16),
(40, 25, 'L', 'Negro', 14998.00, 13),
(41, 25, 'S', 'Rosa', 14998.00, 10),
(42, 25, 'M', 'Rosa', 14998.00, 14),
(43, 27, 'M', 'Blanco', 20000.00, 16),
(44, 27, 'L', 'Blanco', 20000.00, 13),
(45, 27, 'M', 'Negro', 20000.00, 15),
(46, 27, 'L', 'Negro', 20000.00, 12),
(47, 27, 'M', 'Gris', 20000.00, 14),
(48, 27, 'L', 'Gris', 20000.00, 11),
(49, 29, 'M', 'Rojo', 12000.00, 17),
(50, 29, 'M', 'Blanco', 12000.00, 16),
(60, 11, 'S', 'Negro', 11999.00, 10),
(61, 30, 'XXL', 'Negro', 50.00, 3),
(62, 30, 'M', 'Negro', 49996.00, 5),
(63, 28, 'S', 'Gris', 20000.00, 23),
(64, 28, 'l', 'Negro', 20000.00, 2),
(65, 28, 'xxl', 'amarillo', 20000.00, 4);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id_categoria`);

--
-- Indices de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  ADD PRIMARY KEY (`id_pedido`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `pedido_productos`
--
ALTER TABLE `pedido_productos`
  ADD PRIMARY KEY (`id_pedido_producto`),
  ADD KEY `id_pedido` (`id_pedido`),
  ADD KEY `id_variante` (`id_variante`);

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`id_producto`),
  ADD KEY `id_categoria` (`id_categoria`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `dni` (`dni`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indices de la tabla `variante_productos`
--
ALTER TABLE `variante_productos`
  ADD PRIMARY KEY (`id_variante`),
  ADD KEY `id_producto` (`id_producto`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id_categoria` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  MODIFY `id_pedido` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `pedido_productos`
--
ALTER TABLE `pedido_productos`
  MODIFY `id_pedido_producto` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id_producto` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT de la tabla `variante_productos`
--
ALTER TABLE `variante_productos`
  MODIFY `id_variante` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=66;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `pedidos`
--
ALTER TABLE `pedidos`
  ADD CONSTRAINT `pedidos_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`);

--
-- Filtros para la tabla `pedido_productos`
--
ALTER TABLE `pedido_productos`
  ADD CONSTRAINT `pedido_productos_ibfk_1` FOREIGN KEY (`id_pedido`) REFERENCES `pedidos` (`id_pedido`),
  ADD CONSTRAINT `pedido_productos_ibfk_2` FOREIGN KEY (`id_variante`) REFERENCES `variante_productos` (`id_variante`);

--
-- Filtros para la tabla `productos`
--
ALTER TABLE `productos`
  ADD CONSTRAINT `productos_ibfk_1` FOREIGN KEY (`id_categoria`) REFERENCES `categorias` (`id_categoria`);

--
-- Filtros para la tabla `variante_productos`
--
ALTER TABLE `variante_productos`
  ADD CONSTRAINT `variante_productos_ibfk_1` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id_producto`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
