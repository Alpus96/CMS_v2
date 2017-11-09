-- phpMyAdmin SQL Dump
-- version 4.6.4
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Oct 26, 2017 at 12:55 PM
-- Server version: 5.6.31
-- PHP Version: 7.1.7

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `cms`
--

-- --------------------------------------------------------

--
-- Table structure for table `CONTENTS`
--

CREATE TABLE `CONTENTS` (
  `ID` int(11) NOT NULL,
  `CONTENT_TEXT` text NOT NULL,
  `MARKER` varchar(64) NOT NULL,
  `AUTHOR` varchar(128) NOT NULL,
  `TIMESTAMP_CREATED` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `TIMESTAMP_EDITED` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `DELETED_CONTENTS`
--

CREATE TABLE `DELETED_CONTENTS` (
  `ID` int(11) NOT NULL,
  `CONTENT_TEXT` text NOT NULL,
  `MARKER` varchar(128) NOT NULL,
  `DELETED` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `TOKEN_STORE`
--

CREATE TABLE `TOKEN_STORE` (
  `ID` varchar(64) NOT NULL,
  `TOKEN` text NOT NULL,
  `SALT` varchar(1024) NOT NULL,
  `UNIX` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `TOKEN_STORE`
--

INSERT INTO `TOKEN_STORE` (`ID`, `TOKEN`, `SALT`, `UNIX`) VALUES
('admin', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ1c2VybmFtZSI6ImFkbWluIiwiZGlzcGxheV9uYW1lIjoiQWxleCBMLlAuIiwiaGFzaCI6IiQyeSQxMCRoTm5LWUxuVmhGNDdDN2JnOEEzZWd1UElmcWxSLkowYWUxVE1pUHBzSGtMMEE1SmNXTTZaUyIsInR5cGUiOjEsImxvY2tlZCI6MH0.DyJ3Z8lrBwkBX6W5X-up2UOwpcASbiJsKF29Y3zFCs0', 'Q08t97pGk32uDOH6C5Y0sXv0zT7uiN8jgh0wpCK0dqa69retLfwI39U2Hs3tJ81F', '2017-10-23 18:47:18');

-- --------------------------------------------------------

--
-- Table structure for table `USERS`
--

CREATE TABLE `USERS` (
  `USERNAME` varchar(64) NOT NULL,
  `DISPLAY_NAME` varchar(128) NOT NULL,
  `HASH` varchar(256) NOT NULL,
  `TYPE` int(1) NOT NULL,
  `LOCKED` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `USERS`
--

INSERT INTO `USERS` (`USERNAME`, `DISPLAY_NAME`, `HASH`, `TYPE`, `LOCKED`) VALUES
('admin', 'Alex L.P.', '$2y$10$hNnKYLnVhF47C7bg8A3eguPIfqlR.J0ae1TMiPpsHkL0A5JcWM6ZS', 1, 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `CONTENTS`
--
ALTER TABLE `CONTENTS`
  ADD PRIMARY KEY (`ID`,`TIMESTAMP_CREATED`);

--
-- Indexes for table `DELETED_CONTENTS`
--
ALTER TABLE `DELETED_CONTENTS`
  ADD PRIMARY KEY (`ID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `CONTENTS`
--
ALTER TABLE `CONTENTS`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `DELETED_CONTENTS`
--
ALTER TABLE `DELETED_CONTENTS`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
