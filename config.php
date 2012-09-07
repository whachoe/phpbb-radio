<?php
  include_once 'db_config.php';
  
  // Mongo DB
  define('MONGO_DB', "breakzradio");
  define('MONGO_COLLECTION', "tracks");
  
  // Soundcloud API Key: This to be able to play soundcloud-links
  // If your forum doesn't have any links to soundcloud-urls, you can ignore this
  // 
  // Get one at http://soundcloud.com/you/apps
  define('SOUNDCLOUD_API_KEY', 'da7fd01a909b42f7145ae301fe2cff5c'); 
  
  // Various constants used in the website
  define("CONTACTEMAIL", "cjpa@breakzforum.be");
  define("CONTACTTITLE", "Write a love-letter to cjpa");
  define("SITE_TITLE", "Breakz Radio");
  
  // On my forum, these are forum-ids of private forums so we don't want to show them 
  // On your install, this will probably be different. Change to something suitable for your needs
  $forums_to_ignore = array("13","16","19","11","12");
  
  // Link to your forum: This is used to generate links to individual posts and forums in web/js/radio.js.php
  define('FORUM_URL', "http://www.breakzforum.be/forum"); // no trailing slash
  