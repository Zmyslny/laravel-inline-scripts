<?php

declare(strict_types=1);

namespace Tests;

use Zmyslny\LaravelInlineScripts\BladeInlineScriptsProvider;

abstract class TestCase extends \Orchestra\Testbench\TestCase
{
    protected function getPackageProviders($app): array
    {
        return [BladeInlineScriptsProvider::class];
    }
}