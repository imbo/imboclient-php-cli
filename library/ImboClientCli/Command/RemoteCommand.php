<?php
/**
 * This file is part of the Imbo package
 *
 * (c) Christer Edvartsen <cogo@starzinger.net>
 *
 * For the full copyright and license information, please view the LICENSE file that was
 * distributed with this source code.
 */

namespace ImboClientCli\Command;

use Symfony\Component\Console\Input\InputOption,
    Symfony\Component\Console\Input\InputInterface,
    Symfony\Component\Console\Output\OutputInterface,
    InvalidArgumentException,
    RuntimeException;

/**
 * Base command for other ImboClientCli commands
 *
 * @package Commands
 * @author Christer Edvartsen <cogo@starzinger.net>
 */
abstract class RemoteCommand extends Command {
    /**
     * The name of the current server (value of the server option added below)
     *
     * @var string
     */
    protected $server;

    /**
     * Add the "server" option to all remote commands
     *
     * @see Symfony\Components\Console\Command\Command::configure()
     */
    protected function configure() {
        $this->addOption(
            'server',
            null,
            InputOption::VALUE_OPTIONAL,
            'Which configured imbo server to add the image to. If not specified the default server will be used'
        );
    }

    /**
     * Initialization method
     *
     * This method is triggered before any commands will be executed. It will choose which server
     * to run the command against based on the server option added to this class.
     *
     * The chosen server will be stored in the server attribute.
     *
     * @see Symfony\Components\Console\Command\Command::initialize()
     */
    protected function initialize(InputInterface $input, OutputInterface $output) {
        parent::initialize($input, $output);

        $servers = $this->configuration['servers'];
        $default = isset($servers['default'])
                 ? $servers['default']
                 : '';

        $server = $input->getOption('server');

        if (!$server) {
            $server = $default;
        }

        if (!$default) {
            throw new RuntimeException('No default server is configured. Please set up a default server or specify which one to use with --server.');
        }

        if (empty($servers[$server]) || $servers[$server]['active'] !== true) {
            throw new InvalidArgumentException('No active server called "' . $server . '" exists in the configuration.');
        }

        $this->server = $servers[$server];
        $this->server['name'] = $server;
    }
}
