-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Värd: 127.0.0.1
-- Tid vid skapande: 14 maj 2025 kl 14:06
-- Serverversion: 10.4.32-MariaDB
-- PHP-version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Databas: `ultradb`
--

-- --------------------------------------------------------

--
-- Ersättningsstruktur för vy `first_performances`
-- (See below for the actual view)
--
CREATE TABLE `first_performances` (
`UnifiedID` mediumint(8) unsigned
,`FirstPerfID` mediumint(8) unsigned
);

-- --------------------------------------------------------

--
-- Tabellstruktur `tcountry`
--

CREATE TABLE `tcountry` (
  `Code` char(3) NOT NULL DEFAULT '',
  `ISO3166_1` char(2) CHARACTER SET ascii COLLATE ascii_general_ci DEFAULT NULL,
  `Active` char(1) DEFAULT 'N',
  `CountryNameDE` varchar(100) DEFAULT NULL,
  `CountryNameEN` varchar(100) DEFAULT NULL,
  `CountryNameFR` varchar(100) DEFAULT NULL,
  `CountryNameES` varchar(100) DEFAULT NULL,
  `CountryNameIT` varchar(100) DEFAULT NULL,
  `CountryNameRU` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `CountryNameZH` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `CountryNameJA` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Region` smallint(6) DEFAULT 1
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Tabellstruktur `tcup`
--

CREATE TABLE `tcup` (
  `EventID` mediumint(9) NOT NULL DEFAULT 0,
  `Cupname` varchar(100) NOT NULL DEFAULT '',
  `CupYear` varchar(10) NOT NULL DEFAULT '2017',
  `Pool` tinyint(1) DEFAULT NULL,
  `ScoreTab` varchar(15) DEFAULT NULL,
  `Slope_M` decimal(10,6) UNSIGNED DEFAULT NULL,
  `Offset_M` decimal(10,6) UNSIGNED DEFAULT NULL,
  `Slope_W` decimal(10,6) UNSIGNED DEFAULT NULL,
  `Offset_W` decimal(10,6) UNSIGNED DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Tabellstruktur `tevent`
--

CREATE TABLE `tevent` (
  `EventID` mediumint(8) UNSIGNED NOT NULL,
  `PartOf` mediumint(8) UNSIGNED DEFAULT 0,
  `Edition` varchar(12) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `EventName` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_german2_ci NOT NULL DEFAULT '',
  `PromOrg` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `Contact` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `Address` varchar(150) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `Phone` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `Email` varchar(150) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `URL` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `EventType` tinyint(4) DEFAULT NULL,
  `Startdate` datetime DEFAULT NULL,
  `Enddate` datetime DEFAULT NULL,
  `Length` varchar(40) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `Duration` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `AltitudeDiff` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `Postcode` varchar(10) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `City` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `Country` varchar(10) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `StartLoc` mediumint(8) UNSIGNED DEFAULT NULL,
  `CourseDesc` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `MoreInfo` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `TimeLimit` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `FieldLimit` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `Fee` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `IAULabel` char(1) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT 'N',
  `RecordProof` char(1) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT 'N',
  `FinisherM` smallint(5) UNSIGNED DEFAULT NULL,
  `FinisherW` smallint(5) UNSIGNED DEFAULT NULL,
  `ParentID` mediumint(8) UNSIGNED DEFAULT NULL,
  `Display` char(1) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'Y',
  `Results` char(1) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'N',
  `Diploma` char(1) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'N',
  `ResultSource` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `Comments` text CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `CreatedBy` varchar(25) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `CreatedOn` datetime DEFAULT NULL,
  `ChangedBy` varchar(25) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `ChangedOn` datetime DEFAULT NULL,
  `DisplayPrio` tinyint(1) DEFAULT 0,
  `BinCat` tinyint(4) DEFAULT 0,
  `NormLen` decimal(8,3) UNSIGNED DEFAULT 0.000,
  `MethodVersion` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci PACK_KEYS=0;

--
-- Trigger `tevent`
--
DELIMITER $$
CREATE TRIGGER `CalcNormLen` BEFORE UPDATE ON `tevent` FOR EACH ROW Begin  
    Set new.NormLen = 
        if(new.Length REGEXP '^[0-9. ]+mi', 
            Floor( REGEXP_SUBSTR (new.Length, '[0-9.]+') * 1609.344) / 1000, 
            REGEXP_SUBSTR (new.Length, '[0-9.]+') )   ;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `CalcNormLen2` BEFORE INSERT ON `tevent` FOR EACH ROW Begin  
    Set new.NormLen = 
        if(new.Length REGEXP '^[0-9. ]+mi', 
            Floor( REGEXP_SUBSTR (new.Length, '[0-9.]+') * 1609.344) / 1000, 
            REGEXP_SUBSTR (new.Length, '[0-9.]+') )   ;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Tabellstruktur `tevent_submit`
--

CREATE TABLE `tevent_submit` (
  `SubmID` mediumint(8) UNSIGNED NOT NULL,
  `EventID` mediumint(8) UNSIGNED DEFAULT NULL,
  `PartOf` mediumint(8) UNSIGNED DEFAULT NULL,
  `Edition` varchar(12) DEFAULT NULL,
  `EventName` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_german2_ci NOT NULL DEFAULT '',
  `PromOrg` varchar(100) DEFAULT NULL,
  `Contact` varchar(50) DEFAULT NULL,
  `Address` varchar(150) DEFAULT NULL,
  `Phone` varchar(50) DEFAULT NULL,
  `Email` varchar(150) DEFAULT NULL,
  `URL` varchar(255) DEFAULT NULL,
  `EventType` varchar(20) DEFAULT NULL,
  `Startdate` datetime DEFAULT NULL,
  `Enddate` datetime DEFAULT NULL,
  `Length` varchar(40) DEFAULT NULL,
  `Duration` varchar(20) DEFAULT NULL,
  `AltitudeDiff` varchar(20) DEFAULT NULL,
  `Postcode` varchar(10) DEFAULT NULL,
  `City` varchar(50) DEFAULT NULL,
  `Country` varchar(10) DEFAULT NULL,
  `StartLoc` mediumint(8) UNSIGNED DEFAULT NULL,
  `CourseDesc` varchar(255) DEFAULT NULL,
  `MoreInfo` varchar(255) DEFAULT NULL,
  `TimeLimit` varchar(20) DEFAULT NULL,
  `FieldLimit` varchar(20) DEFAULT NULL,
  `Fee` varchar(20) DEFAULT NULL,
  `IAULabel` char(1) DEFAULT 'N',
  `RecordProof` char(1) DEFAULT 'N',
  `ParentID` varchar(10) DEFAULT NULL,
  `Display` char(1) NOT NULL DEFAULT 'Y',
  `Comments` text DEFAULT NULL,
  `SubmitterEmail` varchar(150) DEFAULT NULL,
  `SubmType` tinyint(3) UNSIGNED DEFAULT 0 COMMENT '1=new,2=corr',
  `CreatedBy` varchar(25) DEFAULT NULL,
  `CreatedOn` datetime DEFAULT NULL,
  `ChangedBy` varchar(25) DEFAULT NULL,
  `ChangedOn` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Tabellstruktur `tjapcities`
--

CREATE TABLE `tjapcities` (
  `JapCityID` int(11) NOT NULL,
  `LocJPN` varchar(30) NOT NULL,
  `LocRoman` varchar(60) NOT NULL,
  `Source` varchar(25) CHARACTER SET ascii COLLATE ascii_general_ci DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Tabellstruktur `tjapnames`
--

CREATE TABLE `tjapnames` (
  `JapNameID` int(11) NOT NULL,
  `Hiragana` varchar(30) NOT NULL,
  `Kanji` varchar(20) NOT NULL,
  `Romaji` varchar(50) NOT NULL,
  `Type` varchar(30) DEFAULT NULL,
  `Annotation` varchar(250) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellstruktur `tlink`
--

CREATE TABLE `tlink` (
  `LinkID` mediumint(8) UNSIGNED NOT NULL,
  `RefersTo` mediumint(8) UNSIGNED NOT NULL,
  `URL` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `LinkDesc` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Display` char(1) DEFAULT 'P',
  `LinkType` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1=Link to event, 2=Link to person',
  `Comments` varchar(250) DEFAULT NULL,
  `ChangedOn` datetime DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;

-- --------------------------------------------------------

--
-- Tabellstruktur `tmergelog`
--

CREATE TABLE `tmergelog` (
  `MergeID` mediumint(8) UNSIGNED NOT NULL,
  `Kept` mediumint(8) UNSIGNED DEFAULT NULL,
  `Deleted` mediumint(8) UNSIGNED NOT NULL,
  `AffectedPerfs` smallint(5) UNSIGNED DEFAULT NULL,
  `MergedBy` varchar(20) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `MergedOn` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Tabellstruktur `tnames`
--

CREATE TABLE `tnames` (
  `PersonName` varchar(30) NOT NULL,
  `FirstCnt` smallint(5) UNSIGNED NOT NULL,
  `LastCnt` smallint(5) UNSIGNED NOT NULL,
  `OverallCnt` mediumint(9) NOT NULL,
  `FirstProb` float UNSIGNED DEFAULT 0,
  `LastProb` float UNSIGNED DEFAULT 0
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Tabellstruktur `tnames_hebr`
--

CREATE TABLE `tnames_hebr` (
  `HebrNameID` mediumint(9) NOT NULL,
  `Hebr_plain` varchar(30) NOT NULL COMMENT 'Hebrew w/o nikkud',
  `Hebr_nik` varchar(30) DEFAULT NULL COMMENT 'Hebrew incl. nikkud',
  `English` varchar(50) NOT NULL,
  `Engl_variant` varchar(250) DEFAULT NULL,
  `NameType` varchar(15) CHARACTER SET ascii COLLATE ascii_general_ci DEFAULT NULL,
  `Gender` varchar(5) CHARACTER SET ascii COLLATE ascii_general_ci DEFAULT NULL,
  `Source` varchar(150) CHARACTER SET ascii COLLATE ascii_general_ci DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellstruktur `tperformance`
--

CREATE TABLE `tperformance` (
  `PerfID` mediumint(8) UNSIGNED NOT NULL,
  `EventID` mediumint(8) UNSIGNED NOT NULL DEFAULT 0,
  `RankTotal` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `PersonID` mediumint(8) UNSIGNED NOT NULL DEFAULT 0,
  `PerfClub` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `TimeGross` time DEFAULT '00:00:00',
  `TimeNet` time DEFAULT '00:00:00',
  `Distance` decimal(7,3) UNSIGNED DEFAULT 0.000,
  `RankMW` smallint(5) UNSIGNED DEFAULT NULL,
  `CatGER` varchar(4) DEFAULT NULL,
  `RankCatGER` smallint(5) UNSIGNED DEFAULT NULL,
  `CatEvent` varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `RankCatEvent` smallint(5) UNSIGNED DEFAULT NULL,
  `CatInt` varchar(4) DEFAULT NULL,
  `RankCatInt` smallint(5) UNSIGNED DEFAULT NULL,
  `Scores` decimal(10,0) DEFAULT NULL,
  `Exclude` tinyint(1) DEFAULT 0,
  `Comments` varchar(255) DEFAULT NULL,
  `RankIntYear` smallint(5) UNSIGNED DEFAULT NULL,
  `RankNatYear` smallint(5) UNSIGNED DEFAULT NULL,
  `Age_Y` decimal(8,5) DEFAULT 0.00000
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci PACK_KEYS=0;

-- --------------------------------------------------------

--
-- Tabellstruktur `tperson`
--

CREATE TABLE `tperson` (
  `PersonID` mediumint(8) UNSIGNED NOT NULL,
  `LastName` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_german2_ci NOT NULL,
  `FirstName` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_german2_ci DEFAULT NULL,
  `OrigName` varchar(70) CHARACTER SET utf8mb4 COLLATE utf8mb4_german2_ci DEFAULT NULL,
  `Club` varchar(50) DEFAULT NULL,
  `City` varchar(50) DEFAULT NULL,
  `Nationality` char(3) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `Gender` enum('M','W','X') CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT 'M',
  `Birthday` date DEFAULT NULL,
  `YOB` smallint(6) DEFAULT 0,
  `YOBmin` smallint(6) DEFAULT 0,
  `YOBmax` smallint(6) DEFAULT 0,
  `DUVID` mediumint(9) DEFAULT 0,
  `ParentID` mediumint(8) UNSIGNED DEFAULT 0,
  `Privacy` tinyint(4) DEFAULT 0,
  `Comments` varchar(255) DEFAULT NULL,
  `CreatedBy` varchar(15) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `CreatedOn` timestamp NOT NULL DEFAULT current_timestamp(),
  `ChangedBy` varchar(15) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `ChangedOn` timestamp NOT NULL DEFAULT current_timestamp(),
  `Nat2` varchar(3) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `ComradesID` mediumint(9) DEFAULT NULL,
  `DOD` date DEFAULT NULL,
  `OrigNameL` varchar(50) DEFAULT NULL,
  `OrigNameF` varchar(50) DEFAULT NULL,
  `Mileage` decimal(9,3) UNSIGNED DEFAULT 0.000,
  `RaceCnt` smallint(5) UNSIGNED DEFAULT 0
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci PACK_KEYS=0;

-- --------------------------------------------------------

--
-- Tabellstruktur `trecordger`
--

CREATE TABLE `trecordger` (
  `Dist` varchar(5) NOT NULL,
  `RecType` tinyint(4) NOT NULL COMMENT '0=Overall,1=Road,2=Track,3=Indoor',
  `AK` varchar(5) NOT NULL,
  `Perf` varchar(12) DEFAULT NULL,
  `CreaDate` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_bin;

-- --------------------------------------------------------

--
-- Tabellstruktur `tstat`
--

CREATE TABLE `tstat` (
  `Land` varchar(10) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `Jahr` int(11) DEFAULT NULL,
  `Strecke` varchar(25) DEFAULT NULL,
  `Gender` char(1) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `PerfCnt` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `CreaDate` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Tabellstruktur `tstat0`
--

CREATE TABLE `tstat0` (
  `PerfCnt` int(11) NOT NULL,
  `PersonCnt` int(11) NOT NULL,
  `EventCnt` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Summary counts of perfs, persons and events for index page';

-- --------------------------------------------------------

--
-- Tabellstruktur `tstat2`
--

CREATE TABLE `tstat2` (
  `Land` varchar(10) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `Jahr` int(11) NOT NULL,
  `Strecke` varchar(25) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `Gender` char(1) CHARACTER SET ascii COLLATE ascii_general_ci NOT NULL,
  `Rank` smallint(5) UNSIGNED DEFAULT NULL,
  `Time` time DEFAULT NULL,
  `Distance` decimal(7,3) DEFAULT NULL,
  `CreaDate` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Tabellstruktur `tstat3`
--

CREATE TABLE `tstat3` (
  `Nation` varchar(3) DEFAULT NULL,
  `EvtYear` int(11) DEFAULT NULL,
  `Gender` char(1) DEFAULT NULL,
  `Performances` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `Runners` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `Region` tinyint(3) UNSIGNED NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Tabellstruktur `tstatlong`
--

CREATE TABLE `tstatlong` (
  `Cat` varchar(5) CHARACTER SET ascii COLLATE ascii_general_ci NOT NULL COMMENT 'e.g.45km, 50mi, 100km, 100mi',
  `PersonID` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `Lastname` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Firstname` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Gender` char(1) DEFAULT NULL,
  `YOB` smallint(6) DEFAULT 0,
  `Nationality` varchar(3) DEFAULT NULL,
  `DiffDate` mediumint(8) UNSIGNED DEFAULT 0,
  `EventID` mediumint(8) UNSIGNED DEFAULT 0,
  `Startdate` datetime DEFAULT NULL,
  `EventName` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `NormLen` decimal(8,3) DEFAULT 0.000,
  `Dist1` decimal(8,3) UNSIGNED DEFAULT 0.000,
  `EvtID2` mediumint(8) UNSIGNED DEFAULT 0,
  `Startdate2` datetime DEFAULT NULL,
  `EventName2` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `NormLen2` decimal(8,3) UNSIGNED DEFAULT 0.000,
  `Dist2` decimal(8,3) UNSIGNED DEFAULT 0.000
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Ersättningsstruktur för vy `unified_runners`
-- (See below for the actual view)
--
CREATE TABLE `unified_runners` (
`PersonID` mediumint(8) unsigned
,`UnifiedID` mediumint(8) unsigned
);

-- --------------------------------------------------------

--
-- Ersättningsstruktur för vy `unified_runners_in_specified_events`
-- (See below for the actual view)
--
CREATE TABLE `unified_runners_in_specified_events` (
`PersonID` mediumint(8) unsigned
,`UnifiedID` mediumint(8) unsigned
);

-- --------------------------------------------------------

--
-- Struktur för vy `first_performances`
--
DROP TABLE IF EXISTS `first_performances`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `first_performances`  AS SELECT `u`.`UnifiedID` AS `UnifiedID`, min(`tp`.`PerfID`) AS `FirstPerfID` FROM (`tperformance` `tp` join (select `p`.`PersonID` AS `PersonID`,case when `p`.`ParentID` > 0 then `p`.`ParentID` else `p`.`PersonID` end AS `UnifiedID` from `tperson` `p`) `u` on(`tp`.`PersonID` = `u`.`PersonID` or `tp`.`PersonID` = `u`.`UnifiedID`)) GROUP BY `u`.`UnifiedID` ;

-- --------------------------------------------------------

--
-- Struktur för vy `unified_runners`
--
DROP TABLE IF EXISTS `unified_runners`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `unified_runners`  AS SELECT `p`.`PersonID` AS `PersonID`, CASE WHEN `p`.`ParentID` > 0 THEN `p`.`ParentID` ELSE `p`.`PersonID` END AS `UnifiedID` FROM `tperson` AS `p` ;

-- --------------------------------------------------------

--
-- Struktur för vy `unified_runners_in_specified_events`
--
DROP TABLE IF EXISTS `unified_runners_in_specified_events`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `unified_runners_in_specified_events`  AS SELECT `p`.`PersonID` AS `PersonID`, CASE WHEN `p`.`ParentID` > 0 THEN `p`.`ParentID` ELSE `p`.`PersonID` END AS `UnifiedID` FROM ((`tperson` `p` join `tperformance` `tp` on(`p`.`PersonID` = `tp`.`PersonID`)) join `tevent` `e` on(`tp`.`EventID` = `e`.`EventID`)) WHERE `e`.`ParentID` in (42622,42600,60546) ;

--
-- Index för dumpade tabeller
--

--
-- Index för tabell `tcountry`
--
ALTER TABLE `tcountry`
  ADD PRIMARY KEY (`Code`);

--
-- Index för tabell `tcup`
--
ALTER TABLE `tcup`
  ADD PRIMARY KEY (`EventID`);

--
-- Index för tabell `tevent`
--
ALTER TABLE `tevent`
  ADD PRIMARY KEY (`EventID`),
  ADD KEY `StartLoc` (`StartLoc`),
  ADD KEY `Duration` (`Duration`),
  ADD KEY `Length` (`Length`),
  ADD KEY `Country` (`Country`),
  ADD KEY `ParentID` (`ParentID`),
  ADD KEY `EventType` (`EventType`),
  ADD KEY `RecordProof` (`RecordProof`);

--
-- Index för tabell `tevent_submit`
--
ALTER TABLE `tevent_submit`
  ADD PRIMARY KEY (`SubmID`);

--
-- Index för tabell `tjapcities`
--
ALTER TABLE `tjapcities`
  ADD PRIMARY KEY (`JapCityID`),
  ADD KEY `LocJPN_idx` (`LocJPN`);

--
-- Index för tabell `tjapnames`
--
ALTER TABLE `tjapnames`
  ADD PRIMARY KEY (`JapNameID`),
  ADD KEY `kana_idx` (`Hiragana`),
  ADD KEY `kanji_idx` (`Kanji`);

--
-- Index för tabell `tlink`
--
ALTER TABLE `tlink`
  ADD PRIMARY KEY (`LinkID`);

--
-- Index för tabell `tmergelog`
--
ALTER TABLE `tmergelog`
  ADD PRIMARY KEY (`MergeID`);

--
-- Index för tabell `tnames`
--
ALTER TABLE `tnames`
  ADD UNIQUE KEY `PersonName` (`PersonName`);

--
-- Index för tabell `tnames_hebr`
--
ALTER TABLE `tnames_hebr`
  ADD PRIMARY KEY (`HebrNameID`),
  ADD KEY `Hebr_plain` (`Hebr_plain`),
  ADD KEY `Type` (`NameType`);

--
-- Index för tabell `tperformance`
--
ALTER TABLE `tperformance`
  ADD PRIMARY KEY (`PerfID`),
  ADD KEY `PersonID` (`PersonID`),
  ADD KEY `EventID` (`EventID`),
  ADD KEY `EvtID_RankTotal` (`EventID`,`RankTotal`),
  ADD KEY `Age_Idx` (`Age_Y`),
  ADD KEY `idx_tperformance_eventid_gender` (`EventID`,`PersonID`);

--
-- Index för tabell `tperson`
--
ALTER TABLE `tperson`
  ADD PRIMARY KEY (`PersonID`),
  ADD KEY `OrigName` (`OrigName`),
  ADD KEY `Nat` (`Nationality`),
  ADD KEY `ParentID` (`ParentID`),
  ADD KEY `Idx_LName` (`LastName`) USING BTREE,
  ADD KEY `Idx_FName` (`FirstName`) USING BTREE,
  ADD KEY `ComradesID` (`ComradesID`),
  ADD KEY `Idx_OrigNameL` (`OrigNameL`),
  ADD KEY `Idx_OrignameF` (`OrigNameF`),
  ADD KEY `Mileage` (`Mileage`),
  ADD KEY `Gender` (`Gender`),
  ADD KEY `idx_tperson_personid_gender` (`PersonID`,`Gender`);

--
-- Index för tabell `trecordger`
--
ALTER TABLE `trecordger`
  ADD PRIMARY KEY (`Dist`,`RecType`,`AK`);

--
-- AUTO_INCREMENT för dumpade tabeller
--

--
-- AUTO_INCREMENT för tabell `tevent`
--
ALTER TABLE `tevent`
  MODIFY `EventID` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT för tabell `tevent_submit`
--
ALTER TABLE `tevent_submit`
  MODIFY `SubmID` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT för tabell `tjapcities`
--
ALTER TABLE `tjapcities`
  MODIFY `JapCityID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT för tabell `tjapnames`
--
ALTER TABLE `tjapnames`
  MODIFY `JapNameID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT för tabell `tlink`
--
ALTER TABLE `tlink`
  MODIFY `LinkID` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT för tabell `tmergelog`
--
ALTER TABLE `tmergelog`
  MODIFY `MergeID` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT för tabell `tnames_hebr`
--
ALTER TABLE `tnames_hebr`
  MODIFY `HebrNameID` mediumint(9) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT för tabell `tperformance`
--
ALTER TABLE `tperformance`
  MODIFY `PerfID` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT för tabell `tperson`
--
ALTER TABLE `tperson`
  MODIFY `PersonID` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
