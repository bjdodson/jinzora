<?php if (!defined(JZ_SECURE_ACCESS)) die ('Security breach detected.');
/**
 * Custom Metadata Service
 *
 * This service retrieves data from two distinct sources: Amazon for album 
 * data and Yahoo! Music for artist information. The Amazon retrieval method
 * is the most complex and feature filled. 
 *-------------------------------------------------------------------------
 * FEATURES
 * Amazon Album Retrieval
 *   o 
 *-------------------------------------------------------------------------
 * TODO: 
 *   o Retrieve customer images from Amazon when no album image exists.
 *   o Allow [COUNTRY] meta tag to allow lookup on different Amazon servers.
 */

/**
 * Configuration
 */
define('SERVICE_METADATA_webmosher','true');

$jzSERVICE_INFO = array();
$jzSERVICE_INFO['name'] = "Custom combination service retrieval";
$jzSERVICE_INFO['url'] = "http://www.darkhart.net";

global $matchAlbumWeight;
$matchAlbumWeight = array(
   'album' => array(
      'exact' => 8000,
      'general' => 4000,
      'regex' => 2000,
      'partial' => 2000,
      ),
   'artist' => array(
      'exact' => 800,
      'general' => 400,
      'regex' => 200,
      'partial' => 200),
   'year' => array (
      'exact' => 80,
      'general' => 0),
   'image' => array (
      'exact' => 40,
      'general' => 20),
   'review' => array (
      'exact' => 8,
      'general' => 4),
   'rating' => array (
      'exact' => 4)
   );

/**
 *-------------------------------------------------------------------------
 * CONFIG COMPLETE -- VENTURE BELOW AT YOUR OWN RISK
 *-------------------------------------------------------------------------
*/

