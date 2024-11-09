-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 08-11-2024 a las 09:46:52
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `roa_order_chatbot`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `faqs`
--

CREATE TABLE `faqs` (
  `id` int(11) NOT NULL,
  `question` varchar(255) NOT NULL,
  `answer` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `faqs`
--

INSERT INTO `faqs` (`id`, `question`, `answer`) VALUES
(1, '¿Cómo puedo crear un pedido?', 'Para crear un pedido, dirígete al menú de pedidos y selecciona la opción \"Crear pedido\". Luego, ingresa los detalles del cliente y los productos que deseas pedir.'),
(2, '¿Cómo puedo editar un pedido?', 'Para editar un pedido, ve a la sección \"Editar pedido\", busca el pedido por cliente y fecha, y luego puedes modificar los detalles.'),
(3, '¿Cómo puedo eliminar un pedido?', 'Para eliminar un pedido, selecciona \"Eliminar pedido\", busca el pedido y confirma la eliminación.'),
(4, '¿Qué hago si un cliente no está registrado?', 'Si un cliente no está registrado, puedes agregarlo en la sección de \"Clientes\".'),
(5, '¿Cómo puedo ver los productos disponibles?', 'Los productos disponibles se encuentran en la sección \"Productos\", donde puedes consultar todos los productos y sus detalles.'),
(6, '¿Dónde consulto los pedidos creados?', 'Puedes consultar todos los pedidos creados en la sección de \"Pedidos\". Ahí podrás ver una lista de todos los pedidos junto con su estado y detalles.'),
(7, '¿Cómo edito un pedido?', 'Para editar un pedido, ve a la sección \"Editar pedido\". Busca el pedido por el nombre del cliente y la fecha, luego puedes cambiar los productos, la cantidad y otros detalles.'),
(8, '¿Cómo edito la información de un cliente?', 'Para editar la información de un cliente, ve a la sección \"Editar cliente\". Busca el cliente por nombre y podrás modificar los datos como dirección, teléfono, tipo de cliente, etc.'),
(9, '¿Cómo elimino un pedido?', 'Para eliminar un pedido, ve a la sección \"Eliminar pedido\", busca el pedido por el nombre del cliente y la fecha, y luego confirma la eliminación.'),
(10, '¿Qué hago si un cliente no está registrado?', 'Si un cliente no está registrado, puedes agregarlo fácilmente desde la sección \"Clientes\". Haz clic en \"Agregar Cliente\" y llena los datos requeridos.'),
(11, '¿Cómo puedo ver los productos disponibles?', 'Puedes consultar todos los productos disponibles en la sección \"Productos\". Ahí podrás ver los detalles de cada producto, como su precio y descripción.'),
(12, '¿Cómo cambio la contraseña de mi cuenta?', 'Para cambiar tu contraseña, ve a la configuración de tu cuenta y selecciona \"Cambiar Contraseña\". Debes ingresar tu contraseña actual y la nueva.'),
(13, '¿Cómo realizo un pedido sin stock de un producto?', 'Si un producto no está en stock, puedes realizar el pedido con la cantidad disponible o esperar a que el producto vuelva a estar disponible. Si el producto tiene un stock limitado, se indicará en el proceso de pedido.'),
(14, '¿Qué hago si necesito un reporte de ventas?', 'Para obtener un reporte de ventas, accede a la sección de \"Reportes\" y selecciona el tipo de reporte que necesitas. Puedes filtrar por fechas y otros parámetros.'),
(15, '¿Cómo puedo ver el historial de pedidos de un cliente?', 'Para ver el historial de pedidos de un cliente, busca al cliente en la sección \"Clientes\", luego selecciona el nombre del cliente y verás un listado con todos los pedidos realizados.');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `faqs`
--
ALTER TABLE `faqs`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `faqs`
--
ALTER TABLE `faqs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
