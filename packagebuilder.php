#!/usr/bin/env php
<?php
// print on new line
function println(?string $text = null): void
{
    echo "$text \r\n";
}

// get path to package
function getArg(string $text): string
{
    $argument = trim(readline($text . ' : '));
    readline_add_history($text);

    return $argument;
}

// add trailing slashes
function addTrailingSlash(string $path): string
{
    if (substr($path, -1) !== '/') {
        return $path . '/';
    }

    return $path;
}

// unify dir separator
function unifyDir(string $path): string
{
    $path = str_replace('\\\\', '/', $path);
    return str_replace('\\', '/', $path);
}

// packs a folder to a dir
function packDir(string $dir, string $basePath, PharData $archive, bool $addDir = false)
{
    $dirs = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($basePath . $dir));

    foreach ($dirs as $file) {
        /** @var $file SplFileInfo */
        if (!$file->isFile()) {
            continue;
        }

        $removePath = addTrailingSlash(unifyDir($basePath . (($addDir === true) ? '' : $dir)));
        $archiveName = unifyDir(addTrailingSlash($file->getPath()) . $file->getFilename());
        $archiveName = str_replace($removePath, '', $archiveName);
        $archive->addFile($file->getRealPath(), $archiveName);
    }
}

// archives packaed. cleanup
$archivesPacked = [];

// paths to pack in own archives.
$pathsForPackingRegex = '#^((.*?)templates|(.*?)files)$#';

// files to exclude
$excludedFiles = ['.*', '*.md', 'license'];

// paths to pack in main
$pathsForPackage = ['language'];

println('-- Teralios Package Builder for WoltLab® Suite Core --');
println('                 Current 1.0.0 Beta 1                 ');
println();
println();

// Ask for path to package
$packageFolder = addTrailingSlash(getArg('Path to package'));
$packageXML = $packageFolder . 'package.xml';

// check folder
if (!file_exists($packageFolder) || !file_exists($packageXML)) {
    println('Package not found.');
    println('Finished');
    exit;
}

// read name
$xml = simplexml_load_file($packageXML);
$packageName = $xml->attributes()->name;

println('Start packing ' . $packageName . ' ...');

// pack dirs to own archives
$dirs = new DirectoryIterator($packageFolder);
foreach ($dirs as $dir) {
    if (preg_match($pathsForPackingRegex, $dir)) {
        println('Start packing ' . $dir);

        $archiveName = $packageFolder . $dir . '.tar';
        $archive = new PharData($archiveName);

        packDir($dir, $packageFolder, $archive);

        $archivesPacked[] = $archiveName;
        println($dir . 'is packed as ' . $archiveName);
        unset($archive);
    }
}

// pack final package
println('Start packing final package');
$archiveName = $packageName . '.tar';
$archive = new PharData($packageFolder . $archiveName);

$dirs = new DirectoryIterator($packageFolder);
foreach ($dirs as $dir) {
    $name = $dir->getFilename();

    // not need . and ..
    if ($name == '.' || $name == '..') {
        continue;
    }

    if ($dir->isDir() && in_array($name, $pathsForPackage)) {
        packDir($name, $packageFolder, $archive, true);
    }

    // files exclude from package.
    if ($dir->isFile()) {
        // exclude files
        $nextFile = false;
        foreach ($excludedFiles as $exName) {
            // ignore file, when it end with $exFile
            if (substr($exName, 0, 1) == '*') {
                $exName = substr($exName, 1);
                $start = 0 - strlen($exName);
                if (substr($name, $start) == $exName) {
                    $nextFile = true;
                    continue;
                }
            }

            // ignore files starts with $exFile
            if (substr($exName, -1) == '*') {
                $exName = substr($exName, 0, -1);
                if (substr($name, 0, strlen($exName)) == $exName) {
                    $nextFile = true;
                    continue;
                }
            }

            // ignore falls
            if (mb_strtolower($exName) == mb_strtolower($name)) {
                $nextFile = true;
                continue;
            }
        }

        if ($nextFile) {
            continue;
        } else {
            $archive->addFile($dir->getRealPath(), $name);
        }
    }
}

// finished
unset($archive);
println('Package packed.');
println('Cleanup ...');

// clean up
foreach ($archivesPacked as $archive) {
    println($archive);
    unlink($archive);
}