/*
* Gets the metadata for an album from Amazon
*
* @author Fred Hirsch
* @param $node The current node we are looking at
* @param $displayOutput Should we display output? (defaults to true)
**/
function SERVICE_GETALBUMMETADATA_webmosher($node, $displayOutput = true, $return = false) {
   global $include_path, $matchAlbumWeight;
   global $test_php4;$test_php4 = false;
   $link_url = 'http://www.amazon.com/dp/';

   $parent = $node->getParent();
   $search_tracking = array();

   // Normally, we are probably not overriding our values, so just procede.
   if(empty($_POST[descOVERRIDE]) && empty($_POST[imgOVERRIDE])) {
      // Next we pre-process the album and artist information from the JZ node
      // that is assigned to this meta data request. This is done to try and
      // simplify the data before it is sent off to Amazon, but also maintain
      // the original so that matching can be correlated correctly.
      
      // Setup the incoming Album/Artist information 
      $album = trim($node->getName());
      $orig_album = $album;
      $artist = trim($parent->getName());
      $orig_artist = $artist;
   
      //Strip down the album a bit
      $album = preg_replace('/[\(\[][^\)\]]+[\)\]]/', '', $album);      // Remove text in parenthesis & brackets
      $album = preg_replace('/[-_,]/', ' ', $album);                    // Convert - and _ to space
      $album = preg_replace('/([A-Z])/', " $1", $album);                // Pad a space before capitol letters
      $album = preg_replace('/\s+/', ' ', $album);                      // Remove extra space
   
      // Stop word filtering removes extra words that may not be found in the 
      // result and will cause a lower correlation value.
      $stopwords = array('the', 'a','and');
      foreach ($stopwords as $word) {
         $album = preg_replace('/\b' . $word . '\b/i', '', $album);    // Remove stopwords
      }
      $album = preg_replace('/[^\w\s]/u', '', utf8_decode($album));     // Remove non-word characters & UTF8 handling
      $album = trim($album);
   
      // We utilize the idea of "Meta-tagging" in the album names to allow 
      // better search results. Amazon uses a similar system to mark album 
      // entries, so it fits well with their system. Essentially, if any album
      // has a tag enclosed in [] that matches the items below, the artist 
      // value is modified to improve searching. This is most effective for 
      // soundtracks & compilations. 
      $various = array('Soundtrack' => array('orig_artist' => 'Soundtrack', 'artist' => 'Various'),
      	'Various' => array('orig_artist' => 'Various Artists', 'artist' => 'Various'),
   	'Compilation' => array('orig_artist' => 'Various Artists', 'artist' => 'Various'),
   	'Single' => array('orig_artist' => $orig_artist, 'artist' => $artist));
      
      // We want to keep track of the postfixes in case they can be matched to 
      // the search.
      $postfix = '';
      foreach ($various as $key => $val) {
         if (preg_match('/\[' . $key . '\]/', $orig_album)) {
            $artist = $val['artist'];
            $orig_artist = $val['orig_artist'];
            $orig_album = preg_replace('/\s*[\(\[][^\)\]]+[\)\]]/', '', $orig_album);
            $postfix .= $key . ' ';
         }
      }
      
      // Some artists seem to like to release multiple albums with the same 
      // name, but in different years. Using the year value in a meta tag will
      // allow the search to add more correlation for that release year.
      if (preg_match('/\[(\d\d\d\d)\]$/', $orig_album, $match)) {
         $exact_year = $match[1];
         $orig_album = preg_replace('/\s*[\(\[][^\)\]]+[\)\]]/', '', $orig_album);
      } else {
         $exact_year = false;
      }
   
      // Now, we do the same thing to the artist.
      $artist = preg_replace('/\s*[\(\[][^\)\]]+[\)\]]/', '', $artist); // Remove text in parenthesis & brackets
      $artist = preg_replace('/[-_,]/', ' ', $artist);                  // Convert - and _ to space
      $artist = preg_replace('/\s+/', ' ', $artist);                    // Remove extra space
      foreach ($stopwords as $word) {
         $artist = preg_replace('/\b' . $word . '\b/i', '', $artist);  // Remove stopwords
      }
      $artist = preg_replace('/[^\w\s\']/u', '', utf8_decode($artist)); // Remove non-word characters & UTF8 handling
      $artist = trim($artist);
      // Lastly, we attempt to normalize any unicode in the artist text and if 
      // its different, we will flag this as an additional search.
      // TODO
   
      // Configure a standard ordered search list
      $searches = array(); 
      // A fully exact search is the default. If this one matches something, we 
      // usually ignore the rest.
      $searches[] = array( name => "All Exact", artist => $orig_artist, album => $orig_album, exact_artist => true, exact_album => true, exact_year => $exact_year, postfix => $postfix);
      $searches[] = array( name => "General Album", artist => '', album => $album, exact_artist => false, exact_album => false, exact_year => $exact_year, threshhold => 4800,postfix => $postfix);
      $searches[] = array( name => "General Artist", artist => $artist, album => '', exact_artist => false, exact_album => false, exact_year => $exact_year, threshhold => 8400,postfix => $postfix);

      // We attempt to normalize any unicode in the album/artist text and if 
      // its different, we will flag this as an additional search.
      include_once($include_path . "lib/utfnormal/UtfNormal.php");
      $utffix = new UTFNormal();
      $artistUTFNormal = preg_replace('/[^\w\s]/', '', $utffix->toNFKD(utf8_encode($artist)));
      $albumUTFNormal = preg_replace('/[^\w\s]/', '', $utffix->toNFKD(utf8_encode($album)));
      if ($artist != $artistUTFNormal && $album != $albumUTFNormal) {
         $searches[] = array( name => "Normalized Artist", artist => $artistUTFNormal, album => $albumUTFNormal, exact_artist => false, exact_album => false, exact_year => $exact_year, postfix => $postfix);
      } elseif ($artist != $artistUTFNormal ) {
         $searches[] = array( name => "Normalized Album", artist => $artistUTFNormal, album => $album, exact_artist => false, exact_album => false, exact_year => $exact_year, postfix => $postfix);
      } elseif ($album != $albumUTFNormal) {
         $searches[] = array( name => "Normalized Album", artist => $artist, album => $albumUTFNormal, exact_artist => false, exact_album => false, exact_year => $exact_year, postfix => $postfix);
      }
      
      // Album & artist were modified, so we need to add a general search.
      if ($orig_album != $album && $orig_artist != $artist) {
         $searches[] = array( name => "All General", artist => $artist, album => $album, exact_artist => false, exact_album => false, exact_year => $exact_year,postfix => $postfix);
      }
      
      if ($orig_album != $album) {
         $searches[] = array( name => "Exact Artist", artist => $orig_artist, album => $album, exact_artist => true, exact_album => false, exact_year => $exact_year,postfix => $postfix);
      }
      if ($orig_artist != $artist) {
         $searches[] = array( name => "Exact Album", artist => $artist, album => $orig_album, exact_artist => false, exact_album => true, exact_year => $exact_year,postfix => $postfix);
      }
      // Set search defaults
      $lastSearchWeight = 0;

      // Calculate the best weightings, if we match this, we are done.
      $maxSearchWeight = ($matchAlbumWeight['album']['exact'] * 8) +   // Exact album weight
                         ($matchAlbumWeight['artist']['exact'] * 8)+  // Exact artist weight
                         $matchAlbumWeight['year']['exact'] +    // Exact artist weight
                         $matchAlbumWeight['image']['exact'] +   // Exact artist weight
                         $matchAlbumWeight['review']['exact'] +  // Exact artist weight
                         //$matchAlbumWeight['rating']['exact'] +  // Exact artist weight
                         (($exact_year) ? 80 : 0) ; // We pro-rate a bit more if we need an exact year.
      $baseSearchWeight = $matchAlbumWeight['album']['general'] +   // General album weight
                         $matchAlbumWeight['artist']['general'] +   // General artist weight
                         (($exact_year) ? 80 : 0) ; // We pro-rate a bit more if we need an exact year.
      $maxPages = 3;
      $searchItem = '';
      
      $fix_jz_path = urlencode(implode('/', $node->getPath()));
      print "<form action=\"popup.php?action=popup&ptype=getmetadata&jz_path=$fix_jz_path\" method=\"post\">\n";
      print "<input type=\"hidden\" name=\"edit_search_all_albums\" value=\"on\"/>\n";
      print "<input type=\"hidden\" name=\"edit_search_all_artists\" value=\"off\"/>\n";
      print "<input type=\"hidden\" name=\"metaSearchSubmit\" value=\"Search\"/>\n";
      print "<input type=\"hidden\" name=\"edit_search_images_miss\" value=\"always\"/>\n";
      print "<input type=\"hidden\" name=\"edit_search_desc_miss\" value=\"always\"/>\n";
      
      while (list($key,$search) = each($searches)) {
         $currentPage = 1;
         $totalPages = 1;
         $weight = 1;
   
         // We don't bother with the following searches:
         if ($search[artist] == 'Various' && $search[album] == '') {
            continue;
         } elseif (isset($search[threshhold]) && $search[threshhold] < $lastSearchWeight) {
            continue;
         }
   
         print '<table width="100%" border="0" cellspacing="0" cellpadding="0">';
         print "<tr><td>Searching for $search[name]...</td></tr>\n";
         $currentSearch = 'Title=' . urlencode($search[album]) . '&Artist=' . urlencode($search[artist]);
         while ($currentPage <= $totalPages) {
            // Do the XML data retrieval searching
            if ($xml = getXMLData($currentSearch)) {
            } else {
               print "No content received from Amazon, please retry.";
               break;
            }
   
            $totalPages = (xml_data($xml->Items->TotalPages) <= $maxPages) ? xml_data($xml->Items->TotalPages) : $maxPages;
   
            // Did we just get one match, or more than one?
            if (xml_data($xml->Items->TotalResults) == 1) {
               $item = $xml->Items->Item;
               $weight = weightMatch($search, $item);
               // Check the weighting values
               if ($weight >= $maxSearchWeight) {
                  $searchItem = $item;
                  break;
               } elseif ($weight > $baseSearchWeight && $weight > $lastSearchWeight) {
                  $searchItem = $item;
                  $lastSearchWeight = $weight;
               }
            } elseif (xml_data($xml->Items->TotalResults) > 1) {
               // If we found multiple results, we need to look through them all.
               foreach ($xml->Items->Item as $item) {
            	   $weight = weightMatch($search, $item);
            	   
                  // Check the weighting values
                  if ($weight >= $maxSearchWeight) {
                     $searchItem = $item;
                     break;
                  } elseif ($weight > $baseSearchWeight && $weight > $lastSearchWeight) {
                     $searchItem = $item;
                     $lastSearchWeight = $weight;
                  }
               }
               $currentSearch = "ItemPage=" . $currentPage;
            }
            $currentPage++;
            if ($weight >= $maxSearchWeight) {
               break;
            }
            sleep(1);  // Prevent "SPAMMING" Amazon?
         } 
         print "</table>\n";
         if ($weight >= $maxSearchWeight) {
            break;
         }
         flushdisplay();
      } 
      
      if (empty($searchItem)) {
         print "Match result not found. You may override the result by selecting override items above.<br\>\n";
         unset ($item);
      } else {
         print "Found as Amazon ID: [<a href=\"$link_url" . xml_data($searchItem->ASIN) . "\" target=\"_blank\">" . xml_data($searchItem->ASIN) . "</a>], setting data.<br/><br/>\n";
         print "<script><!--\n";
         print "document.getElementById(\"" . xml_data($searchItem->ASIN) ."\").setAttribute('bgcolor', '#000080');\n";
         print "//-->\n</script>\n";
         $item = $searchItem;
      }
      print "<div align=\"center\"><input type=\"submit\" value=\"Override Default\" class=\"jz_submit\"/></div>";
      print "</form>";
      flushdisplay();

      print "<div align=\"center\"><input type=\"submit\" value=\"Override Default\" class=\"jz_submit\"/></div>";
   // Here, we start an override of the original retrieval. 
   } else {
      $item = albumOverride();
   }

   $id = xml_data($item->ASIN);
   $year = substr(xml_data($item->ItemAttributes->ReleaseDate),0,4);
   if (isset($item->LargeImage->URL) &&  xml_data($item->LargeImage->URL) != '') {
      $image = xml_data($item->LargeImage->URL);
   } elseif (isset($item->MediumImage->URL) &&  xml_data($item->MediumImage->URL) != '') {
      $image = $item->MediumImage->URL;
   }
   $review = xml_data($item->EditorialReviews->EditorialReview->Content);
   // TODO: Rating does not seem to set unless override is chosen.
   $rating = sprintf(xml_data($item->CustomerReviews->AverageRating));
   if (is_array( $item->BrowseNodes->BrowseNode)) {
      $genre = xml_data($item->BrowseNodes->BrowseNode[0]->Ancestors->BrowseNode->Name);
   } else {
      $genre = xml_data($item->BrowseNodes->BrowseNode->Ancestors->BrowseNode->Name);
   }
   $ListPrice = sprintf(xml_data($item->ItemAttributes->ListPrice->FormattedPrice));

   $tracks = array();
   if (is_array($item->Tracks->Disc->Track)) {
      foreach ($item->Tracks->Disc->Track as $track) {
         $tracks[] = sprintf(xml_data($track));
      }
   } 

   if (!$return){
      writeAlbumMetaData($node, $year, $image, $tracks, $review, $rating, $ListPrice, $genre, true);
      return true;
   } else {
      if ($return == "array"){
         $retArr['year'] = $year;
         $retArr['image'] = $image;
         $retArr['review'] = $review;
         $retArr['rating'] = $rating;
         $retArr['id'] = $id;

         return $retArr;
      } else {
         return $$return;
      }
   }
   return true;
}

