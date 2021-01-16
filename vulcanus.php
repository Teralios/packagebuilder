#!/usr/bin/env php
<?php

require_once('./vendor/autoload.php');

use Teralios\Vulcanus\Config;
use Teralios\Vulcanus\Core;

$config = new Config(__FILE__, null);
$core = new Core($config);
$core->run();
