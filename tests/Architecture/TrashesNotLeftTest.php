<?php

declare(strict_types=1);

test('debug functions are not used')
    ->expect(['dd', 'dump', 'ray', 'var_dump', 'var_export'])
    ->not->toBeUsed();
