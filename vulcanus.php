#!/usr/bin/env php
<?php

require_once('./vendor/autoload.php');

use Teralios\src\Package\Package;

$package = new Package('../de.teralios.quizCreator/');
$instructions = $package->getInstructions();

foreach ($instructions as $instruction) {
    echo $instruction->type . ':';
    if ($instruction->hasFiles()) {
        echo $instruction->getPath();
    } else {
        echo $instruction->getFileName();
    }

    echo "\n";
}
