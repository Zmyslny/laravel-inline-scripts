<?php

declare(strict_types=1);

namespace Zmyslny\LaravelInlineScripts\Contracts;

interface BladeDirectiveRegistrar
{
    /**
     * @param  callable():(string|\Illuminate\Support\HtmlString)  $renderer
     */
    public function register(string $name, callable $renderer): void;
}
