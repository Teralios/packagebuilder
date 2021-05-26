<?php

namespace Teralios\Vulcanus\Tests;

use PHPUnit\Framework\TestCase;
use Teralios\Vulcanus\Package\Instruction;
use Teralios\Vulcanus\Package\Package;
use Teralios\Vulcanus\Package\Requirement;

class PackageTest extends TestCase
{
    protected Package $package;

    protected function setUp(): void
    {
        $this->package = new Package('../examples'); // basic package.xml of quizcreator.
    }

    public function testInvalidPackage(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new Package('../'); // no, here is no package.
    }

    public function testName(): void
    {
        self::assertEquals('de.teralios.quizCreator', $this->package->getName());
    }

    /**
     * @depends testName
     */
    public function testGetRequirements(): void
    {
        $requirements = $this->package->getRequirements();

        self::assertIsArray($requirements);
        self::assertContainsOnlyInstancesOf(Requirement::class, $requirements);
        self::assertContainsEquals(new Requirement('com.woltlab.wcf', '5.4.0 Beta 1'), $requirements);
    }

    /**
     * @depends testGetRequirements
     */
    public function testGetInstructions(): void
    {
        $instructions = $this->package->getInstructions();

        // base information
        self::assertIsArray($instructions);
        self::assertCount(13, $instructions);
        self::assertContainsOnlyInstancesOf(Instruction::class, $instructions);

        // samples
        self::assertContainsEquals(new Instruction('file', ''), $instructions);
        self::assertContainsEquals(new Instruction('sql', 'install.sql'), $instructions);
    }

    /**
     * @depends testGetInstructions
     */
    public function testInstructions(): void
    {
        $instructions = $this->package->getInstructions();

        foreach ($instructions as $instruction) {
            if (in_array($instruction->type, ['file', 'template', 'acpTemplate', 'language'])) {
                self::assertTrue($instruction->hasFiles());
                self::assertNull($instruction->getFileName());
                self::assertNotEmpty($instruction->getPath());
            } else {
                self::assertFalse($instruction->hasFiles());
                self::assertNotEmpty($instruction->getFileName());
                self::assertNull($instruction->getPath());
            }
        }
    }

    /**
     * @depends testInstructions
     */
    public function testInstructionGetFileName(): void
    {
        $files = [
            'sql' => 'install.sql',
            'templateListener' => 'templateListener.xml',
            'page' => 'page.xml',
            'acpMenu' => 'acpMenu.xml',
            'menuItem' => 'menuItem.xml',
            'userGroupOption' => 'userGroupOption.xml',
            'option' => 'option.xml',
            'objectType' => 'objectType.xml',
            'box' => 'box.xml'];

        $instructions = $this->package->getInstructions();
        foreach ($instructions as $instruction) {
            if (isset($files[$instruction->type])) {
                self::assertEquals($files[$instruction->type], $instruction->getFileName());
            }
        }
    }

    /**
     * @depends testInstructions
     */
    public function testInstructionGetPath(): void
    {
        $paths = [
            'template' => 'templates/',
            'acpTemplate' => 'acptemplates/',
            'file' => 'files/',
            'language' => 'language/'
        ];

        $instructions = $this->package->getInstructions();
        foreach ($instructions as $instruction) {
            if (isset($paths[$instruction->type])) {
                self::assertEquals($paths[$instruction->type], $instruction->getPath());
            }
        }
    }

    public function testInstructionToMainArchive(): void
    {
        $paths = [
            'template' => false,
            'acpTemplate' => false,
            'file' => false,
            'language' => true,
        ];

        $instructions = $this->package->getInstructions();
        foreach ($instructions as $instruction) {
            if (isset($paths[$instruction->type])) {
                self::assertEquals($paths[$instruction->type], $instruction->toMainArchive());
            }
        }
    }
}