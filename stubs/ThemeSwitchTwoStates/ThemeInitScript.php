<?php

declare(strict_types=1);

namespace App\Blade\ThemeSwitchTwoStates;

use ThemeSwitchTwoStates\ThemeTypeEnum;
use Zmyslny\LaravelInlineScripts\Script\FromFileWithPlaceholders;

class ThemeInitScript extends FromFileWithPlaceholders
{
    protected string $fileName = 'theme-init';

    public function __construct()
    {
        $this->fileDirectory = resource_path('js/theme-switch-two-states');

        parent::__construct();
    }

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
