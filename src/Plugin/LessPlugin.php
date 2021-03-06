<?php
/**
 * @copyright 2017 Hostnet B.V.
 */
declare(strict_types=1);

namespace Hostnet\Component\Resolver\Plugin;

use Hostnet\Component\Resolver\Builder\Step\LessBuildStep;
use Hostnet\Component\Resolver\Cache\CachedImportCollector;
use Hostnet\Component\Resolver\Import\BuiltIn\LessImportCollector;

/**
 * Plugin to enable less compiling. Compiles a less file into a css file.
 */
final class LessPlugin implements PluginInterface
{
    public function activate(PluginApi $plugin_api): void
    {
        $plugin_api->addBuildStep(new LessBuildStep());

        $less_collector = new LessImportCollector();
        if ($plugin_api->getConfig()->isDev()) {
            $less_collector = new CachedImportCollector($less_collector, $plugin_api->getCache());
        }
        $plugin_api->addCollector($less_collector);
    }
}
