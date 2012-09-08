<?php
	require_once __DIR__.'/../vendor/autoload.php';
	use Composer\Autoload;

	$loader = new \Composer\Autoload\ClassLoader();
	$loader->add('Radio', __DIR__.'/../libs');
	$loader->register();
 
	return $loader;
