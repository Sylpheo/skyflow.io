-- phpMyAdmin SQL Dump
-- version 3.4.11.1deb2+deb7u1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Aug 14, 2015 at 12:09 PM
-- Server version: 5.5.41
-- PHP Version: 5.4.39-0+deb7u2

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

CREATE DATABASE IF NOT EXISTS skyflow;

GRANT ALL ON skyflow.* to 'skyflow'@'localhost' IDENTIFIED BY 'skyflow';

--
-- Database: `exacttarget`
--

USE 'skyflow';

-- --------------------------------------------------------

--
-- Table structure for table `event`
--

CREATE TABLE IF NOT EXISTS `event` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `id_user` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_user` (`id_user`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=15 ;

--
-- Dumping data for table `event`
--

INSERT INTO `event` (`id`, `name`, `description`, `id_user`) VALUES
(12, 'Modificiation', 'test', 1),
(13, 'nom', 'nom', 1),
(14, 'remerciements', 'remerciements', 1);

-- --------------------------------------------------------

--
-- Table structure for table `flow`
--

CREATE TABLE IF NOT EXISTS `flow` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `class` varchar(255) NOT NULL,
  `documentation` varchar(255) NOT NULL,
  `id_user` int(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_user` (`id_user`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `flow`
--

INSERT INTO `flow` (`id`, `name`, `class`, `documentation`, `id_user`) VALUES
(1, 'FLoooow', 'test', '<p>Modificaion</p>', 1),
(2, 'Flow_nom', 'test2', '<p><strong><span style="background-color:#FF0000">Documentation du Flow_Test2 ! </span></strong></p>', 1),
(3, 'Mail_remerciement', 'mail_remerciements', '<p>Flow d&#39;envoi d&#39;un mail de remerciement pour la participation + wave</p>', 1),
(4, 'testModification', 'test', '<p>Modification de la documentation !!!</p>', 1);

-- --------------------------------------------------------

--
-- Table structure for table `mapping`
--

CREATE TABLE IF NOT EXISTS `mapping` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `id_event` int(255) NOT NULL,
  `id_flow` int(255) NOT NULL,
  `id_user` int(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_event` (`id_event`,`id_flow`,`id_user`),
  KEY `id_flow` (`id_flow`),
  KEY `id_user` (`id_user`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

--
-- Dumping data for table `mapping`
--

INSERT INTO `mapping` (`id`, `id_event`, `id_flow`, `id_user`) VALUES
(6, 12, 1, 1),
(3, 13, 2, 1),
(4, 14, 3, 1);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `salt` varchar(255) NOT NULL,
  `role` varchar(255) NOT NULL,
  `clientid` varchar(255) DEFAULT NULL,
  `clientsecret` varchar(255) DEFAULT NULL,
  `waveid` varchar(255) DEFAULT NULL,
  `wavesecret` varchar(255) DEFAULT NULL,
  `wavelogin` varchar(255) DEFAULT NULL,
  `wavepassword` varchar(255) DEFAULT NULL,
  `skyflowtoken` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=8 ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `salt`, `role`, `clientid`, `clientsecret`, `waveid`, `wavesecret`, `wavelogin`, `wavepassword`, `skyflowtoken`) VALUES
(1, 'elodie', 'OCAbgggk0Kz6NAM5jYkDfr9G2V0QqSBomOcudQGFkOlEU05PFnVL6jxtANk8k8wGc22UrTbkzqGXUasnpxRCQA==', '271220c3ca55281bd4e5a0c', 'ROLE_ADMIN', 'e4sz5qpc7tzzmdg7xbaezjwy', 'aYnxNchsNBjjYAPu78KW22Xr', '3MVG9SemV5D80oBcbOkdI2WCxIIA5fZMPI3ZDTZBBU_6E6zc8Z5wKZ4DCh.bPDxBEV4PocUnC3ELl70tjOSof', '8180025755972035170', 'pierre.lecointre@sylpheo.dev', 'easy1234NWu2pdAhMFXl6KyyHPu5YDhy', '1e6238c22e407d1dc0f4238917bb74be'),
(2, 'test', 'nsdQPFZZNORrj+kAVAeRfWLCo0x26FtcO6nFUkwO/Kc75XS8ze0WsqmP2rrQJipn4WJhB6xhQYoMhot0RYOeGg==', '8a3325f01227842b5a2901f', 'ROLE_ADMIN', '', '', '', '', '', '', ''),
(3, 'test2', '4xIw9h2SytzuJaC4xf3fMSQGNabe9Tsy3CjS199tN4WLfJbHjuTVi0mb0glhSXteovVT+xTqa3dsomQdhuZ3CA==', '7ebdae62c8832b771a0c8ea', 'ROLE_ADMIN', '', '', '', '', '', '', ''),
(4, 'a', '/UWth6eJo7Jq+98NivAUUviWSxv+t53AUcfbHfhvudmSSqfFPtJ4MC0aqhKvuMK1nga1s3jVHzsB+GSvdGnD+w==', '074f8e76b1a26a54ffe46ea', 'ROLE_ADMIN', 'gyihœv', 'hvkb7', '', '', '', '', '5581488c46115'),
(5, 'swiffer', '2bMEtdEwg9yQRicz6XO3tjiCAMKhDKZRj0Tlgg0MYzbwxk1Zmxl/ppMu6t3GIx9wj9xBHfE3aBaL/hlasXVqAQ==', '7930c71afc2298731bfbb3f', 'ROLE_ADMIN', NULL, NULL, NULL, NULL, NULL, NULL, ''),
(6, 'b', 'mfm6GOzEkSy8ZYihRbQZGO0tgKsUpfjYCizUcUKFMWAoO/46otiRXVVw6RhR162t8QI48deZsN9Pvi2rVKy3mw==', '630337d005271c07b91c554', 'ROLE_ADMIN', NULL, NULL, NULL, NULL, NULL, NULL, '8565087516cf00b4ca59098431c53987'),
(7, 'b', 'r85YNm6TGH1sSNSvy8a9/lHzh9HDUT1gQQZASRjQE6crYGLe0FSaVVIp2WiuBjv2vNQkrm8HZi83R7y2zpAUmA==', '649aa4fd2ee2078b35a95b0', 'ROLE_ADMIN', NULL, NULL, NULL, NULL, NULL, NULL, '811e1999ddf84db1a76b641e5fb71c3d');

-- --------------------------------------------------------

--
-- Table structure for table `wave_request`
--

CREATE TABLE IF NOT EXISTS `wave_request` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `request` varchar(255) NOT NULL,
  `id_user` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_user` (`id_user`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `wave_request`
--

INSERT INTO `wave_request` (`id`, `request`, `id_user`) VALUES
(1, 'q = load "0FbB00000005KPEKA2/0FcB00000005W4tKAE";q = filter q by ''Email'' in ["e.lodie62@hotmail.fr"];q = foreach q generate ''FirstName'' as ''FirstName'',''LastName'' as ''LastName'';', 1);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `event`
--
ALTER TABLE `event`
  ADD CONSTRAINT `event_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`);

--
-- Constraints for table `flow`
--
ALTER TABLE `flow`
  ADD CONSTRAINT `flow_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `mapping`
--
ALTER TABLE `mapping`
  ADD CONSTRAINT `mapping_ibfk_1` FOREIGN KEY (`id_event`) REFERENCES `event` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `mapping_ibfk_2` FOREIGN KEY (`id_flow`) REFERENCES `flow` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `mapping_ibfk_3` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `wave_request`
--
ALTER TABLE `wave_request`
  ADD CONSTRAINT `wave_request_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;