<?php

declare(strict_types=1);

namespace Zmyslny\LaravelInlineScripts\Ready\ColorSchemeSwitchThreeStates;

use InvalidArgumentException;
use Throwable;
use Zmyslny\LaravelInlineScripts\Script\FromFileWithPlaceholders;

class SwitchScript extends FromFileWithPlaceholders
{
    public const DEFAULT_KEY = 'd';

    public const KEY_PATTERN = '/^[a-z]$/';

    protected string $fileName = 'switch-script';

    protected string $fileDirectory = __DIR__.'/js';

    protected string $key;

    public function __construct(string $key = self::DEFAULT_KEY)
    {
        $this->setKey($key);

        parent::__construct();
    }

    /**
     * @throws Throwable
     */
    public function setKey(string $key): void
    {
        throw_if( ! $this->isAcceptableKey($key), InvalidArgumentException::class, sprintf('Key must be one letter from the %s pattern.', self::KEY_PATTERN));

        $this->key = $key;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * @return array<string,string>
     */
    public function getPlaceholders(): array
    {
        return [
            '__TOGGLE_KEY__' => $this->key,
            '__DARK__' => SchemeTypeEnum::DARK->value,
            '__LIGHT__' => SchemeTypeEnum::LIGHT->value,
        ];
    }

    /**
     * @throws Throwable
     */
    protected function isAcceptableKey(string $key): bool
    {
        if (blank($key) || mb_strlen($key) !== 1) {
            return false;
        }

        return preg_match(self::KEY_PATTERN, $key) === 1;
    }
}
