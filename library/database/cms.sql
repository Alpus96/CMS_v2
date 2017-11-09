-- phpMyAdmin SQL Dump
-- version 4.6.4
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Oct 13, 2017 at 08:22 AM
-- Server version: 5.6.31
-- PHP Version: 5.6.30

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
-- Table structure for table `ACTIVE_USERS`
--

CREATE TABLE `ACTIVE_USERS` (
  `ID` int(11) NOT NULL,
  `USERNAME` varchar(64) NOT NULL,
  `TOKEN` varchar(512) NOT NULL,
  `TIMESTAMP` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

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
-- Table structure for table `USERS`
--

CREATE TABLE `USERS` (
  `ID` int(11) NOT NULL,
  `USERNAME` varchar(64) NOT NULL,
  `AUTHOR_NAME` varchar(128) NOT NULL,
  `HASH` varchar(256) NOT NULL,
  `TYPE` int(1) NOT NULL,
  `LOCKED` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `USERS`
--

INSERT INTO `USERS` (`ID`, `USERNAME`, `AUTHOR_NAME`, `HASH`, `TYPE`, `LOCKED`) VALUES
(4, 'admin', 'Alexander', '$2y$10$vhjXzuNgpIupOMMd9YAGTODd5.bvmg/4NUL5JKOOLDNuQXtew85bC', 1, 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `ACTIVE_USERS`
--
ALTER TABLE `ACTIVE_USERS`
  ADD PRIMARY KEY (`ID`);

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
-- Indexes for table `USERS`
--
ALTER TABLE `USERS`
  ADD PRIMARY KEY (`ID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `ACTIVE_USERS`
--
ALTER TABLE `ACTIVE_USERS`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=308;
--
-- AUTO_INCREMENT for table `CONTENTS`
--
ALTER TABLE `CONTENTS`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;
--
-- AUTO_INCREMENT for table `DELETED_CONTENTS`
--
ALTER TABLE `DELETED_CONTENTS`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;
--
-- AUTO_INCREMENT for table `USERS`
--
ALTER TABLE `USERS`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