function weightMatch ($search, $item) {
   global $search_tracking, $matchAlbumWeight;
   $search_asin = (string) xml_data($item->ASIN);

   if (isset($search_tracking[$search_asin]) && $search_tracking[$search_asin]) {
      return 0;
   } else {
      $search_tracking[$search_asin] = true;
   }
   $weight = 0;
   $artist_weight = 0;
   $album_weight = 0;
   $album_multiplier = 1;
   $artist_multiplier = 1;

   $link_url = 'http://www.amazon.com/dp/';
   $amazon_key = '19B1FW4R5ABSKBWNV582';
   $link_xml = 'http://webservices.amazon.com/onca/xml?Service=AWSECommerceService&ResponseGroup=Large&Operation=ItemLookup&AWSAccessKeyId=' . $amazon_key . '&ItemId=';
   $colors = array(0 => '#A00000', 
      1 => '#008000', 
      2 => '#FFFF00',
      3 => '#FFDD44',
      4 => '#FF9900');
   $states = array('artist' => $colors[0],
      'album' => $colors[0],
      'image' => $colors[0],
      'descr' => $colors[0],
      'rating' => $colors[0],
      'year' => $colors[0]);
   
   // Let's see if our one match got us good results:
   $search_album = xml_data($item->ItemAttributes->Title);


   // Check search item meta tags to see if they match our own.
   $filter_postfix = array('IMPORT');
   $postfix_match = '';
   foreach ($filter_postfix as $key ) {
      if (preg_match("/$search[postfix]/i", $key) && (preg_match('/\[' . $key . '\]/i', $search_album) ||
         preg_match('/\(' . $key . '\)/i', $search_album))) {
         $album_weight += 500;
         $postfix_match = '++';
      } elseif (preg_match('/\[' . $key . '\]/i', $search_album) ||
         preg_match('/\(' . $key . '\)/i', $search_album)) {
         $album_weight -= 1000;
         $postfix_match = '--';
      }
   }
   
   // Remove text in parenthesis & brackets
   $search_album = preg_replace('/\s*\[[^\]]+\]/', '', $search_album);      
   $search_album = preg_replace('/\s*\([^\)]+\)/', '', $search_album);      
   
   // File names cannot contain these characters, so we remove them from the search as well
   $search_album = preg_replace('/[:?\/\\\"*<>|]/', '', $search_album);
   $search_album = preg_replace('/\.$/', '', $search_album);

   $stopwords = array('the', 'a','and');
   if ($search[exact_album] == true) {
      $search_album = trim($search_album); // Just trim whitespace
   } else {
      // If this is a filtered match, we should make sure the result is filtered the same way
      $search_album = preg_replace('/[-_,]/', ' ', $search_album);                    // Convert - and _ to space
      $search_album = preg_replace('/\s+/', ' ', $search_album);                      // Remove extra space
      $search_album = preg_replace('/[^\w\s]/u', '', utf8_decode($search_album));     // Remove non-word characters & UTF8 handling
      foreach ($stopwords as $word) {
         $search_album = preg_replace('/\b' . $word . '\b/i', '', $search_album);    // Remove stopwords
      }
   }

   // The artist field can be blank, so if it is, we try using the author
   if (sizeof ($item->ItemAttributes->Artist) == 1) {
      if (xml_data($item->ItemAttributes->Artist) != '') {
         $search_artist = xml_data($item->ItemAttributes->Artist);
      } elseif ($item->ItemAttributes->Author != '') {
         $search_artist = xml_data($item->ItemAttributes->Author);
      } else {
         // Last ditch, just use the filtered artist.
         $search_artist = $artist;
      }
   // Multiple artist album.
   } else {
      for ($i = 0; $i < sizeof($item->ItemAttributes->Artist); $i++) {
         $prefix = '';
         if ($i > 0) {
            $prefix = ' ';
         }
         $search_artist .= $prefix . xml_data($item->ItemAttributes->Artist[$i]);
      }
   }

   // File names cannot contain these characters, so filter them from results.
   $search_artist= preg_replace('/[:?\/\\\"*<>|]/', '', $search_artist);
   $search_artist = preg_replace('/\s*\[[^\]]+\]/', '', $search_artist); // Remove text in parenthesis & brackets
   $search_artist = preg_replace('/\s*\([^\)]+\)/', '', $search_artist); // Remove text in parenthesis & brackets
   
   // File names always trim the last period from a name.
   $search_artist = preg_replace('/\.$/', '', $search_artist);

   if ($search[exact_artist] == true) {
      $search_artist = trim($search_artist);
   } else {
      $search_artist = preg_replace('/[-_,]/', ' ', $search_artist);                  // Convert - and _ to space
      $search_artist = preg_replace('/\s+/', ' ', $search_artist);                    // Remove extra space
      $search_artist = preg_replace('/[^\w\s\']/u', '', utf8_decode($search_artist)); // Remove non-word characters & UTF8 handling
      foreach ($stopwords as $word) {
         $search_artist = preg_replace('/\b' . $word . '\b/i', '', $search_artist);  // Remove stopwords
      }
   }

   // Highest priority is a completely exact match. Album priority is most important.
   if ($search[exact_album] && strtoupper($search[album]) == strtoupper($search_album)) {
      $album_weight += $matchAlbumWeight['album']['exact'];
      $artist_multiplier = 8;
      $states['album'] = $colors[1];
   } elseif (strtoupper($search[album]) == strtoupper($search_album)) {
      $album_weight += $matchAlbumWeight['album']['general'];
      $artist_multiplier = 4;
      $states['album'] = $colors[2];
   } elseif ($search[album] != '' && 
      (preg_match('/'. addcslashes($search_album, "&()[].+^$(){}=!'") . '/i', $search[album]) || 
      preg_match('/' . addcslashes($search[album], "&()[].+^$(){}=!'") . '/i', $search_album))) {
      $album_weight += $matchAlbumWeight['album']['regex'];
      $artist_multiplier = 3;
      $states['album'] = $colors[3];
   } else {
      $words = explode(' ', $search_album);
      $word_base = $matchAlbumWeight['album']['partial'] / sizeof($words);
      foreach ($words as $word) {
         if(preg_match('/' . $word . '/i', $search[album])) {
            $album_weight += $word_base;
            $states['album'] = $colors[4];
         }
      }
   }

   // First we try an exact match
   if ($search[exact_artist] && strtoupper($search[artist]) == strtoupper($search_artist)) {
      $artist_weight += $matchAlbumWeight['artist']['exact'];
      $album_multiplier = 8;
      $states['artist'] = $colors[1];
   // Then we try a less exact match
   } elseif (strtoupper($search[artist]) == strtoupper($search_artist)) {
      $artist_weight += $matchAlbumWeight['artist']['general'];
      $album_multiplier = 4;
      $states['artist'] = $colors[2];
   // Then we look for a general pattern match
   } elseif ($search[artist] != '' && 
      (preg_match('/' . addcslashes($search_artist, "&()[].+^$(){}=!'") . '/i', $search[artist]) || 
      preg_match('/' . addcslashes($search[artist], "&()[].+^$(){}=!'") . '/i', $search_artist))) {
      $artist_weight += $matchAlbumWeight['artist']['regex'];
      $album_multiplier = 2;
      $states['artist'] = $colors[3];
   // Lastly, we allow a match on individual terms in the search
   } else {
      $words = explode(' ', $search_artist);
      $word_base = $matchAlbumWeight['artist']['partial'] / sizeof($words);
      foreach ($words as $word) {
         if(preg_match('/' . $word . '/i', $search[artist])) {
            $artist_weight += $word_base;
            $states['artist'] = $colors[4];
         }
      }
   }

   // If the artist name is in the album, its probably a good bet.
   if (preg_match('/' . addcslashes($search_artist, "&()[].+^$(){}=!'") . '/i', $search_album)) {
      $artist_multiplier += 2;
   }

   $weight = ($album_weight * $album_multiplier) + ($artist_weight * $artist_multiplier);
   
   if ($search[exact_year] && substr(xml_data($item->ItemAttributes->ReleaseDate),0,4) == $search[exact_year]) {
      $weight += $matchAlbumWeight['year']['exact'];
      $states['year'] = $colors[1];
   } elseif (isset($item->ItemAttributes->ReleaseDate) && xml_data($item->ItemAttributes->ReleaseDate) != '') {
      $weight += $matchAlbumWeight['year']['general'];
      $states['year'] = $colors[1];
   } elseif ($search[exact_year]) {
      $states['year'] = $colors[2];
   } else {
      $states['year'] = '#c0c0c0';
   }
   
   // Add some weightings for various data we really are looking for:
   $display_img = 'style/slick/clear.gif';
   $display_desc = 'Not available';
   if (isset($item->LargeImage->URL) && xml_data($item->LargeImage->URL) != '') {
      $weight +=  $matchAlbumWeight['image']['exact'];
      $states['image'] = $colors[1];
      $display_img = $item->LargeImage->URL;
   } elseif (isset($item->MediumImage->URL) && xml_data($item->MediumImage->URL) != '') {
      $weight +=  $matchAlbumWeight['image']['general'];
      $states['image'] = $colors[2];
      $display_img = $item->MediumImage->URL;
   }
   if (isset($item->EditorialReviews->EditorialReview->Content) && trim(xml_data($item->EditorialReviews->EditorialReview->Content)) != '') {
      $weight +=  $matchAlbumWeight['review']['exact']; // Has a description
      $states['descr'] = $colors[1];
      $display_desc = $item->EditorialReviews->EditorialReview->Content;
   }
   if (isset($item->CustomerReviews->AverageRating) && xml_data($item->CustomerReviews->AverageRating) != '') {
      $weight += $matchAlbumWeight['rating']['exact'] ; // Has ratings
      $states['rating'] = $colors[1];
   }
      	    
   print '<tr><td><table width="100%" border="1" cellspacing="0" cellpadding="5" id="'. xml_data($item->ASIN) .'">';
   print '<tr><td align="right" width="160px" bgcolor="' . $states['artist'] . '"><b>Artist:</b></td><td width="100%"> ' . $search_artist . '</td>';
   print "<td rowspan=\"2\" align=\"center\" nowrap=\"nowrap\"><a href=\"$link_url" . xml_data($item->ASIN) . "\" target=\"_blank\">View Amazon</a><br>\n";
   print "[<a href=\"$link_xml" . xml_data($item->ASIN) . "\" target=\"_blank\">XML View</a>]</td></tr>\n";
   print '<tr><td align="right" width="160px" bgcolor="' . $states['album'] . '"><b>Album:</b></td><td width="100%"> ' . $search_album . ' ' . $postfix_match . '</td></tr>';
   
   print '<tr><td colspan="3"><table width="100%" border="0" cellspacing="0" cellpadding="5"><tr>';
   print '<td width="160px" height="160" align="center" valign="middle" bgcolor="' . $states['image'] . '"><img src="' . $display_img . '" height="150" width="150"><br></td>';
   print '<td valign="top" bgcolor="' . $states['descr'] . '">' . $display_desc . '</td>';
   print '</tr><tr>';
   print '<td><input type="radio" name="imgOVERRIDE" value="'. xml_data($item->ASIN) .'"/>Override</td>';
   print '<td><input type="radio" name="descOVERRIDE" value="'. xml_data($item->ASIN) .'"/>Override</td>';
   print '</tr></table></td></tr></table></td></tr>';
   
   return $weight;
}

