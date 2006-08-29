-- phpMyAdmin SQL Dump
-- version 2.6.0-pl3
-- http://www.phpmyadmin.net
-- 
-- Host: localhost
-- Generation Time: Apr 26, 2005 at 06:45 PM
-- Server version: 4.0.20
-- PHP Version: 4.3.10
-- 
-- Database: `jinzora2`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `jz_discussions`
-- 

CREATE TABLE `jz_discussions` (
  `my_id` int(11) NOT NULL default '0',
  `date_added` int(11) default NULL,
  `my_user` varchar(32) default NULL,
  `comment` text,
  `path` varchar(255) default NULL,
  PRIMARY KEY  (`my_id`)
) ENGINE=MyISAM;

-- 
-- Dumping data for table `jz_discussions`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `jz_links`
-- 

CREATE TABLE `jz_links` (
  `my_id` int(11) NOT NULL default '0',
  `parent` varchar(255) NOT NULL default '/',
  `path` varchar(255) NOT NULL default '/',
  `type` varchar(5) NOT NULL default '',
  PRIMARY KEY  (`my_id`)
) ENGINE=MyISAM;

-- 
-- Dumping data for table `jz_links`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `jz_nodes`
-- 

CREATE TABLE `jz_nodes` (
  `my_id` varchar(20) NOT NULL default '',
  `name` varchar(255) default NULL,
  `leaf` varchar(5) default 'false',
  `lastplayed` int(11) default '0',
  `playcount` int(11) default '0',
  `directplaycount` int(11) default '0',
  `dlcount` int(11) default '0',
  `viewcount` int(11) default '0',
  `rating` float default '0',
  `rating_count` float default '0',
  `rating_val` float default '0',
  `main_art` varchar(255) default NULL,
  `valid` varchar(5) default 'true',
  `path` varchar(255) NOT NULL default '/',
  `ptype` varchar(20) default NULL,
  `hidden` varchar(10) default 'false',
  `filepath` varchar(255) default '/',
  `level` int(11) default '0',
  `descr` varchar(255) default NULL,
  `longdesc` text,
  `date_added` int(11) default NULL,
  `leafcount` int(11) default '0',
  `nodecount` int(11) default '0',
  `featured` varchar(5) default 'false',
  PRIMARY KEY  (`path`),
  UNIQUE KEY `my_id` (`my_id`)
) ENGINE=MyISAM;

-- 
-- Dumping data for table `jz_nodes`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `jz_requests`
-- 

CREATE TABLE `jz_requests` (
  `my_id` int(11) NOT NULL default '0',
  `entry` text,
  `comment` text,
  `my_user` varchar(32) default NULL,
  `type` varchar(10) default 'request',
  `path` varchar(255) default NULL,
  PRIMARY KEY  (`my_id`)
) ENGINE=MyISAM;

-- 
-- Dumping data for table `jz_requests`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `jz_tracks`
-- 
CREATE TABLE `jz_tracks` (
  `my_id` varchar(20) NOT NULL default '',
  `ID` int(11) NOT NULL auto_increment,
  `path` varchar(255) NOT NULL default '/',
  `filepath` varchar(255) default NULL,
  `name` varchar(255) default NULL,
  `level` int(11) default '0',
  `hidden` varchar(10) default 'false',
  `trackname` varchar(255) default NULL,
  `number` char(3) default '-',
  `valid` varchar(5) default 'true',
  `bitrate` varchar(10) default '-',
  `frequency` varchar(10) default '-',
  `filesize` varchar(10) default '-',
  `length` varchar(10) default '-',
  `genre` varchar(20) default '-',
  `artist` varchar(100) default '-',
  `album` varchar(150) default '-',
  `year` varchar(5) default '-',
  `Description` longtext,
  `Price` int(11) NULL default '0',
  `extension` varchar(5) default NULL,
  `lyrics` text,
  `sheet_music` text,
  PRIMARY KEY  (`path`),
  UNIQUE KEY  (`ID`),
  UNIQUE KEY `my_id` (`my_id`)
) ENGINE=MyISAM COMMENT='Stores all the information about tracks';

-- 
-- Dumping data for table `jz_tracks`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `jz_album_track`
-- 

CREATE TABLE `jz_album_track` (
  `ID` int(11) NOT NULL auto_increment,
  `AlbumID` int(11) NOT NULL default '0',
  `TrackID` int(11) NOT NULL default '0',
  PRIMARY KEY  (`ID`),
  KEY `fk_album_track_album_id` (`AlbumID`),
  KEY `fk_album_track_track_id` (`TrackID`)
) ENGINE=MyISAM COMMENT='Matches tracks to multiple artists' AUTO_INCREMENT=1 ;

-- 
-- Dumping data for table `jz_album_track`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `jz_albums`
-- 

CREATE TABLE `jz_albums` (
  `ID` int(11) NOT NULL auto_increment,
  `Name` varchar(254) NOT NULL default '',
  `Description` longtext,
  `Image` longtext,
  `Year` int(4) default '0',
  `Playcount` int(11) NOT NULL default '0',
  `Rating` int(11) NOT NULL default '0',
  `AmazonID` varchar(254) default NULL,
  `Price` int(11) NOT NULL default '0',
  PRIMARY KEY  (`ID`),
  UNIQUE KEY `Name` (`Name`)
) ENGINE=MyISAM COMMENT='Stores all Albums' AUTO_INCREMENT=1 ;

-- 
-- Dumping data for table `jz_albums`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `jz_artist_album`
-- 

CREATE TABLE `jz_artist_album` (
  `ID` int(11) NOT NULL auto_increment,
  `ArtistID` int(11) NOT NULL default '0',
  `AlbumID` int(11) NOT NULL default '0',
  PRIMARY KEY  (`ID`),
  KEY `ArtistID` (`ArtistID`)
) ENGINE=MyISAM COMMENT='Matches Albums to multiple Artists' AUTO_INCREMENT=1 ;

