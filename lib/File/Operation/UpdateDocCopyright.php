<?php

namespace Teralios\Vulcanus\File\Operation;

class UpdateDocCopyright extends Operation
{
    protected string $extension = 'php';
    protected static string $copyrightString = '© %s %s';
    protected static ?string $replacement = null;

    public function action(): void
    {
        $regex = '#^([\s]{1}\*[\s]{1}@copyright).*?$#gm';
        if (static::$replacement === null) {
            static::setInformation('Teralios.de', date('Y'), null);
        }
        $replacement = ' * @copyright ' . static::$replacement;

        $this->content = preg_replace($regex, $replacement, $this->content);
    }

    public static function setInformation(string $text, string $startYear, ?string $endYear): void
    {
        if ($endYear === null) {
            $endYear = date('Y');
        }

        $year = $startYear . ($endYear > $startYear) ? ' - ' . $endYear : '';
        static::$replacement = sprintf(static::$copyrightString, $year, $text);
    }
}