<?php

// wp eval-file elementor-pro-check.php
// wp plugin list --fields=name,version | grep -i 'elementor'

use Elementor\Core\Utils\Version;
use Elementor\Modules\CompatibilityTag\Compatibility_Tag;
use ElementorPro\Plugin as Pro_Plugin;
use ElementorPro\Modules\CompatibilityTag\Compatibility_Tag_Component as Pro_Module;

wp_clean_plugins_cache(false);

$elementorProPluginInstance = Pro_Plugin::instance();

$compatibilityTagModule = $elementorProPluginInstance->modules_manager->get_modules('compatibility-tag');
$compatibilityCheckerComponent = $compatibilityTagModule->get_component('compatibility-tag-pro-handler');
$reflectionModule = new ReflectionObject($compatibilityCheckerComponent);
$getPluginsToCheckMethod = $reflectionModule->getMethod('get_plugins_to_check');
$getPluginsToCheckMethod->setAccessible(true);

$pluginsToCheck = $getPluginsToCheckMethod->invoke($compatibilityCheckerComponent);
$activePlugins = Pro_Plugin::elementor()->wp->get_active_plugins();

$compatibilityChecker = new Compatibility_Tag(Pro_Module::PLUGIN_VERSION_TESTED_HEADER);

foreach (
    $compatibilityChecker->check(
        Version::create_from_string(ELEMENTOR_PRO_VERSION),
        $pluginsToCheck->only($activePlugins->keys()->all())->keys()->all()
    ) as $pluginFile => $status
) {
    if ($status === Compatibility_Tag::COMPATIBLE) continue;
    WP_CLI::error(sprintf('%s: %s', $pluginFile, $status));
}