function artistOverride() {
   $ret = array();
   if (isset($_POST[descOVERRIDE]) ) {
      $ret['bio'] = $_POST[descOVERRIDE];
   }

   if (isset($_POST[imgOVERRIDE])) {
      $ret['image'] = $_POST[imgOVERRIDE];
   }
   return $ret;
}

function albumOverride() {
   if (isset($_POST[descOVERRIDE])) {
      $asin = $_POST[descOVERRIDE];
      $desc_xml = getXMLData(urlencode($asin), true);
   }
   if (isset($_POST[imgOVERRIDE])) {
      $asin = $_POST[imgOVERRIDE];
      $img_xml = getXMLData(urlencode($asin), true);
   }
   if (isset($_POST[descOVERRIDE]) && isset($_POST[imgOVERRIDE])) {
      if (isset($desc_xml->Items->Item->LargeImage) && isset($desc_xml->Items->Item->LargeImage->URL)) {
         $desc_xml->Items->Item->LargeImage->URL = (string) xml_data($img_xml->Items->Item->LargeImage->URL);
      } else {
         $desc_xml->Items->Item->addChild('LargeImage');
         $desc_xml->Items->Item->LargeImage->addChild('URL', $img_xml->Items->Item->LargeImage->URL);
      }
      if (isset($desc_xml->Items->Item->MediumImage) && isset($desc_xml->Items->Item->MediumImage->URL)) {
         $desc_xml->Items->Item->MediumImage->URL = (string) xml_data($img_xml->Items->Item->MediumImage->URL);
      } else {
         $desc_xml->Items->Item->addChild('MediumImage');
         $desc_xml->Items->Item->MediumImage->addChild('URL', $img_xml->Items->Item->MediumImage->URL);
      }
      if (isset($desc_xml->Items->Item->SmallImage)&& isset($desc_xml->Items->Item->SmallImage->URL)) {
         $desc_xml->Items->Item->SmallImage->URL = (string) xml_data($img_xml->Items->Item->SmallImage->URL);
      } else {
         $desc_xml->Items->Item->addChild('SmallImage');
         $desc_xml->Items->Item->SmallImage->addChild('URL', $img_xml->Items->Item->SmallImage->URL);
      }
      $item = $desc_xml->Items->Item;
   } elseif (isset($_POST[descOVERRIDE])) {
      $item = $desc_xml->Items->Item;
   } elseif (isset($_POST[imgOVERRIDE])) {
      $item = $img_xml->Items->Item;
   } else {
      return false;
   }

   $display_img = 'style/slick/clear.gif';
   $display_desc = 'Not available';
   if (isset($item->LargeImage->URL) && xml_data($item->LargeImage->URL) != '') {
      $display_img = $item->LargeImage->URL;
   } elseif (isset($item->MediumImage->URL) && xml_data($item->MediumImage->URL) != '') {
      $display_img = $item->MediumImage->URL;
   }
   if (isset($item->EditorialReviews->EditorialReview->Content) && trim(xml_data($item->EditorialReviews->EditorialReview->Content)) != '') {
      $display_desc = $item->EditorialReviews->EditorialReview->Content;
   } else {
      $item->EditorialReviews->EditorialReview->Content = '';
   }

   print '<tr><td><table width="100%" border="1" cellspacing="0" cellpadding="5" id="'. xml_data($item->ASIN) .'">';
   print '<tr><td colspan="3" nowrap="nowrap"><strong>Attempting Override</strong></td>';
   print '<tr><td align="right" width="160px" bgcolor="' . $states['artist'] . '"><b>Artist:</b></td><td width="100%"> ' . xml_data($item->ItemAttributes->Artist) . '</td>';
   print "<td rowspan=\"2\" align=\"right\"><a href=\"$link_url" . xml_data($item->ASIN) . "\" target=\"_blank\">View Amazon</a></td></tr>\n";
   print '<tr><td align="right" width="160px" bgcolor="' . $states['album'] . '"><b>Album:</b></td><td width="100%"> ' . xml_data($item->ItemAttributes->Title) . '</td></tr>';
   
   print '<tr><td colspan="3"><table width="100%" border="0" cellspacing="0" cellpadding="5"><tr>';
   print '<td width="160px" height="160" align="center" valign="middle" bgcolor="' . $states['image'] . '"><img src="' . $display_img . '" height="150" width="150"><br></td>';
   print '<td valign="top" bgcolor="' . $states['descr'] . '">' . $display_desc . '</td>';
   print '</tr></table></td></tr></table></td></tr>';

   return $item;
}

