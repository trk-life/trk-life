<?php

require_once dirname(dirname(__DIR__)) . '/app/config/bootstrap.php';

require_once __DIR__ . '/TestUtilsTrait.php';

use TrkLife\Config;

Config::loadYaml(__DIR__ . '/data/config.yml');