-- 
-- Dumping data for table `jz_artist_album`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `jz_artists`
-- 

CREATE TABLE `jz_artists` (
  `ID` int(11) NOT NULL auto_increment,
  `Name` varchar(254) NOT NULL default '',
  `Description` longtext,
  `Image` longtext,
  `Playcount` int(11) NOT NULL default '0',
  `Rating` int(11) NOT NULL default '0',
  PRIMARY KEY  (`ID`),
  UNIQUE KEY `Name` (`Name`)
) ENGINE=MyISAM COMMENT='Stores all Artists' AUTO_INCREMENT=1 ;

-- 
-- Dumping data for table `jz_artists`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `jz_data`
-- 

CREATE TABLE `jz_data` (
  `ID` int(11) NOT NULL auto_increment,
  `Name` varchar(254) NOT NULL default '',
  `Value` varchar(254) NOT NULL default '',
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM COMMENT='Table to store random data needed by Jinzora' AUTO_INCREMENT=1 ;

-- 
-- Dumping data for table `jz_data`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `jz_genre_album`
-- 

CREATE TABLE `jz_genre_album` (
  `ID` int(11) NOT NULL auto_increment,
  `GenreID` int(11) NOT NULL default '0',
  `AlbumID` int(11) NOT NULL default '0',
  PRIMARY KEY  (`ID`),
  KEY `GenreID` (`GenreID`)
) ENGINE=MyISAM COMMENT='Matches Albums to multiple Genres' AUTO_INCREMENT=1 ;

-- 
-- Dumping data for table `jz_genre_album`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `jz_genre_artist`
-- 

CREATE TABLE `jz_genre_artist` (
  `ID` int(11) NOT NULL auto_increment,
  `GenreID` int(11) NOT NULL default '0',
  `ArtistID` int(11) NOT NULL default '0',
  PRIMARY KEY  (`ID`),
  KEY `GenreID` (`GenreID`)
) ENGINE=MyISAM COMMENT='Matches Artists to multiple Genres' AUTO_INCREMENT=1 ;

-- 
-- Dumping data for table `jz_genre_artist`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `jz_genres`
-- 

CREATE TABLE `jz_genres` (
  `ID` int(11) NOT NULL auto_increment,
  `Name` varchar(254) NOT NULL default '',
  `Description` longtext,
  `Image` longtext,
  `Playcount` int(11) default '0',
  `Rating` int(11) default '0',
  PRIMARY KEY  (`ID`),
  UNIQUE KEY `Name` (`Name`)
) ENGINE=MyISAM COMMENT='Stores all Genres' AUTO_INCREMENT=1 ;

-- 
-- Dumping data for table `jz_genres`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `jz_media_types`
-- 

CREATE TABLE `jz_media_types` (
  `ID` int(11) NOT NULL auto_increment,
  `Extension` varchar(4) NOT NULL default '',
  `Type` varchar(10) NOT NULL default '',
  `Header` text NOT NULL,
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM COMMENT='Stores the different media types and their headers' AUTO_INCREMENT=1 ;

-- 
-- Dumping data for table `jz_media_types`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `jz_now_playing`
-- 

CREATE TABLE `jz_now_playing` (
  `ID` int(11) NOT NULL auto_increment,
  `TrackID` int(11) NOT NULL default '0',
  `UserID` int(11) NOT NULL default '0',
  `Time` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM COMMENT='Stores the Now Playing data' AUTO_INCREMENT=1 ;

-- 
-- Dumping data for table `jz_now_playing`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `jz_now_viewing`
-- 

CREATE TABLE `jz_now_viewing` (
  `ID` int(11) NOT NULL auto_increment,
  `TrackID` int(11) NOT NULL default '0',
  `UserID` int(11) NOT NULL default '0',
  `Time` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM COMMENT='Stores the Now Viewing data' AUTO_INCREMENT=1 ;

-- 
-- Dumping data for table `jz_now_viewing`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `jz_settings`
-- 

CREATE TABLE `jz_settings` (
  `ID` int(11) NOT NULL auto_increment,
  `Name` varchar(254) NOT NULL default '',
  `Value` varchar(254) NOT NULL default '',
  `Type` varchar(254) NOT NULL default '',
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM COMMENT='Stores all the Jinzora settings' AUTO_INCREMENT=1 ;

-- 
-- Dumping data for table `jz_settings`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `jz_similar_albums`
-- 

CREATE TABLE `jz_similar_albums` (
  `ID` int(11) NOT NULL auto_increment,
  `AlbumID` int(11) NOT NULL default '0',
  `SimilarID` int(11) NOT NULL default '0',
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM COMMENT='Links albums to other similar albums' AUTO_INCREMENT=1 ;

-- 
-- Dumping data for table `jz_similar_albums`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `jz_similar_artists`
-- 

CREATE TABLE `jz_similar_artists` (
  `ID` int(11) NOT NULL auto_increment,
  `ArtistID` int(11) NOT NULL default '0',
  `SimilarID` int(11) NOT NULL default '0',
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM COMMENT='Links artists to other similar artists' AUTO_INCREMENT=1 ;

-- 
-- Dumping data for table `jz_similar_artists`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `jz_users`
-- 

CREATE TABLE `jz_users` (
  `ID` int(11) NOT NULL auto_increment,
  `Username` varchar(254) NOT NULL default '',
  `Password` varchar(254) NOT NULL default '',
  `Stream` tinyint(1) NOT NULL default '0',
  `Download` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM COMMENT='Stores data about the users' AUTO_INCREMENT=1 ;

-- 
-- Dumping data for table `jz_users`
-- 

