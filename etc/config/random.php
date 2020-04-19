<?php declare(strict_types=1);

return (static function (): array {
    return [
        'mammatus.random' => bin2hex(random_bytes(13)),
    ];
})();
