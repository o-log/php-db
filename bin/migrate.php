<?php

// when file is in project_folder/bin
$project_autoload = __DIR__ . '/../vendor/autoload.php';
if (file_exists($project_autoload)) {
    require_once $project_autoload;
} else {
    // when file in project_folder/vendor/o-log/bin
    $vendor_autoload = __DIR__ . '/../../../../vendor/autoload.php';
    if (!file_exists($vendor_autoload)) {
        throw new \Exception('autoload.php not found, probably needs composer install');
    }
    require_once $vendor_autoload;
}

\Config\Config::init();

\OLOG\DB\MigrateCLI::run();