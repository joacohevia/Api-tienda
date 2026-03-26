-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1:3306
-- Tiempo de generación: 26-03-2026 a las 13:48:07
-- Versión del servidor: 10.4.28-MariaDB
-- Versión de PHP: 8.2.4

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
  `id_categoria` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `descripcion` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `categorias`
--

INSERT INTO `categorias` (`id_categoria`, `nombre`, `descripcion`) VALUES
(2, 'verano edit', 'Ropa ligera para altas temperaturas edit'),
(3, 'invierno', 'Abrigos y prendas térmicas'),
(4, 'otonio', 'Ropa medianamente ligeta y a su vez abrigada'),
(5, 'deporte', 'Ropa técnica para actividad física'),
(6, 'formal', 'Vestimenta para ocasiones elegantes'),
(8, 'string', 'string');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedidos`
--

CREATE TABLE `pedidos` (
  `id_pedido` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `fecha` datetime DEFAULT current_timestamp(),
  `estado` enum('carrito','pendiente','pagado','enviado','cancelado') DEFAULT 'carrito',
  `total` decimal(10,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `pedidos`
--

INSERT INTO `pedidos` (`id_pedido`, `id_usuario`, `fecha`, `estado`, `total`) VALUES
(1, 2, '2026-03-15 10:30:00', 'pagado', 4499.49),
(2, 3, '2026-03-18 14:20:00', 'carrito', 0.00),
(3, 4, '2026-03-10 09:15:00', 'enviado', 5999.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedido_productos`
--

CREATE TABLE `pedido_productos` (
  `id_pedido_producto` int(11) NOT NULL,
  `id_pedido` int(11) NOT NULL,
  `id_variante` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `precio_unitario` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `pedido_productos`
--

INSERT INTO `pedido_productos` (`id_pedido_producto`, `id_pedido`, `id_variante`, `cantidad`, `precio_unitario`) VALUES
(1, 1, 1, 1, 2999.99),
(2, 1, 5, 1, 1499.50),
(3, 1, 1, 1, 2999.99),
(4, 1, 5, 1, 1499.50),
(5, 3, 8, 1, 5999.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `id_producto` int(11) NOT NULL,
  `id_categoria` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `descripcion` varchar(50) DEFAULT NULL,
  `precio_base` decimal(10,2) NOT NULL DEFAULT 0.00,
  `marca` varchar(20) DEFAULT NULL,
  `img` varchar(255) DEFAULT NULL,
  `activo` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`id_producto`, `id_categoria`, `nombre`, `descripcion`, `precio_base`, `marca`, `img`, `activo`) VALUES
(11, 2, 'Short de playa', 'Short ligero con secado rápido', 2999.99, 'OceanWear', 'img/short-playa.jpg', 1),
(12, 2, 'Remera manga corta', 'Algodón 100%, transpirable', 1499.50, 'BasicStyle', 'img/remera-verano.jpg', 1),
(13, 3, 'Buzo térmico', 'Con forro polar interior', 4599.00, 'WinterPro', 'img/buzo-invierno.jpg', 1),
(14, 5, 'Pantalón deportivo', 'Elasticado, ideal para running', 3299.99, 'ActiveFit', 'img/pantalon-deporte.jpg', 1),
(15, 6, 'Camisa formal', 'Corte slim, tela premium', 5999.00, 'Elegance', 'img/camisa-formal.jpg', 1),
(21, 4, 'strindededeedg', 'string', 0.01, 'string', 'string', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id_usuario` int(11) NOT NULL,
  `nombre` varchar(10) NOT NULL,
  `apellido` varchar(10) NOT NULL,
  `dni` int(10) DEFAULT NULL,
  `email` varchar(20) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `creado` datetime DEFAULT current_timestamp(),
  `rol` enum('admin','cliente') DEFAULT 'cliente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id_usuario`, `nombre`, `apellido`, `dni`, `email`, `password`, `creado`, `rol`) VALUES
(2, 'Ana', 'Lopez', 87654321, 'ana@email.com', '123456', '2026-03-17 15:57:37', 'cliente'),
(3, 'Carlos', 'Mendez', 11223344, 'carlos@email.com', '123456', '2026-03-17 15:57:37', 'cliente'),
(4, 'Maria', 'Sanchez', 55667788, 'maria@email.com', '123456', '2026-03-17 15:57:37', 'cliente'),
(7, 'Admin', 'Principal', 123456789, 'admin@tienda.com', '$2y$10$ps8wVtRLmwLmWP2Of...MuqtBuZLs7aEyLs7XpfIjUHi567LC8n8K', '2026-03-23 16:31:48', 'admin'),
(8, 'Juan', 'Pérez', 10111213, 'juan@gmail.com', '$2y$10$qY9/gW1sohf/UpPbCtaDuubgMD.76I0WJQpr3iZpfjvDPueolbqvW', '2026-03-25 10:14:30', 'cliente');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `variante_productos`
--

CREATE TABLE `variante_productos` (
  `id_variante` int(11) NOT NULL,
  `id_producto` int(11) NOT NULL,
  `talle` varchar(1) DEFAULT NULL,
  `color` varchar(50) DEFAULT NULL,
  `precio` decimal(10,2) NOT NULL DEFAULT 0.00,
  `stock` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `variante_productos`
--

INSERT INTO `variante_productos` (`id_variante`, `id_producto`, `talle`, `color`, `precio`, `stock`) VALUES
(1, 11, 'M', 'azul', 2999.99, 15),
(2, 11, 'S', 'azul', 2999.99, 10),
(3, 11, 'M', 'negro', 3199.99, 8),
(4, 12, 'S', 'blanco', 1499.50, 20),
(5, 12, 'M', 'blanco', 1499.50, 25),
(6, 12, 'S', 'rojo', 1599.50, 12),
(7, 13, 'M', 'gris', 4599.00, 10),
(8, 13, 'S', 'negro', 4599.00, 5);

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
  MODIFY `id_categoria` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  MODIFY `id_pedido` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `pedido_productos`
--
ALTER TABLE `pedido_productos`
  MODIFY `id_pedido_producto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id_producto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `variante_productos`
--
ALTER TABLE `variante_productos`
  MODIFY `id_variante` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

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
