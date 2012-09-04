<?php
  // Mysql DB
  $user = 'your_user';
  $pw   = 'your_pw';
  $options = array(
      PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
  );

  $dsn = 'mysql:unix_socket=/tmp/mysql.sock;dbname=your_dbname';
