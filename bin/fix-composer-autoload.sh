#!/bin/sh
# Remove composer/composer from the autoload to prevent Composer\ namespace
# conflicts with Composer's PHAR classes. See: https://github.com/composer/composer/issues/264
if [ -f vendor/composer/autoload_psr4.php ]; then
    sed -i "/'Composer\\\\\\\\' => array(\$vendorDir . '\/composer\/composer\/src\/Composer'),/d" vendor/composer/autoload_psr4.php
fi
