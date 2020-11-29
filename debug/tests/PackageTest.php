<?php

namespace Teralios\Vulcanus\Tests;

// imports
use Teralios\Vulcanus\Package\Package;
use PHPUnit\Framework\TestCase;
use Teralios\Vulcanus\Package\PackageException;

/**
 * Class        PackageTest
 * @package     pb
 * @subpackage  Teralios\Vulcanus\Tests
 * @author      Karsten (Teralios) Achterrath
 * @copyright   ©2020 Teralios.de
 * @license     GNU General Public License <https://www.gnu.org/licenses/gpl-3.0.txt>
 *
 * @covers Teralios\Vulcanus\Package\Package
 */
final class PackageTest extends TestCase
{
    protected ?Package $package = null;

    public function setUp(): void
    {
        $this->package = new Package('../data1/');
    }

    public function testValidatePath(): void
    {
        // no package file was found
        $this->expectException(PackageException::class);
        $package = new Package('./');
    }

    public function testReadPackageXML(): void
    {
        // default package
        $this->assertEquals('de.teralios.package.test', $this->package->getID());
        $this->assertEquals('Test package.xml for Vulcanus', $this->package->getName());
        $this->assertEquals('1.0.0', $this->package->getVersion());
    }

    public function testReadPackageXMLAlt(): void
    {
        // deleted packagename and version
        $package = new Package('../data2/');
        $this->assertEquals($package->getID(), $package->getName());
        $this->assertEquals('unknown', $package->getVersion());
    }
}
