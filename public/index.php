<?php

date_default_timezone_set('Europe/Amsterdam');

require_once __DIR__ . '/../vendor/autoload.php';

exit((new Miniframe\Core\Bootstrap())->run(__DIR__ . '/../'));
