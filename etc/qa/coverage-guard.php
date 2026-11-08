<?php

declare(strict_types=1);

use PhpParser\Node;
use PhpParser\Node\Stmt\Catch_;
use ShipMonk\CoverageGuard\Config;
use ShipMonk\CoverageGuard\Excluder\ExcludedLineRange;
use ShipMonk\CoverageGuard\Excluder\ExclusionContext;
use ShipMonk\CoverageGuard\Excluder\ExecutableLineExcluder;
use ShipMonk\CoverageGuard\Excluder\IgnoreThrowNewExceptionLineExcluder;
use ShipMonk\CoverageGuard\Rule\EnforceCoverageForMethodsRule;

return (static function (): Config {
    $config = new Config();

    $config->addRule(new EnforceCoverageForMethodsRule(
        requiredCoveragePercentage: 80,
        minExecutableLines: 1,
    ));

    $config->addExecutableLineExcluder(new IgnoreThrowNewExceptionLineExcluder([
        RuntimeException::class,
    ]));

    $config->addExecutableLineExcluder(new class implements ExecutableLineExcluder {
        public function getExcludedLineRange(Node $node, ExclusionContext $context): ExcludedLineRange|null
        {
            if (! $node instanceof Catch_) {
                return null;
            }

            if (str_contains($context->getFilePath(), 'Composer/ContainerCompiler.php') === false) {
                return null;
            }

            return new ExcludedLineRange($node->getStartLine(), $node->getEndLine());
        }
    });

    return $config;
})();
