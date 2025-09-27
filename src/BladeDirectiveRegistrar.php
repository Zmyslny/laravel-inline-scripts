<?php

declare(strict_types=1);

namespace Zmyslny\LaravelInlineScripts;

use Illuminate\Contracts\Support\Htmlable;
use Illuminate\View\Compilers\BladeCompiler;
use Throwable;
use Zmyslny\LaravelInlineScripts\Contracts\BladeDirectiveRegistrar as BladeDirectiveRegistrarInterface;
use Zmyslny\LaravelInlineScripts\Exceptions\BladeInlineScriptsException;

class BladeDirectiveRegistrar implements BladeDirectiveRegistrarInterface
{
    public function __construct(public BladeCompiler $blade) {}

    /**
     * @param  string  $name  The name of the Blade directive to register
     * @param  callable():(string|\Illuminate\Support\HtmlString)  $renderer
     *
     * @throws Throwable
     */
    public function register(string $name, callable $renderer): void
    {
        throw_if(blank($name), BladeInlineScriptsException::class, 'Directive name cannot be empty.');

        $this->blade->directive($name, static function () use ($renderer) {
            $result = $renderer();

            if ($result instanceof Htmlable) {
                return $result->toHtml();
            }

            return (string) $result;
        });
    }
}
