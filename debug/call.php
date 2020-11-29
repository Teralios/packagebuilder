<?php

require_once('../vendor/autoload.php');

use Teralios\Vulcanus\Package\Package;

$package = new Package('./data1/');
print_r($package);