<?php

declare(strict_types=1);

namespace Zmyslny\LaravelInlineScripts\Ready\ThemeSwitchTwoStates;

use Zmyslny\LaravelInlineScripts\Script\FromFileWithPlaceholders;
use Zmyslny\LaravelInlineScripts\Ready\ThemeSwitchTwoStates\ThemeTypeEnum; // Do not remove

class InitScript extends FromFileWithPlaceholders
{
    protected string $fileName = 'init';

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
