<?php

declare(strict_types=1);

namespace Zmyslny\LaravelInlineScripts\Ready\ColorSchemeSwitchThreeStates;

enum SchemeTypeEnum: string
{
    case LIGHT = 'light';
    case DARK = 'dark';
    case SYSTEM = 'system';
}