function getXMLData($search, $exact = false) {
   // Snoopy is used to robot the URL fetching
   include_once($include_path. "lib/snoopy.class.php");
   $snoopy_retry = 3;
   $amazon_key = '19B1FW4R5ABSKBWNV582';
   if ($exact) {
      $baseSearch = 'http://webservices.amazon.com/onca/xml?Service=AWSECommerceService&ResponseGroup=Large&Operation=ItemLookup&AWSAccessKeyId=' . $amazon_key . '&ItemId=' . $search;
   } else {
      $search_url = 'http://webservices.amazon.com/onca/xml?Service=AWSECommerceService&ResponseGroup=Large&Operation=ItemSearch&SearchIndex=Music&AWSAccessKeyId=' . $amazon_key;
      $baseSearch = $search_url . '&' . $search;
   }
   $snoopy = new Snoopy;
   $snoopy_tries = 0;
   while ($snoopy_retry > $snoopy_tries) {
      $snoopy->fetch($baseSearch);
      $snoopy_tries++;
      if ($snoopy->status == 200) {
         $xml_content = $snoopy->results;
         break;
      } else {
         if ($snoopy->status) {
            print "\n<div width=\"100%\" align=\"center\" style=\"background:#C00000;color:#FFFFFF;padding:3px\"><b>";
            print "\nThere was a problem fetching results from Amazon: <font color=\"red\">" . $snoopy->status . " " . $snoopy->error . "\n";
            print "\nWe will retry this request " . ($snoopy_retry - $snoopy_tries) . " more times.";
            print "\n</font></b></div>";
            //print "\n<div width=\"100%\" align=\"center\" style=\"background:#C08000;color:#FFFFFF;padding:3px\">We will retry this request " . $snoopy_retry - $snoopy_tries . " more times.</div>";
         } else {
            print "<b>There was a fatal error: <font color=\"red\">" . $php_errormsg . "</font></b><br>";
		      return false;
         }
      }
   }

   // Amazon returns XML, so parse it through simpleXML
   if (isset($xml_content) && $xml_content != '') {
      if (!$test_php4 && extension_loaded('simplexml')) {
         $xml = new SimpleXMLElement($xml_content);
      } elseif (extension_loaded('xml')) {
         if (version_compare('5.0.0', phpversion())) {
            print "<div width=\"100%\" align=\"center\" style=\"background:#C08000;color:#FFFFFF;padding:3px\"><b>PHP5 supports SimpleXML in a native library. This function will run much faster if you enable this component on your server.</b></div>";
         }
         include_once($include_path. "lib/simplexml/IsterXmlSimpleXMLImpl.php");
         $parser = new IsterXmlSimpleXMLImpl;
         $xml1 = $parser->load_string($xml_content);
         $xml = $xml1->ItemSearchResponse;
      } else {
         print "This metadata system requires PHP with the SimpleXML or plain libexpat XML extension installed.";
         exit;
      }
      if (empty ($xml)) {
         print "<div width=\"100%\" align=\"center\" style=\"background:#C00000;color:#FFFFFF;padding:3px\"><b>There was a problem fetching results from Amazon: <font color=\"red\">" . $snoopy->status . " " . $snoopy->error . "</font></b></div>";
         #print "<div width=\"100%\" align=\"center\" style=\"background:#C00000;color:#FFFFFF;padding:3px\">If you wish, you can report this error to the developer by clicking the following button.";
         #print '<form method="POST" action="http://www.darkhart.net/support.php"><input type="hidden" name="subject" value="JZ Custom - XML parse error"><input type="hidden" name="data" value="' . . '"><input type="submit" value="Submit Report"></form></div>';
         print "<code>htmlentities($xml_content)</code>";
         return false;
      }
   }
   return $xml;
}

