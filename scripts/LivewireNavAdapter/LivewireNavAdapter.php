<?php

declare(strict_types=1);

namespace Zmyslny\LaravelInlineScripts\Ready\LivewireNavAdapter;

use Zmyslny\LaravelInlineScripts\Script\FromFileWithPlaceholders;

class LivewireNavAdapter extends FromFileWithPlaceholders
{
    protected string $fileName = 'livewire-nav-adapter';

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
