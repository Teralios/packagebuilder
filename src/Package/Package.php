<?php

namespace Teralios\Vulcanus\Package;

use Teralios\Vulcanus\Helper;

class Package
{
    // base package file name
    protected const PACKAGE_FILE = 'package.xml';

    // package.xml
    protected string $packageFile;
    protected \DOMDocument $packageXML;

    // package information
    protected string $name = '';
    protected array $requirements = [];
    protected array $instructions = [];


    public function __construct(protected string $packagePath)
    {
        $this->packagePath = Helper::addTrailingSlash(Helper::unifySeparators($packagePath));
        $this->packageFile = $this->packagePath . self::PACKAGE_FILE;

        $this->checkPackagePath();
        $this->loadXML();
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return Requirement[]
     */
    public function getRequirements(): array
    {
        return $this->requirements;
    }

    /**
     * @return Instruction[]
     */
    public function getInstructions(): array
    {
        return $this->instructions;
    }

    protected function checkPackagePath(): void
    {
        if (\file_exists($this->packageFile) === false) {
            throw new \InvalidArgumentException('"' . $this->packagePath . '" is not a valid path.');
        }
    }

    protected function loadXML(): void
    {
        // clear current errors
        \libxml_use_internal_errors(true);
        \libxml_clear_errors();

        $domDocument = new \DOMDocument('1.0', 'UTF-8');
        $domDocument->loadXML(file_get_contents($this->packageFile));
        $domDocument->schemaValidate('https://raw.githubusercontent.com/WoltLab/WCF/master/XSD/package.xsd'); // current best option.

        // check errors
        $xmlErrors = \libxml_get_errors();
        if (count($xmlErrors)) {
            throw new \InvalidArgumentException('Invalid package.xml: "' . $this->packageFile . '"');
        }

        $this->packageXML = $domDocument;

        $this->parseXML();
    }

    protected function parseXML(): void
    {
        $xPath = new \DOMXpath($this->packageXML);
        $xPath->registerNamespace('ns', 'http://www.woltlab.com');

        // read name space.
        $elements = $xPath->query('/ns:package');
        if (!empty($elements)) {
            $this->name = Helper::getAttribute('name', $elements->item(0)) ?? '';
        }

        // read requirements
        $requirements = $xPath->query('//ns:requiredpackages/ns:requiredpackage');
        if (!empty($requirements)) {
            foreach ($requirements as $requirement) {
                /** @var \DOMNode $requirement */
                $name = $requirement->nodeValue;
                $minversion = Helper::getAttribute('minversion', $requirement);
                $this->requirements[] = new Requirement($name, $minversion);
            }
        }

        // read instructions
        $instructions = $xPath->query('//ns:instructions[@type="install"]/ns:instruction');
        if (!empty($instructions)) {
            foreach ($instructions as $instruction) {
                /** @var \DOMNode $instruction */
                $type = Helper::getAttribute('type', $instruction) ?? '';
                $value = $instruction->nodeValue ?? '';

                if (!empty($type)) {
                    $this->instructions[] = new Instruction($type, $value);
                }
            }
        }
    }
}
