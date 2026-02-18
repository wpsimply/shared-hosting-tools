<?php

// wp eval-file list-woo-log-files.php

date_default_timezone_set('UTC');

$log_files = array_filter(
    WC_Log_Handler_File::get_log_files(),
    static function ($log_file) {
        return fnmatch('*-'.date('Y-m-d').'-*.log', $log_file);
    }
);

echo implode("\n", array_map(
    static function ($log_file) {
        return Automattic\WooCommerce\Utilities\LoggingUtil::get_log_directory().$log_file;
    },
    $log_files
));
