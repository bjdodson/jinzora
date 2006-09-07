-- Database: `jinzora3`
-- 

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
-- Table structure for table `jz_tracks`
-- 
CREATE TABLE 
	jz_tracks
		(
			my_id varchar(20) NOT NULL default '',
			path varchar(255) NOT NULL default '/',
			filepath varchar(255) default NULL,
			name varchar(255) default NULL,
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

-- 
-- Table structure for table `jz_genres`
-- 

CREATE TABLE 
	jz_genres
		(
			my_id varchar(20) NOT NULL default '',
			name varchar(100) default NULL,
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
			hidden varchar(10) default 'false',
			filepath varchar(255) default NULL,
			descr varchar(255) default NULL,
			longdesc text,
			date_added int default NULL,
			leafcount int default 0,
			nodecount int default 0,
			PRIMARY KEY  (my_id),
		);


-- 
-- Table structure for table `jz_subgenres`
-- 
CREATE TABLE 
	jz_subgenres
		(
			my_id varchar(20) NOT NULL default '',
			name varchar(100) default NULL,
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
			hidden varchar(10) default 'false',
			filepath varchar(255) default NULL,
			descr varchar(255) default NULL,
			longdesc text,
			date_added int default NULL,
			leafcount int default 0,
			nodecount int default 0,
			PRIMARY KEY  (my_id)
		);

-- 
-- Table structure for table `jz_artists`
-- 
CREATE TABLE 
	jz_artists
		(
			my_id varchar(20) NOT NULL default '',
			name varchar(100) default NULL,
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
			hidden varchar(10) default 'false',
			filepath varchar(255) default NULL,
			descr varchar(255) default NULL,
			longdesc text,
			date_added int default NULL,
			leafcount int default 0,
			nodecount int default 0,
			PRIMARY KEY  (my_id)
		);

-- 
-- Table structure for table `jz_albums`
-- 
CREATE TABLE 
	jz_albums
		(
			my_id varchar(20) NOT NULL default '',
			name varchar(100) default NULL,
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
			hidden varchar(10) default 'false',
			filepath varchar(255) default NULL,
			descr varchar(255) default NULL,
			longdesc text,
			date_added int default NULL,
			leafcount int default 0,
			nodecount int default 0,
			compilation varchar(1) default 'N',
			PRIMARY KEY  (my_id)
		);

-- 
-- Table structure for table `jz_track_map`
-- 
CREATE TABLE 
	jz_track_map
		(
		track_id varchar(20) NOT NULL,
		album_id varchar(20) NOT NULL,
		diskname varchar(100) DEFAULT NULL,
		artist_id varchar(20) NOT NULL,
		subgenre_id varchar(20) NOT NULL,
		genre_id varchar(20) NOT NULL
		);

-- 
-- Table structure for table `jz_album_map`
-- 		
CREATE TABLE 
	jz_album_map
		(
		album_id varchar(20) NOT NULL,
		artist_id varchar(20) NOT NULL,
		subgenre_id varchar(20) NOT NULL,
		genre_id varchar(20) NOT NULL
		);

-- 
-- Table structure for table `jz_artist_map`
-- 				
CREATE TABLE 
	jz_artist_map
		(
		artist_id varchar(20) NOT NULL,
		subgenre_id varchar(20) NOT NULL,
		genre_id varchar(20) NOT NULL
		);

-- 
-- Table structure for table `jz_subgenre_map`
-- 		
CREATE TABLE 
	jz_subgenre_map
		(
		subgenre_id varchar(20) NOT NULL,
		genre_id varchar(20) NOT NULL
		);
		
-- 
-- Table structure for table `jz_featured`
-- 				
CREATE TABLE
	jz_featured
		(
		media_type varchar(10) NOT NULL,
		media_id varchar(20) NOT NULL
		);



--ADD INDEXES
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
