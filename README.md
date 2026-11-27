# app

[![Continuous Integration](https://github.com/MammatusPHP/app/actions/workflows/ci.yaml/badge.svg)](https://github.com/MammatusPHP/app/actions/workflows/ci.yaml)
[![Latest Stable Version](https://poser.pugx.org/mammatus/app/v/stable.png)](https://packagist.org/packages/mammatus/app)
[![Total Downloads](https://poser.pugx.org/mammatus/app/downloads.png)](https://packagist.org/packages/mammatus/app/stats)
[![License](https://poser.pugx.org/mammatus/app/license.png)](https://packagist.org/packages/mammatus/app)

Main entry point for all MammatusPHP applications. This package provides the CLI binaries, async event-loop runtime, and dependency injection container that boot your application.

## Install

To install via [Composer](http://getcomposer.org/), use the command below:

```
composer require mammatus/app
```

## Usage

This package provides two CLI binaries:

- **`mammatus`**: run an application by group name
- **`mammatus-build`**: build/compile the DI container

### Running an application

```bash
mammatus group-name
```

The `group-name` argument specifies which [group](https://github.com/MammatusPHP/groups) to boot. The package will dispatch lifecycle events, set up the DI container, and start the event loop.

### Building the DI container

```bash
mammatus-build
```

Compiles the PHP-DI container for faster startup times. This generates `MammatusGeneratedCompiledContainer` in `src/Generated/`.

## Architecture

The boot flow follows this sequence:

1. **Binary entry point**: `mammatus` or `mammatus-build` loads the Composer autoloader
2. **Container creation**: `Factory::create()` builds the PHP-DI container with all definitions from `etc/di/*.php`
3. **Kernel event**: `Mammatus\LifeCycleEvents\Kernel` is dispatched synchronously
4. **Event loop**: ReactPHP's event loop starts with async future ticks
5. **Boot/Start event**: `Boot` event for `mammatus`, `Start` event for `mammatus-build`
6. **App boot**: the `App` or `Build` class boots, dispatching `Initialize` and booting the specified group
7. **Event loop runs**: handlers execute within the async event loop
8. **Shutdown**: the event loop completes and the exit code is returned

## Configuration

Configuration is loaded from `extra.mammatus.config` paths in `composer.json` across all installed packages. Each package can declare its own configuration files:

```json
{
  "extra": {
    "mammatus": {
      "config": [
        "etc/config/*.php"
      ]
    }
  }
}
```

DI definitions are loaded from `etc/di/*.php` files using the [php-di-definitions](https://github.com/php-di-definitions/definitions-gatherer) package.

## Lifecycle Events

The following events are dispatched during the boot process. All events come from the [mammatus/life-cycle-events](https://github.com/MammatusPHP/life-cycle-events) package.

### Event reference

| Event | Dispatched by | Dispatch mode | Built-in listeners |
|-------|---------------|---------------|--------------------|
| `Mammatus\LifeCycleEvents\Kernel` | `Boot::boot()` | Synchronous | None |
| `Mammatus\LifeCycleEvents\Boot` | `Boot::boot()` | Async (`Loop::futureTick`) | None |
| `Mammatus\LifeCycleEvents\Start` | `Boot::boot()` | Async (`Loop::futureTick`) | None |
| `Mammatus\LifeCycleEvents\Initialize` | `App::boot()` | Async (`Loop::futureTick`) | `Mammatus\LifeCycle\Output` (removes STD\* streams from the loop), `Mammatus\LifeCycle\Signals` (sets up SIGINT/SIGTERM handlers) |
| `Mammatus\LifeCycleEvents\Build` | `Build::build()` | Synchronous | `Mammatus\LifeCycle\Container` (generates the compiled PSR-11 container) |
| `Mammatus\LifeCycleEvents\Shutdown` | `Mammatus\LifeCycle\Signals` | On SIGINT/SIGTERM signal | None |

### Dispatch order

The `mammatus` and `mammatus-build` binaries follow different dispatch paths:

**`mammatus` binary** (run an application):

1. `Kernel` (synchronous)
2. `Boot` (async)
3. `Initialize` (async, via `App::boot()`)
4. Application runs in the event loop
5. `Shutdown` (on SIGINT/SIGTERM)

**`mammatus-build` binary** (compile the container):

1. `Kernel` (synchronous)
2. `Start` (async)
3. `Build` (synchronous, via `Build::build()`)
4. Process exits

Note that `Kernel` is the only event dispatched synchronously before the event loop starts. All other events fire asynchronously via `Loop::futureTick`, except `Shutdown` which is signal-driven.

### How to listen

Listeners implement the [WyriHaximus\Broadcast\Contracts\Listener](https://github.com/wyrihaximus/php-broadcast-contracts) marker interface and a `handle()` method that accepts the event class. Listeners are auto-discovered from the DI container via `ContainerListenerProvider`.

```php
<?php

declare(strict_types=1);

namespace App\Listener;

use Mammatus\LifeCycleEvents\Initialize;
use Psr\Log\LoggerInterface;
use WyriHaximus\Broadcast\Contracts\Listener;

final readonly class OnInitialize implements Listener
{
    public function __construct(
        private readonly LoggerInterface $logger,
    ) {
    }

    public function handle(Initialize $event): void
    {
        $this->logger->info('Application is initializing');
    }
}
```

Register the listener in the DI container by adding it to an `etc/di/*.php` file, and it will be automatically discovered and invoked when the event fires.

## Composer Plugin

The `Mammatus\Composer\ContainerCompiler` plugin compiles the DI container on `composer install` and `composer update`. It removes the generated container file and rebuilds it, ensuring the compiled container stays in sync with your dependencies.

To manually rebuild the container:

```bash
composer generate-container
```

## Contributing

Please read [CONTRIBUTING.md](CONTRIBUTING.md) for details on how to contribute.

## License

The MIT License (MIT)

Copyright © 2026 Cees-Jan Kiewiet

<!-- vale write-good = NO -->
Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

<!-- vale neighbor.AllCapsProse = NO -->
THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
<!-- vale neighbor.AllCapsProse = yes -->
<!-- vale write-good = yes -->
