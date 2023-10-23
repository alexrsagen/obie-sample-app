<?php
declare(strict_types = 1);
namespace ObieSampleApp;
use Obie\Log;

/** @var App $app */
if (!($app = require dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'init.php')) return;

Log::info('Hello world!');