<?php

declare(strict_types=1);

namespace Zmyslny\LaravelInlineScripts\Examples\ThemeSwitchTwoStates;

use Override;
use Zmyslny\LaravelInlineScripts\Script\FromFileWithPlaceholders;

class ThemeInitScript extends FromFileWithPlaceholders
{
    protected string $fileName = 'themeInit';

    protected string $fileDirectory = __DIR__;

    /**
     * @return array<string,string>
     */
    #[Override]
    public function getPlaceholders(): array
    {
        return [
            '__DARK__' => ThemeTypeEnum::DARK->value,
            '__LIGHT__' => ThemeTypeEnum::LIGHT->value,
        ];
    }
}
