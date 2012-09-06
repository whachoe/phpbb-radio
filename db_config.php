<?php
  // Mysql DB
  define('MYSQL_USER', 'breakz_be');
  define('MYSQL_PW', 'reuteMeTeut');
  define('MYSQL_DSN', 'mysql:unix_socket=/tmp/mysql.sock;dbname=breakz_be_2009');
  
  $options = array(
      PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
  );