function xml_data($data) {
   global $test_php4;
   if (!$test_php4 && extension_loaded('simplexml')) { 
      return $data;
   } else {
      if (isset($data)) { 
         return $data->CDATA(); 
      } else {
         return "";
      }
   }
}


/*
* Gets the metadata for an artist from Yahoo!
*
* @author Fred Hirsch
* @param $node The current node we are looking at
* @param $return should we return or write data (defaults to write),
*                and if return what do we return (image = binaryImageData, genre, description)
**/
//function SERVICE_GETARTISTMETADATA_webmosher($node, $displayOutput, $return = false){
function SERVICE_GETARTISTMETADATA_webmosher($node = false, $return = false, $artistName = false){
   global $include_path;

   include_once($include_path . "lib/utfnormal/UtfNormal.php");
   $utffix = new UTFNormal();

   // let's set the artist we're looking at
   if (is_object($node)){
      $artist = $node->getName();
   } else {
		$artist = $node['artist'];
   }	
   $artist = preg_replace("/\&/", 'and', $artist);

   $items = array();
   
   // Normally, we are probably not overriding our values, so just procede.
   if(empty($_POST[descOVERRIDE]) && empty($_POST[imgOVERRIDE])) {
      $fix_jz_path = urlencode(implode('/', $node->getPath()));
      print "<form action=\"popup.php?action=popup&ptype=getmetadata&jz_path=$fix_jz_path\" method=\"post\">\n";
      print "<input type=\"hidden\" name=\"edit_search_all_albums\" value=\"off\"/>\n";
      print "<input type=\"hidden\" name=\"edit_search_all_artists\" value=\"on\"/>\n";
      print "<input type=\"hidden\" name=\"metaSearchSubmit\" value=\"Search\"/>\n";
      print "<input type=\"hidden\" name=\"edit_search_images_miss\" value=\"always\"/>\n";
      print "<input type=\"hidden\" name=\"edit_search_desc_miss\" value=\"always\"/>\n";

      $search_artist = urlencode(strtolower(preg_replace('/[^\w\s]/', '', $utffix->toNFKD($artist))));
      $yahoo_search = "http://search.music.yahoo.com/search/?m=artist&x=0&y=0&p=". $search_artist;
      $content_y = getHTMLData($yahoo_search);
      $items['yahoo'] = parseYahooArtist($content_y, $artist);
      
      print "<h2>YAHOO!</h2>\n";
      print "<table width=\"100%\" cellspacing=\"0\" cellpadding=\"2\" border=\"0\">\n";
      print "<tr>\n";
      print "<td align=\"center\" valign=\"top\"><img width=\"150px\" src=\"" . $items['yahoo']['image'] . "\"/></td>\n";
      print "<td align=\"left\" valign=\"top\">" . substr($items['yahoo']['bio'],0,200) . "</td>\n";
      print "</tr><tr>\n";
      print "<td width=\"50%\" align=\"center\"><input type=\"radio\" name=\"imgOVERRIDE\" value=\"" . $items['yahoo']['image'] . "\">Override Image</td>\n";
      print "<td width=\"50%\" align=\"center\"><input type=\"radio\" name=\"descOVERRIDE\" value=\"" . $items['yahoo']['bio'] . "\">Override Bio</td>\n";
      print "</table><hr/>\n";

      // Rhapsody has some very specific artist name search requirements.
      $search_artist = urlencode(strtolower(preg_replace('/[^\w]/', '', $utffix->toNFKD($artist))));
      $rhaps_search = "http://www.rhapsody.com/" . strtolower(preg_replace('/[^\w]/', '', $search_artist)) . "/more.html";
      $content_r = getHTMLData($rhaps_search,$artist);
      $items['rhaps'] = parseRhapsodyArtist($content_r, $artist);
      
      print "<h2>Rhapsody</h2>\n";
      print "<table width=\"100%\" cellspacing=\"0\" cellpadding=\"2\" border=\"0\">\n";
      print "<tr>\n";
      print "<td align=\"center\" valign=\"top\"><img width=\"150px\" src=\"" . $items['rhaps']['image'] . "\"/></td>\n";
      print "<td align=\"left\" valign=\"top\">" . substr($items['rhaps']['bio'],0,200) . "</td>\n";
      print "</tr><tr>\n";
      print "<td width=\"50%\" align=\"center\"><input type=\"radio\" name=\"imgOVERRIDE\" value=\"". $items['rhaps']['image'] ."\">Override Image</td>\n";
      print "<td width=\"50%\" align=\"center\"><input type=\"radio\" name=\"descOVERRIDE\" value=\"". $items['rhaps']['bio'] ."\">Override Bio</td>\n";
      print "</table><hr/>\n";
      
      if (isset($items['yahoo']['bio']) && $items['yahoo']['bio'] != 'Not available.') {
         $bio = $items['yahoo']['bio'];
      } elseif (isset($items['rhaps']['bio']) && $items['rhaps']['bio'] != 'Not available.') {
         $bio = $items['rhaps']['bio'];
      } else {
         $bio = '';
      }
      if (isset($items['yahoo']['image']) && $items['yahoo']['image'] != '') {
         $image = $items['yahoo']['image'];
      } elseif (isset($items['rhaps']['image']) && $items['rhaps']['image'] != '') {
         $image = $items['rhaps']['image'];
      } else {
         $image = '';
      }
      print "<div align=\"center\"><input type=\"submit\" value=\"Override Default\" class=\"jz_submit\"/></div>";
      print "</form>";
      flushdisplay();

   } else {
      $retArr = artistOverride();
      print "<h2>OVERRIDE</h2>\n";
      print "<table width=\"100%\" cellspacing=\"0\" cellpadding=\"2\" border=\"0\">\n";
      print "<tr>\n";
      print "<td align=\"center\" valign=\"top\"><img width=\"150px\" src=\"" . (isset($retArr['image']) ? $retArr['image'] : $image) . "\"/></td>\n";
      print "<td align=\"left\" valign=\"top\">" . (isset($retArr['bio']) ? $retArr['bio'] : $bio) . "</td>\n";
      print "</tr><tr>\n";
      print "</table><hr/>\n";

   }
   
   // Now let's write the data
   if ($return){
      if ($return == "array"){
         if (empty($retArr['bio'])) {
            $retArr['bio'] = $bio;
         } 
         if (empty($retArr['image'])) {
            $retArr['image'] = $image;
         }
         return $retArr;
      } else {
         return $$return;
      }
      return $$return;
   } else {
      $artReturn = writeArtistMetaData($node, $image, $bio, $displayOutput);
   }
   return false;
}

