<?php

declare(strict_types=1);

namespace Zmyslny\LaravelInlineScripts\Ready\ColorSchemeSwitchTwoStates;

use Zmyslny\LaravelInlineScripts\Script\FromFileWithPlaceholders;

class InitScript extends FromFileWithPlaceholders
{
    protected string $fileName = 'init-script';

    protected string $fileDirectory = __DIR__.'/../../resources/js/theme-switch-two-states';

    /**
     * @return array<string,string>
     */
    public function getPlaceholders(): array
    {
        return [
            '__DARK__' => ThemeTypeEnum::DARK->value,
            '__LIGHT__' => ThemeTypeEnum::LIGHT->value,
        ];
    }
}
