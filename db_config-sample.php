<?php
  // Mysql DB
  define('MYSQL_USER', 'your_phpbb_user');
  define('MYSQL_PW', 'your_phpbb_password');
  define('MYSQL_DSN', 'mysql:host=localhost;dbname=your_phpbb_databasename');
  
  $options = array(
      PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
  );

