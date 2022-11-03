-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 03, 2022 at 04:10 AM
-- Server version: 10.4.24-MariaDB
-- PHP Version: 7.4.29

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `bellintani`
--

-- --------------------------------------------------------

--
-- Table structure for table `accounts`
--

CREATE TABLE `accounts` (
  `account_id` int(10) UNSIGNED NOT NULL,
  `account_name` varchar(255) NOT NULL,
  `account_username` varchar(255) NOT NULL,
  `account_passwd` varchar(255) NOT NULL,
  `account_reg_time` timestamp NOT NULL DEFAULT current_timestamp(),
  `account_level` varchar(255) NOT NULL,
  `account_enabled` tinyint(3) UNSIGNED NOT NULL DEFAULT 1,
  `modified` datetime DEFAULT NULL,
  `created` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `accounts`
--

INSERT INTO `accounts` (`account_id`, `account_name`, `account_username`, `account_passwd`, `account_reg_time`, `account_level`, `account_enabled`, `modified`, `created`) VALUES
(3, 'Admin', 'admin@admin.com', '$2y$10$A7yxjXVG8VjYuvLv6f4V1.gSm6VbYJdH95zuNE6yZ1G.F5vRsrFzq', '2020-10-18 02:11:55', 'admin', 1, '2022-11-02 16:56:54', '2021-03-15 02:01:44');

-- --------------------------------------------------------

--
-- Table structure for table `account_sessions`
--

CREATE TABLE `account_sessions` (
  `session_id` varchar(255) NOT NULL,
  `account_id` int(10) UNSIGNED NOT NULL,
  `login_time` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `account_sessions`
--

INSERT INTO `account_sessions` (`session_id`, `account_id`, `login_time`) VALUES
('2mucnv6mcojil6pu6akr1kk38e', 3, '2022-11-03 03:08:28');

-- --------------------------------------------------------

--
-- Table structure for table `imoveis`
--

CREATE TABLE `imoveis` (
  `imoveis_id` int(11) NOT NULL,
  `proprietarios_id` int(11) DEFAULT NULL,
  `codigo` text DEFAULT NULL,
  `responsavel` varchar(60) DEFAULT NULL,
  `endereco` varchar(80) DEFAULT NULL,
  `bairro` varchar(80) DEFAULT NULL,
  `numero` int(11) NOT NULL,
  `complemento` varchar(40) NOT NULL,
  `cep` varchar(20) DEFAULT NULL,
  `cidade` varchar(50) DEFAULT NULL,
  `estado` varchar(50) DEFAULT NULL,
  `area_util` varchar(50) DEFAULT NULL,
  `area_total` int(11) NOT NULL,
  `tipo` varchar(30) DEFAULT NULL,
  `tipo_residencial` varchar(40) NOT NULL,
  `locacao` decimal(10,2) DEFAULT NULL,
  `condominio` decimal(10,2) DEFAULT NULL,
  `tipo_venda` varchar(15) NOT NULL,
  `condominio_responsabilidade` tinyint(4) NOT NULL DEFAULT 0,
  `iptu` decimal(10,2) DEFAULT NULL,
  `seg_incendio_valor` decimal(10,2) DEFAULT NULL,
  `seg_incendio_valor_total` decimal(10,2) DEFAULT NULL,
  `preco_venda` decimal(10,2) NOT NULL,
  `isento` tinyint(4) NOT NULL DEFAULT 0,
  `observacao` text DEFAULT NULL,
  `image_path` varchar(128) CHARACTER SET utf8mb4 DEFAULT NULL,
  `youtube_url` text NOT NULL,
  `qtd_parcelas_iptu` int(11) NOT NULL,
  `parcela_atual_iptu` int(11) NOT NULL,
  `fundo_comissao` tinyint(4) NOT NULL DEFAULT 0,
  `opcoes_iptu` tinyint(4) NOT NULL DEFAULT 0,
  `taxa_iptu` tinyint(4) DEFAULT 0,
  `lat` varchar(50) NOT NULL,
  `lng` varchar(50) NOT NULL,
  `title_zap` text NOT NULL,
  `zap_id` text NOT NULL,
  `status_zap` varchar(20) NOT NULL DEFAULT 'not_sent',
  `feature` text NOT NULL,
  `torre_qtd` int(11) NOT NULL,
  `andares_qtd` int(11) NOT NULL,
  `garagens_qtd` int(11) NOT NULL,
  `banheiros_qtd` int(11) NOT NULL,
  `quartos_qtd` int(11) NOT NULL,
  `suites_qtd` int(11) NOT NULL,
  `enabled` int(11) NOT NULL DEFAULT 1,
  `created` datetime NOT NULL DEFAULT current_timestamp(),
  `modified` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `imoveis_images`
--

CREATE TABLE `imoveis_images` (
  `imoveis_image_id` int(11) NOT NULL,
  `image_path` text DEFAULT NULL,
  `imoveis_id` int(11) DEFAULT NULL,
  `enabled` int(11) NOT NULL DEFAULT 1,
  `created` datetime NOT NULL DEFAULT current_timestamp(),
  `modified` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `indices`
--

CREATE TABLE `indices` (
  `indices_id` int(11) NOT NULL,
  `tipo` varchar(50) DEFAULT NULL,
  `aliquota` decimal(10,2) NOT NULL,
  `data` date NOT NULL,
  `enabled` int(11) NOT NULL DEFAULT 1,
  `created` datetime NOT NULL DEFAULT current_timestamp(),
  `modified` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `leads`
--

CREATE TABLE `leads` (
  `leads_id` int(11) NOT NULL,
  `name` varchar(60) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(60) NOT NULL,
  `message_text` text NOT NULL,
  `enabled` int(11) NOT NULL DEFAULT 1,
  `created` datetime NOT NULL DEFAULT current_timestamp(),
  `modified` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `locatarios`
--

CREATE TABLE `locatarios` (
  `locatarios_id` int(11) NOT NULL,
  `customer_id` varchar(90) NOT NULL,
  `nome_completo` varchar(60) DEFAULT NULL,
  `cpf` varchar(50) DEFAULT NULL,
  `email` varchar(70) NOT NULL,
  `email_alternativo` varchar(70) NOT NULL,
  `tel` varchar(20) NOT NULL,
  `tel_2` varchar(20) NOT NULL,
  `taxa_despesa` tinyint(4) NOT NULL DEFAULT 0,
  `enabled` int(11) NOT NULL DEFAULT 1,
  `created` datetime NOT NULL DEFAULT current_timestamp(),
  `modified` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `page_permissions`
--

CREATE TABLE `page_permissions` (
  `page_permissions_id` int(11) NOT NULL,
  `role` varchar(255) NOT NULL,
  `dashboard` int(11) NOT NULL,
  `clientes` int(11) NOT NULL,
  `produtos` int(11) NOT NULL,
  `relatorios` int(11) NOT NULL,
  `administracao` int(11) NOT NULL,
  `gerenciar_usuarios` int(11) NOT NULL,
  `registros` int(11) NOT NULL,
  `modified` datetime DEFAULT NULL,
  `created` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `page_permissions`
--

INSERT INTO `page_permissions` (`page_permissions_id`, `role`, `dashboard`, `clientes`, `produtos`, `relatorios`, `administracao`, `gerenciar_usuarios`, `registros`, `modified`, `created`) VALUES
(2, 'admin', 1, 1, 1, 1, 1, 1, 1, NULL, '2021-03-09 10:44:28'),
(5, 'vendedor', 1, 1, 1, 1, 1, 1, 1, NULL, '2021-03-09 10:44:28'),
(6, 'tecnico', 1, 1, 1, 1, 1, 1, 1, NULL, '2021-03-09 10:44:28');

-- --------------------------------------------------------

--
-- Table structure for table `proprietarios`
--

CREATE TABLE `proprietarios` (
  `proprietarios_id` int(11) NOT NULL,
  `nome_completo` varchar(60) DEFAULT NULL,
  `dia_deposito` varchar(10) NOT NULL,
  `tipoPagamento` varchar(20) NOT NULL,
  `cpf` varchar(50) DEFAULT NULL,
  `taxa` decimal(10,2) NOT NULL,
  `banco` varchar(60) NOT NULL,
  `agencia` varchar(70) NOT NULL,
  `conta_corrente` varchar(90) NOT NULL,
  `pix` text NOT NULL,
  `endereco` varchar(60) DEFAULT NULL,
  `numero_endereco` varchar(20) NOT NULL,
  `cidade` varchar(60) NOT NULL,
  `estado` varchar(20) NOT NULL,
  `bairro` varchar(50) NOT NULL,
  `email` varchar(70) NOT NULL,
  `email_alternativo` varchar(70) NOT NULL,
  `tel` varchar(20) NOT NULL,
  `tel_2` varchar(20) NOT NULL,
  `tarifa_doc` tinyint(4) NOT NULL DEFAULT 0,
  `enabled` int(11) NOT NULL DEFAULT 1,
  `created` datetime NOT NULL DEFAULT current_timestamp(),
  `modified` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `accounts`
--
ALTER TABLE `accounts`
  ADD PRIMARY KEY (`account_id`);

--
-- Indexes for table `account_sessions`
--
ALTER TABLE `account_sessions`
  ADD PRIMARY KEY (`session_id`);

--
-- Indexes for table `imoveis`
--
ALTER TABLE `imoveis`
  ADD PRIMARY KEY (`imoveis_id`);

--
-- Indexes for table `imoveis_images`
--
ALTER TABLE `imoveis_images`
  ADD PRIMARY KEY (`imoveis_image_id`);

--
-- Indexes for table `indices`
--
ALTER TABLE `indices`
  ADD PRIMARY KEY (`indices_id`);

--
-- Indexes for table `leads`
--
ALTER TABLE `leads`
  ADD PRIMARY KEY (`leads_id`);

--
-- Indexes for table `locatarios`
--
ALTER TABLE `locatarios`
  ADD PRIMARY KEY (`locatarios_id`);

--
-- Indexes for table `page_permissions`
--
ALTER TABLE `page_permissions`
  ADD PRIMARY KEY (`page_permissions_id`);

--
-- Indexes for table `proprietarios`
--
ALTER TABLE `proprietarios`
  ADD PRIMARY KEY (`proprietarios_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `accounts`
--
ALTER TABLE `accounts`
  MODIFY `account_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `imoveis`
--
ALTER TABLE `imoveis`
  MODIFY `imoveis_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `imoveis_images`
--
ALTER TABLE `imoveis_images`
  MODIFY `imoveis_image_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `indices`
--
ALTER TABLE `indices`
  MODIFY `indices_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `leads`
--
ALTER TABLE `leads`
  MODIFY `leads_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `locatarios`
--
ALTER TABLE `locatarios`
  MODIFY `locatarios_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `proprietarios`
--
ALTER TABLE `proprietarios`
  MODIFY `proprietarios_id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
