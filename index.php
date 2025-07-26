<?php

require_once 'config.php';
require_once 'vendor/autoload.php';

use Intervention\Image\ImageManagerStatic as Image;

$config = require 'config.php';
$token = $config['token'];
$apiURL = $config['apiURL'] . $token . '/';

Image::configure(['driver' => 'imagick']);

require_once 'handlers.php';