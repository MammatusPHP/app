<?php

declare(strict_types=1);

namespace Mammatus\App;

use Mammatus\Contracts\Argv;

final readonly class Group implements Argv
{
    public function __construct(
        public string $group,
    ) {
    }
}
