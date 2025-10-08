<?php

declare(strict_types=1);

namespace Zmyslny\LaravelInlineScripts\Ready\ColorSchemeSwitchThreeStates;

use Zmyslny\LaravelInlineScripts\Script\FromFileWithPlaceholders;

class InitScript extends FromFileWithPlaceholders
{
    protected string $fileName = 'init-script';

    protected string $fileDirectory = __DIR__.'/js';

    /**
     * @return array<string,string>
     */
    public function getPlaceholders(): array
    {
        return [
            '__DARK__' => SchemeTypeEnum::DARK->value,
            '__LIGHT__' => SchemeTypeEnum::LIGHT->value,
        ];
    }
}
