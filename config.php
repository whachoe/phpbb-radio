<?php
  include_once 'db_config.php';
  
  // Mongo DB
  define('MONGO_DB', "breakzradio");
  define('MONGO_COLLECTION', "tracks");
  
  // Soundcloud API Key: This to be able to play soundcloud-links
  // If your forum doesn't have any links to soundcloud-urls, you can ignore this
  // 
  // Get one at http://soundcloud.com/you/apps
  define('SOUNDCLOUD_API_KEY', 'YOUR_SOUNDCLOUD_API_KEY');
  
  // Various constants used in the website
  define("CONTACTEMAIL", "cjpa@breakzforum.be");
  define("CONTACTTITLE", "Write a love-letter to cjpa");
  define("SITE_TITLE", "Breakz Radio");
  