phpbb-radio
===========
 
## Make a radio from your PHPBB-forum
These scripts parse all the posts on a phpbb-forum and filter out links to mp3's, soundcloud-links and embedded-soundcloud links ([soundcloud]url[/soundcloud]).
All urls are kept in a Mongodb (http://www.mongodb.org/). 

### Requirements:
*    PHP-5.3
*    MongoDB
*    a PHPBB-forum
*    Sass (http://sass-lang.com/) Not required but very handy to have. Makes working with CSS a lot more fun. just type sass --watch web/css/style.scss:web/css/style.css to automatically update your style.css file.


### Important files:
*    crawler.php: Connects to the mysql-database which hosts the phpbb-data, pulls out all the posts, parses them, checks the availability of the urls and finally saves them to Mongodb.
*    check_availability.php: Checks an existing Mongodb and tests the availability of the urls that are stored inside.
*    web/index.php: The web-frontend of the radio.
*    web/api: Contains the API which gets called by javascript to get new tracks. Here the good stuff happens.
*    web/js/radio.js: Contains all the javascript logic to get music from the API, play it and update the view dynamically
*    web/.htaccess: Redirects all calls to api/* to the rest-server in api/index.php
                                               * 
     If you run nginx, the following line should replace the .htaccess: 

        rewrite ^/api/.*$ /api/index.php last;

### Demo Site
http://radio.breakzforum.be




