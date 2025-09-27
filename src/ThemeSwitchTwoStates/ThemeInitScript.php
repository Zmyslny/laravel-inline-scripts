<?php

declare(strict_types=1);

namespace Zmyslny\LaravelInlineScripts\ThemeSwitchTwoStates;

use Override;
use Zmyslny\LaravelInlineScripts\Script\FromFileWithPlaceholders;

class ThemeInitScript extends FromFileWithPlaceholders
{
    protected string $fileName = 'theme-init';

    protected string $fileDirectory = __DIR__.'/../../resources/js/theme-switch-two-states';

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
