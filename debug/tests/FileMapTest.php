<?php

namespace Teralios\Vulcanus\Tests;

// imports
use PHPUnit\Framework\TestCase;
use Teralios\Vulcanus\Package\FileMap;

class FileMapTest extends TestCase
{
    public function testAddFile()
    {
        $fileMap = new FileMap();
        $fileMap->addFile('test.php');

        foreach ($fileMap as $file) {
            $this->assertEquals('test.php', $file);
        }
    }

    public function testAddFiles()
    {
        $files = ['test.php', 'test.html', 'test.css'];
        $fileMap = new FileMap();
        $fileMap->addFiles($files);

        $currentIndex = 0;
        foreach ($fileMap as $file) {
            $this->assertEquals($files[$currentIndex], $file);
            $currentIndex++;
        }
    }

    public function testCount()
    {
        $fileMap = new FileMap();
        $fileMap->addFile('test.php');
        $this->assertCount(1, $fileMap);

        $fileMap->addFiles(['test.html', 'test.css']);
        $this->assertCount(3, $fileMap);
    }
}
