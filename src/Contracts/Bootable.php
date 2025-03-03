<?php

declare(strict_types=1);

namespace Mammatus\Contracts;

use Mammatus\ExitCode;

/** @template T of Argv */
interface Bootable
{
    /** @param T $argv */
    public function boot(Argv $argv): ExitCode;
}
