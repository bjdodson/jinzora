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




-- NEW FOR 3.0. EXPERIMENTAL.
-- You can comment in the forums, but do not take
-- anything in here as final.

-- In Jinzora, we can use a table key (gr = genre) and the media ID
-- as a fully qualified element path.

CREATE TABLE 
	jz_genres
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
			PRIMARY KEY  (my_id),
		);

CREATE TABLE 
	jz_subgenres
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
			PRIMARY KEY  (my_id)
		);

CREATE TABLE 
	jz_artists
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
			PRIMARY KEY  (my_id)
		);

CREATE TABLE 
	jz_albums
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
			compilation varchar(1) default 'N',
			PRIMARY KEY  (my_id)
		);

CREATE TABLE 
	jz_track_map
		(
		track_id varchar(20) NOT NULL,
		album_id varchar(20) NOT NULL,
		diskname varchar(255) DEFAULT NULL,
		artist_id varchar(20) NOT NULL,
		subgenre_id varchar(20) NOT NULL,
		genre_id varchar(20) NOT NULL,
		);
		
CREATE TABLE 
	jz_album_map
		(
		album_id varchar(20) NOT NULL,
		artist_id varchar(20) NOT NULL,
		subgenre_id varchar(20) NOT NULL,
		genre_id varchar(20) NOT NULL,
		);
		
CREATE TABLE 
	jz_artist_map
		(
		artist_id varchar(20) NOT NULL,
		subgenre_id varchar(20) NOT NULL,
		genre_id varchar(20) NOT NULL,
		);

CREATE TABLE 
	jz_subgenre_map
		(
		subgenre_id varchar(20) NOT NULL,
		genre_id varchar(20) NOT NULL,
		);
		
		
CREATE TABLE
	jz_featured
		(
		media_type varchar(10) NOT NULL,
		media_id varchar(20) NOT NULL
		);
				
--GETTING ALL INFO FOR A TRACK:
--SELECT jz_genres.name as genre, 
--       jz_subgenres.name as subgenre,
--       jz_artists.name as artist,
--       jz_albums.name as album,
--       jz_track_map.diskname as disk,
--       jz_tracks.name as track
--
--FROM jz_genres,jz_subgenres,jz_artists,jz_albums,jz_tracks,
--     jz_gr_sg, jz_sg_ar, jz_ar_al
--WHERE
--     jz_tracks.my_id    = #VALUE#
--AND  jz_tracks.my_id    = jz_track_map.track_id
--AND  jz_album.my_id     = jz_track_map.album_id
--AND  jz_artists.my_id   = jz_track_map.artist_id
--AND  jz_subgenres.my_id = jz_track_map.subgenre_id
--AND  jz_genres.my_id    = jz_track_map.genre_id
--ORDER BY jz_track_map.is_primary DESC;