function parseYahooArtist($contents,$artist) {
   $utffix = new UTFNormal();
   $artist_alternate = preg_replace('/[^\w\s]/', '', $utffix->toNFKD($artist));
   
   // Ok, now let's see if we got a direct hit or a link
   if (stristr($contents,$artist) || stristr($contents,$artist_alternate)){
      // Now let's see if we can get the right link
      //
      $artist_search = "<a href=\"http://music.yahoo.com/ar-";

      if (strpos($contents,$artist_search)) {
      $contents = substr($contents,strpos($contents,$artist_search) + 9);
      $link = trim(substr($contents,0,strpos($contents,"\">")));
      $link_bio = str_replace("---","-bio--",$link);
   
      // Now let's get the bio back
      $contents = getHTMLData($link_bio);

      $bio = substr($contents,strpos($contents,'width="401">'));
      $bio = substr($bio,strpos($bio,'<td>')+4);
      $bio = substr($bio,0,strpos($bio,'</td>'));
      $bio = strip_tags($bio);
      $bio = preg_replace("/(\r\n)+/m", "\n", $bio);
      #$bio = utf8_encode($bio);

      # Maybe there isn't a bio page.
      if (empty($bio) || $bio == '') {
         $bio = "Not available.";
      	$contents = getHTMLData($link);
      }
   
      // Now let's get the artist image
      $image = substr($contents,strpos($contents,'<td width="300"><img src="http://')+26);
      $image = substr($image,0,strpos($image,'"'));
   
      if (!stristr($image,".jpg") or !stristr($image,"http://")){
         $image = "";
      }}
      else {
        $bio = "Not available";
      }
   } else {
      $bio = "Not available.";
   }
   return array('bio'=> $bio, 'image' => $image);
}

