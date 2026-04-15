<?php 

function logger( $message ) {
    $upload_dir = wp_upload_dir();
    $wp_content_dir = dirname($upload_dir['basedir']);
    $log_file = $wp_content_dir . '/logger_log.txt'; // Set log file path

    $timestamp = date("Y-m-d H:i:s");
    file_put_contents($log_file, "[$timestamp] $message" . PHP_EOL, FILE_APPEND);
}
