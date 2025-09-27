<?php

declare(strict_types=1);

namespace Zmyslny\LaravelInlineScripts\ThemeSwitchTwoStates;

use InvalidArgumentException;
use Override;
use Throwable;
use Zmyslny\LaravelInlineScripts\Script\FromFileWithPlaceholders;

class ThemeSwitchScript extends FromFileWithPlaceholders
{
    public const string DEFAULT_KEY = 'd';

    public const string KEY_PATTERN = '/^[a-z]$/';

    protected string $fileName = 'theme-switch';

    protected string $fileDirectory = __DIR__.'/../../resources/js/theme-switch-two-states';

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
    #[Override]
    public function getPlaceholders(): array
    {
        return [
            '__TOGGLE_KEY__' => $this->key,
            '__DARK__' => ThemeTypeEnum::DARK->value,
            '__LIGHT__' => ThemeTypeEnum::LIGHT->value,
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
