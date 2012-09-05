<?php 
  include_once '../../config.php';
  require '../../libs/RestServer.php';
  require 'apiController.php';

  $mode = 'debug'; // 'debug' or 'production'
  $server = new RestServer($mode);
  // $server->refreshCache(); // uncomment momentarily to clear the cache if classes change in production mode

  $server->addClass('apiController', '/api');
  $server->handle();
