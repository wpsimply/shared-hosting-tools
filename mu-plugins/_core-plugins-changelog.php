<?php

/*
 * Plugin Name: Add plugin changelog links
 * Plugin URI: https://github.com/szepeviktor/wordpress-website-lifecycle
 */

add_filter(
    'plugin_row_meta',
    static function ($plugin_meta, $plugin_file) {
        switch ($plugin_file) {
            case 'wp-rocket/wp-rocket.php':
                $plugin_meta[] = '<a href="https://wp-rocket.me/changelog/" target="_blank">Changelog</a>';
                break;
            case 'elementor-pro/elementor-pro.php':
                $plugin_meta[] = '<a href="https://elementor.com/pro/changelog/" target="_blank">Changelog</a>';
                break;
            case 'hellopack-client/hellopack-client.php':
                $plugin_meta[] = '<a href="https://hellowp.io/hu/hellopack-changelog/" target="_blank">Changelog</a>';
                break;
            case 'automatewoo/automatewoo.php':
                $plugin_meta[] = '<a href="https://dzv365zjfbd8v.cloudfront.net/changelogs/automatewoo/changelog.txt" target="_blank">Changelog</a>';
                break;
            case 'wordpress-seo-premium/wp-seo-premium.php':
                $plugin_meta[] = '<a href="https://developer.yoast.com/changelog/yoast-seo-premium/" target="_blank">Changelog</a>';
                break;
            case 'wpseo-woocommerce/wpseo-woocommerce.php':
                $plugin_meta[] = '<a href="https://developer.yoast.com/changelog/woocommerce-seo/" target="_blank">Changelog</a>';
                break;
            case 'woocommerce-subscriptions/woocommerce-subscriptions.php':
                $plugin_meta[] = '<a href="https://dzv365zjfbd8v.cloudfront.net/changelogs/woocommerce-subscriptions/changelog.txt" target="_blank">Changelog</a>';
                break;
            case 'woocommerce-payments/woocommerce-payments.php':
                $plugin_meta[] = '<a href="https://plugins.svn.wordpress.org/woocommerce-payments/trunk/changelog.txt" target="_blank">Changelog</a>';
                break;
        }
        return $plugin_meta;
    },
    20,
    2
);
