<?php

declare(strict_types=1);

namespace Mammatus;

use function glob;
use function is_array;
use function strpos;
use function WyriHaximus\get_in_packages_composer_path;

use const WyriHaximus\Constants\Boolean\FALSE_;
use const WyriHaximus\Constants\Boolean\TRUE_;

final class ConfigurationLocator
{
    /** @return iterable<string, string> */
    public static function locate(): iterable
    {
        yield from self::requires(get_in_packages_composer_path('extra.mammatus.config', TRUE_));
    }

    /**
     * @param iterable<string> $files
     *
     * @return iterable<string, string>
     */
    private static function requires(iterable $files): iterable
    {
        foreach ($files as $file) {
            yield from self::require($file);
        }
    }

    /** @return iterable<string, string> */
    private static function require(string $file): iterable
    {
        if (strpos($file, '*') !== FALSE_) {
            $glob = glob($file);
            if (is_array($glob)) {
                yield from self::requires($glob);

                return;
            }
        }

        /** @phpstan-ignore generator.keyType,generator.valueType */
        yield from require $file;
    }
}
