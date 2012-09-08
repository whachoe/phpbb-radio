phpbb-radio
===========
 
## Make a radio from your PHPBB-forum
These scripts parse all the posts on a phpbb-forum and filter out links to mp3's, soundcloud-links and embedded-soundcloud links ([soundcloud]url[/soundcloud]).
All urls are kept in a [Mongodb](http://www.mongodb.org/). 
The frontend and JSON-API are made using the [Silex Microframework](http://silex.sensiolabs.org) which is based on Symfony2.

### Requirements:
*    PHP-5.3 (or higher)
*    MongoDB (on debian-based servers, the following is enough to get a working instance: `apt-get install mongodb`
*    a PHPBB-forum
*    [Sass](http://sass-lang.com/) Not required but very handy to have. Makes working with CSS a lot more fun. just type sass --watch web/css/style.scss:web/css/style.css to automatically update your style.css file.


### Important files:
*    db_config.php: Holds the credentials to your phpbb-database (there's a sample file included: db_config-sample.php)
*    config.php: Various configuration options. If you want to stream from soundcloud, be sure to get a [Soundcloud API Key](http://soundcloud.com/you/apps/ "Get Soundcloud API KEY") and set it in this file.
*    crawler.php: Connects to the mysql-database which hosts the phpbb-data, pulls out all the posts, parses them, checks the availability of the urls and finally saves them to Mongodb.
*    check_availability.php: Checks an existing Mongodb and tests the availability of the urls that are stored inside.
*    app/app.php: The web-frontend of the radio and implementation of the JSON-API
*    web/js/radio.js.php: Contains all the javascript logic to get music from the API, play it and update the view dynamically
*    web/.htaccess: Needed for routing everything to index.php
                                                
     If you run nginx, the following line should replace the .htaccess:   
        `rewrite ^/api/.*$ /api/index.php last;`

### Setup:
*   Set up a subdomain for your radio in your DNS and webserver
*   `mv db_config-sample.php db_config.php`
*   Edit db_config.php
*   Edit config.php
*   Make sure mongodb is running and then run (this will take a LONG time):
    `php crawler.php >list.log`
*   You can do `tail -f list.log` while running the crawler to check the progress
*   When the crawler is done, go check out your radio: http://your-radio-domain
*   I use the following line in crontab to refresh the list each night:  
    `* 3 * * * cd /path/to/the/source; php check_availability.php  && php crawler.php`
  
  
### Demo Site:
http://radio.breakzforum.be
