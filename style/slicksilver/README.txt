+++++++++++++++++++++++++++++++++++
+ Plain style for Slick interface +
+++++++++++++++++++++++++++++++++++
This style was created solely for use with the Slick interface on a standalone installation of Jinzora. I
don't use the Jukebox, so there are likely some missing images.

Disclaimer: As always, backup your files before editing! I've tested these changes on my own installs of
Jinzora (both the stable release of 2.1 and a recent CVS version. However, your mileage may vary depending
on any number of things, so please follow these instructions at your own risk. No guarantees, warranties
or luck are implied or expressed herein. Have fun!

==================
Basic Installation
==================

1) Unpack the Plain theme in your ./Jinzora/style/ directory

2) Change permissions in the Plain theme directory to match your other theme directories.

3) In order to display the striped background:
   add the following lines to ./Jinzora/style/css.php after "background: <?php echo jz_pg_bg_color; ?>;" in 
   the body element: 

   background-image: url("<?php echo $root_dir; ?>/style/<?php echo $skin; ?>/tile.gif");
   background-repeat: repeat;


=============================================================
Optional Components Installation (aka Jinzora Tweaking HOWTO)
=============================================================
*A Note On Editing*
When editing files I comment out the lines I change and add my initials, rather than deleting them outright. This
gives me a way to track the changes I make, and should an error arise, troubleshooting is easier. Commenting 
also provides an easy way to quickly search for *all* the changes I've made (in Linux, from the Jinzora root
directory, I use "grep -r '<ABC>' *" which returns all of my Jinzora edits...) For example, my edit for Step 4
below looks like this:

 	//if ($skin == "slick"){ <ABC> 8/4/05
	if ($skin <> "slick"){

And now, onto the edits... 
-------------------------------------------------------------

4) Display Jinzora logo:
   In ./Jinzora/frontend/frontends/slick/header.php change "if ($skin == "slick"){" to  "if ($skin <> "slick"){"
   
5) Display the custom Header Images:
   NOTE: This is a really ugly hack that I hoped I could accomplish using CSS. Unfortunately, I was never able to
   find a way to handle all the text elements simply, So I ended up having to use static images. To accomplish
   Header Text styling, I tried to keep things as modular as I could. I've added my custom Header Images to the
   Icon Library file so that the images can be called as globals inside each function.

   -Backup your existing icons.lib.php file; remame ./Jinzora/frontend/icons.lib.php to icons.lib.php_old   
   
   -From the theme archive, copy the file /Plain/Optional/icons.lib.php to ./Jinzora/frontend/
   
   -In .Jinzora/frontend/blocks.php find "function nodeGrid". In this function under "global" add 
    "$img_ht_genres, $img_ht_artists, $img_ht_albums" to the end of the list of globals. Next, search for
    "$pg_title = word("Genres");" and change it to "$pg_title = $img_ht_genres;". Find
    "$pg_title = word("Artists");" and change it to "$pg_title = $img_ht_artists;". Locate
    "$pg_title = word("Albums");" and change it to "$pg_title = $img_ht_albums;".
    Next, search for "function alphabeticalList", and on the following line, add
    "global $img_ht_alpha_listing_albums, $img_ht_alpha_listing_artists;". Search for 
    "$title = word("Alphabetical Listing (Albums)");" and replace it with "$title = $img_ht_alpha_listing_albums;".
    Search for "$title = word("Alphabetical Listing (Artists)");" and replace it with 
    "$title = $img_ht_alpha_listing_artists;"

   -In ./Jinzora/frontend/frontends/slick/blocks.php search for "function slickRandomAlbums" adding $img_ht_random_albums
    global to the globals list, and replacing "$title = word("Random Albums");" with "$title = $img_ht_random_albums;".
    Then search for "function showSlickCharts", add $img_ht_charts to the globals list and replace
    "$title = word("Charts");" with "$title = $img_ht_charts;" View All Art
	
   -In ./Jinzora/frontend/frontends/slick/header.php search for "function pageTop", add $img_ht_browse to the globals
    list, search for "$title = "Browse"; and replace with "$title = $img_ht_browse;". NOTE: Changing this setting 

6) Change "Logged in as" to "USER: [username]":
   -In ./Jinzora/frontend/frontends/slick/header.php, search for "Logged in as:" and change it to "USER:". I like the
    the username in brackets and on the same line as "USER:", however for longer usernames, this may break. To bring 
    the username up a line, remove the <br> below the line you just edited. To add brackets, simply add brackets around 
    "<?php echo $jzUSER->getName();?>" on the next line down. The edited lines should now resemble the following:
   
   		<strong><?php echo word("USER:");?>:</strong>
   		</font>
   		<font size="1"><strong>[<?php echo $jzUSER->getName();?>]</strong>

7) Custom "default" image:
   This replaces the default image for missing album art.
   -Backup ./Jinzora/style/images/default.jpg
   -From the Plain theme archive copy ./Plain/Optional/default.jpg to ./Jinzora/style/images
   		
=============================================
That's all for now, good luck and enjoy Jinzora!
Questions or comments? Send me a PM at the Jinzora forums: http://www.jinzora.com/forums/privmsg.php?mode=post&u=534

-Languid