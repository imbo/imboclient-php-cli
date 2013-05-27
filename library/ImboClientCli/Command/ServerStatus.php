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

use ImboClient\Client as ImboClient,
    Symfony\Component\Console\Input\InputInterface,
    Symfony\Component\Console\Output\OutputInterface,
    RuntimeException;

/**
 * Command used to check server status
 *
 * @package Commands
 * @author Christer Edvartsen <cogo@starzinger.net>
 */
class ServerStatus extends RemoteCommand {
    /**
     * Class constructor
     */
    public function __construct() {
        parent::__construct('server-status');

        $this->setDescription('Check server status');
        $this->setHelp('Check the current server status (database and storage status)');
    }

    /**
     * Execute the command
     *
     * @see Symfony\Components\Console\Command\Command::execute()
     */
    protected function execute(InputInterface $input, OutputInterface $output) {
        $client = new ImboClient($this->server['url'], $this->server['publicKey'], $this->server['privateKey']);

        try {
            $status = $client->getServerStatus();
        } catch (RuntimeException $e) {
            $output->writeln('An error occured. Could not complete the action.');
            return;
        }

        $output->write(array(
            'Date: ' . $status['date'],
            'Database: ' . ($status['database'] ? '<info>ok</info>' : '<error>error</error>'),
            'Storage: ' . ($status['storage'] ? '<info>ok</info>' : '<error>error</error>'),
        ), true);
    }
}
