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

CREATE TABLE 
	jz_discussions
		(
			  my_id int NOT NULL default 0,
			  date_added int default NULL,
			  my_user varchar(32) default NULL,
			  comment text,
			  path varchar(255) default NULL,
			  PRIMARY KEY  (my_id)
		);

-- 
-- Dumping data for table `jz_discussions`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `jz_links`
-- 

CREATE TABLE 
	jz_links
		(
			  my_id int NOT NULL default 0,
			  parent varchar(255) NOT NULL default '/',
			  path varchar(255) NOT NULL default '/',
			  type varchar(5) NOT NULL default '',
			  PRIMARY KEY  (my_id)
		);

-- 
-- Dumping data for table `jz_links`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `jz_nodes`
-- 

CREATE TABLE 
	jz_nodes
		(
			my_id varchar(20) NOT NULL default '',
			name varchar(255) default NULL,
			leaf varchar(5) default 'false',
			lastplayed int default 0,
			playcount int default 0,
			directplaycount int default 0,
			dlcount int default 0,
			viewcount int default 0,
			rating float default 0,
			rating_count float default 0,
			rating_val float default 0,
			main_art varchar(255) default NULL,
			valid varchar(5) default 'true',
			path varchar(255) NOT NULL default '/',
			ptype varchar(20) default NULL,
			hidden varchar(10) default 'false',
			filepath varchar(255) default '/',
			level int default 0,
			descr varchar(255) default NULL,
			longdesc text,
			date_added int default NULL,
			leafcount int default 0,
			nodecount int default 0,
			featured varchar(5) default 'false',
			PRIMARY KEY  (path),
			UNIQUE (my_id)
		);

-- 
-- Dumping data for table `jz_nodes`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `jz_requests`
-- 

CREATE TABLE 
	jz_requests
		(
			my_id int NOT NULL default 0,
			entry text,
			comment text,
			my_user varchar(32) default NULL,
			type varchar(10) default 'request',
			path varchar(255) default NULL,
			PRIMARY KEY  (my_id)
		);

-- 
-- Dumping data for table `jz_requests`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `jz_tracks`
-- 

CREATE TABLE 
	jz_tracks
		(
			my_id varchar(20) NOT NULL default '',
			path varchar(255) NOT NULL default '/',
			filepath varchar(255) default NULL,
			name varchar(255) default NULL,
			level int default 0,
			hidden varchar(10) default 'false',
			trackname varchar(255) default NULL,
			number char(3) default '-',
			valid varchar(5) default 'true',
			bitrate varchar(10) default '-',
			frequency varchar(10) default '-',
			filesize varchar(10) default '-',
			length varchar(10) default '-',
			genre varchar(20) default '-',
			artist varchar(100) default '-',
			album varchar(150) default '-',
			year varchar(5) default '-',
			Description text,
			Price int NULL default 0,
			extension varchar(5) default NULL,
			lyrics text,
			sheet_music text,
			PRIMARY KEY  (path),
			UNIQUE (my_id)
		);


-- index our nodes:
CREATE INDEX node_level on jz_nodes(level);
CREATE INDEX node_hidden on jz_nodes(hidden);
CREATE INDEX node_featured on jz_nodes(featured);
CREATE INDEX node_leaf on jz_nodes(leaf);

-- index our tracks:
CREATE INDEX track_level on jz_tracks(level);
CREATE INDEX track_hidden on jz_tracks(hidden);

-- these are mostly for charts:
CREATE INDEX node_viewcount on jz_nodes(viewcount desc);
CREATE INDEX node_dateadded on jz_nodes(date_added desc);
CREATE INDEX node_dlcount on jz_nodes(dlcount desc);
CREATE INDEX node_playcount on jz_nodes(playcount desc);
CREATE INDEX node_lastplayed on jz_nodes(lastplayed desc);
CREATE INDEX node_rating on jz_nodes(rating_val desc);

-- not sure if this does anything; could help for random art
CREATE INDEX node_art on jz_nodes(main_art);
