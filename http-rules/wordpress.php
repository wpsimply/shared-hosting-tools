<?php

// Run from wp-config.php after ABSPATH has been defined.
// Only requests that load wp-config.php reach these rules.
$document_root = rtrim($_SERVER['DOCUMENT_ROOT'] ?? '', '/');
$wordpress_root = rtrim(ABSPATH, '/') . '/';
$script_filename = $_SERVER['SCRIPT_FILENAME'] ?? '';
$wordpress_script = str_starts_with($script_filename, $wordpress_root)
    ? substr($script_filename, strlen($wordpress_root))
    : null;

// Allow only the public and admin entry points from wordpress.inc.conf.
// Keep paths case-sensitive, even though the PHP extension match is not.
if (
    preg_match('~\.php$~i', $script_filename) === 1
    && $script_filename !== $document_root . '/index.php'
    && !in_array($wordpress_script, [
        'wp-comments-post.php',
        'wp-login.php',
        'wp-admin/admin-ajax.php',
        'wp-admin/admin-post.php',
        'wp-admin/admin.php',
        'wp-admin/async-upload.php',
        'wp-admin/authorize-application.php',
        'wp-admin/comment.php',
        'wp-admin/customize.php',
        'wp-admin/edit-comments.php',
        'wp-admin/edit-tags.php',
        'wp-admin/edit.php',
        'wp-admin/erase-personal-data.php',
        'wp-admin/export-personal-data.php',
        'wp-admin/export.php',
        'wp-admin/font-library.php',
        'wp-admin/import.php',
        'wp-admin/index.php',
        'wp-admin/load-scripts.php',
        'wp-admin/load-styles.php',
        'wp-admin/media-new.php',
        'wp-admin/media-upload.php',
        'wp-admin/nav-menus.php',
        'wp-admin/options-connectors.php',
        'wp-admin/options-discussion.php',
        'wp-admin/options-general.php',
        'wp-admin/options-media.php',
        'wp-admin/options-permalink.php',
        'wp-admin/options-privacy.php',
        'wp-admin/options-reading.php',
        'wp-admin/options-writing.php',
        'wp-admin/options.php',
        'wp-admin/plugin-install.php',
        'wp-admin/plugins.php',
        'wp-admin/post-new.php',
        'wp-admin/post.php',
        'wp-admin/profile.php',
        'wp-admin/privacy-policy-guide.php',
        'wp-admin/revision.php',
        'wp-admin/site-editor.php',
        'wp-admin/site-health.php',
        'wp-admin/term.php',
        'wp-admin/theme-install.php',
        'wp-admin/themes.php',
        'wp-admin/tools.php',
        'wp-admin/update-core.php',
        'wp-admin/update.php',
        'wp-admin/upgrade.php',
        'wp-admin/upload.php',
        'wp-admin/user-edit.php',
        'wp-admin/user-new.php',
        'wp-admin/users.php',
        'wp-admin/widgets.php',
    ], true)
) {
    return 403;
}
