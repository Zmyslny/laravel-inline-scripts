<?php

declare(strict_types=1);

namespace Zmyslny\LaravelInlineScripts\Contracts;

interface RenderableScript
{
    public function render(): string;

    public function getName(): string;
}
