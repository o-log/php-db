#!/usr/bin/php
<?php

require_once __DIR__ . '/../vendor/autoload.php';

\DBDemo\DBDemoConfig::init();

\OLOG\DB\MigrateCLI::run(__DIR__ . '/../');