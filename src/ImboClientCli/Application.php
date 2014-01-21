<?php
/**
 * This file is part of the ImboClientCli package
 *
 * (c) Christer Edvartsen <cogo@starzinger.net>
 *
 * For the full copyright and license information, please view the LICENSE file that was
 * distributed with this source code.
 */

namespace ImboClientCli;

use ImboClientCli\Command,
    Symfony\Component\Console;

/**
 * Main application class
 *
 * @package Application
 * @author Christer Edvartsen <cogo@starzinger.net>
 */
class Application extends Console\Application {
    /**
     * Class constructor
     *
     * Register all commands and set up some global options
     */
    public function __construct() {
        parent::__construct('ImboClientCli', Version::VERSION);

        // Register commands
        $this->addCommands(array(
            new Command\Activate(),
            new Command\AddImage(),
            new Command\Deactivate(),
            new Command\DeleteImage(),
            new Command\ListServers(),
            new Command\NumImages(),
            new Command\ServerStatus(),
        ));

        // Add global options
        $this->getDefinition()->addOption(
            new Console\Input\InputOption(
                'config',
                null,
                Console\Input\InputOption::VALUE_OPTIONAL,
                'Path to configuration file'
            )
        );
    }
}
