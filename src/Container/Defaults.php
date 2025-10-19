<?php

declare(strict_types=1);

namespace Mammatus\Container;

final class Defaults
{
    /** @return array<string, mixed> */
    public static function create(): array
    {
        return [
            'config.logger.handlers' => [],
        ];
    }
}