function parseRhapsodyArtist($contents, $artist) {
   $utffix = new UTFNormal();
   $artist_alternate = preg_replace('/[^\w\s]/', '', $utffix->toNFKD($artist));
   if (stristr($contents,$artist) || stristr($contents,$artist_alternate)){
      $img_search = '<img class="artistPageFlyoutMainBgImage" src="';
      if (stristr($contents,$img_search)) {
         $image = substr($contents,strpos($contents, $img_search) + strlen($img_search));
         $image = substr($image,0,strpos($image,'"'));
      }

      $bio_search = '<h3 class="fontSize13">About</h3>';
      if (stristr($contents,$bio_search) ) {
         $bio = substr($contents,strpos($contents,$bio_search) + strlen($bio_search));
         $bio = substr($bio,0,strpos($bio,'</div>'));
         $bio = strip_tags($bio);
         $bio = preg_replace("/(\r\n)+/m", "\n", $bio);
         #$bio = utf8_encode($bio);
      } else {
         $bio = "Not available.";
      }
   // TODO search for the Rhapsody content if the main page did not load. 
   //} elseif (empty ($contents) || $contents == '') {
      //$utffix = new UTFNormal();
      //$search_artist = urlencode(strtolower(preg_replace('/[^\w\s]/', '', $utffix->toNFKD($artist))));
      //$link = 'http://www.rhapsody.com/-search?query=' . $search_artist . '&searchtype=RhapArtist';
      //$contents = getHTMLData($link);
   } else {
      $bio = "Not available.";
   }

   return array('bio'=> $bio, 'image' => $image);
}

function getHTMLData($search) {
   // Snoopy is used to robot the URL fetching
   include_once($include_path. "lib/snoopy.class.php");
   $snoopy_retry = 3;
   $snoopy = new Snoopy;
   $snoopy_tries = 0;
   while ($snoopy_retry > $snoopy_tries) {
      @$snoopy->fetch($search);
      $snoopy_tries++;
      if ($snoopy->status == 200) {
         $xml_content = $snoopy->results;
         break;
      } else {
         if ($snoopy->status) {
            print "<div width=\"100%\" align=\"center\" style=\"background:#C00000;color:#FFFFFF;padding:3px\"><b>There was a problem fetching results: <font color=\"red\">" . $snoopy->status . " " . $snoopy->error . "</font></b></div>";
            print "<div width=\"100%\" align=\"center\" style=\"background:#C08000;color:#FFFFFF;padding:3px\">We will retry this request " . $snoopy_retries - $snoopy_tries . " more times.</div>";
         } else {
            print "<b>There was a fatal error: <font color=\"red\">" . $php_errormsg . "</font></b><br>";
		      return false;
         }
      }
   }
   $contents = $snoopy->results;

   return utf8_encode($contents);
}

?>
