<?php
/**
 * This file is part of the ImboClientCli package
 *
 * (c) Christer Edvartsen <cogo@starzinger.net>
 *
 * For the full copyright and license information, please view the LICENSE file that was
 * distributed with this source code.
 */

namespace ImboClientCli\Command;

use ImboClient\ImboClient,
    Symfony\Component\Console\Input\InputOption,
    Symfony\Component\Console\Input\InputInterface,
    Symfony\Component\Console\Output\OutputInterface,
    InvalidArgumentException;

/**
 * Base command for other ImboClientCli commands
 *
 * @package Commands
 * @author Christer Edvartsen <cogo@starzinger.net>
 */
abstract class RemoteCommand extends Command {
    /**
     * Information regarding the current server
     *
     * Elements included in this array are:
     *
     * (string) url The URL to the server
     * (string) publicKey The public key of the user performing the command
     * (string) privateKey The private key of the user performing the command
     * (boolean) active Whether or not the server is activated in the configuration
     * (string) name The name of the server in the configuration
     *
     * @var array
     */
    protected $server;

    /**
     * ImboClient instance used to perform remote operations
     *
     * @var ImboClient
     */
    protected $client;

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
            throw new InvalidArgumentException('No default server is configured. Please set up a default server or specify which one to use with --server.');
        }

        if (empty($servers[$server]) || $servers[$server]['active'] !== true) {
            throw new InvalidArgumentException('No active server called "' . $server . '" exists in the configuration.');
        }

        $this->server = $servers[$server];
        $this->server['name'] = $server;

        $output->write(array('Remote command on <info>' . $this->server['url'] . '</info>: ', ''), true);
    }

    /**
     * Get an instance of the ImboClient
     *
     * @return ImboClient
     */
    public function getclient() {
        if ($this->client === null) {
            $this->setClient(ImboClient::factory(array(
                'serverUrls' => array($this->server['url']),
                'publicKey' => $this->server['publicKey'],
                'privateKey' => $this->server['privateKey'],
            )));
        }

        return $this->client;
    }

    /**
     * Set the ImboClient instance
     *
     * @param ImboClient $client An instance of the client
     */
    public function setClient(ImboClient $client) {
        $this->client = $client;
    }
}
