<?php

declare(strict_types=1);

namespace Zmyslny\LaravelInlineScripts\Examples;

use InvalidArgumentException;
use Override;
use Throwable;
use Zmyslny\LaravelInlineScripts\FileScript\FromFileWithPlaceholders;

class ThemeSwitchScript extends FromFileWithPlaceholders
{
    public const string DEFAULT_KEY = 'd';

    public const string KEY_PATTERN = '/^[a-z]$/';

    protected string $fileName = 'themeSwitch';

    protected string $fileDirectory = __DIR__;

    public function __construct(
        public string $key = self::DEFAULT_KEY {
            set {
                throw_if( ! $this->isAcceptableKey($value), InvalidArgumentException::class, sprintf('Key must be one letter from the %s pattern.', self::KEY_PATTERN));

                $this->key = $value;
            }
        }
    )
    {
        parent::__construct();
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
