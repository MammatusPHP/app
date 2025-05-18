<?php

declare(strict_types=1);

return (static fn (): array => [
    'mammatus.random' => bin2hex(random_bytes(13)),
])();
