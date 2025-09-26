<?php

declare(strict_types=1);

namespace Zmyslny\LaravelInlineScripts;

use Zmyslny\LaravelInlineScripts\Contracts\BladeDirectiveRegistrar;
use Zmyslny\LaravelInlineScripts\Contracts\RenderableScript;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use Throwable;
use Zmyslny\LaravelInlineScripts\Exceptions\BladeInlineScriptsException;

class BladeInlineScripts
{
    /** @var array<RenderableScript> */
    protected array $scripts = [];

    protected ?string $scriptTagId = null;

    protected BladeDirectiveRegistrar $bladeRegistrar;

    protected string $scriptsCode = '';

    protected bool $addHashToScriptId = true;

    public function __construct(
        RenderableScript ...$scripts
    ) {
        $this->scripts = $scripts;

        $this->setBladeRegistrar(app(BladeDirectiveRegistrar::class));
    }

    public function doNotAddHashToScriptId(): self
    {
        $this->addHashToScriptId = false;

        return $this;
    }

    public function registerAs(string $name): void
    {
        $this->bladeRegistrar->register($name, fn (): HtmlString => $this->renderScriptTag());
    }

    public function setBladeRegistrar(BladeDirectiveRegistrar $registrar): self
    {
        $this->bladeRegistrar = $registrar;

        return $this;
    }

    public function getBladeRegistrar(): BladeDirectiveRegistrar
    {
        return $this->bladeRegistrar;
    }

    public function renderScriptTag(): HtmlString
    {
        $scriptTagId = $this->getScriptTagId();

        $html = sprintf('<script id="%s">%s%s%s</script>', $scriptTagId, PHP_EOL, $this->getScriptsCombinedCode(), PHP_EOL);

        return new HtmlString($html);
    }

    public function getScriptTagId(): string
    {
        if ($this->scriptTagId !== null) {
            return $this->scriptTagId;
        }

        return $this->prepareScriptTagId();
    }

    /**
     * @throws Throwable
     */
    public function setScriptTagId(string $scriptTagId): self
    {
        throw_if(blank($scriptTagId), BladeInlineScriptsException::class, 'scriptTagId cannot be empty.');

        $this->scriptTagId = $scriptTagId;

        return $this;
    }

    public function getScriptsCombinedCode(): string
    {
        if (filled($this->scriptsCode)) {
            return $this->scriptsCode;
        }

        $this->scriptsCode = implode(PHP_EOL, array_map(
            fn (RenderableScript $script): string => $script->render(),
            $this->scripts
        ));

        return $this->scriptsCode;
    }

    protected function prepareScriptTagId(): string
    {
        $names = array_map(
            fn (RenderableScript $script) => Str::kebab($script->getName()),
            $this->scripts
        );

        $base = implode('-', $names);

        if ( ! $this->addHashToScriptId) {
            return $base;
        }

        $hash = $this->getScriptTagIdHash($this->getScriptsCombinedCode());

        return $base.'-'.$hash;
    }

    protected function getScriptTagIdHash(string $scriptsCode): string
    {
        return mb_substr(hash('xxh128', $scriptsCode), 0, 8);
    }
}
