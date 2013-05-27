#!/usr/bin/env php
<?php
/**
 * This file is part of the Imbo package
 *
 * (c) Christer Edvartsen <cogo@starzinger.net>
 *
 * For the full copyright and license information, please view the LICENSE file that was
 * distributed with this source code.
 */

namespace ImboClientCli;

// Autoloader
require __DIR__ . '/vendor/autoload.php';

// Run the main application
$application = new Application();
$application->run();
