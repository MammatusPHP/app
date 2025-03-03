<?php

declare(strict_types=1);

namespace Mammatus;

enum ExitCode: int
{
    case Success            = 0;
    case Failure            = 1;
    case ContingencyFailure = 2;
}
