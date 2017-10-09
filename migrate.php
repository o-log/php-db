<?php

require_once 'vendor/autoload.php';

\DBDemo\DBDemoConfig::init();

\OLOG\DB\MigrateCLI::run();