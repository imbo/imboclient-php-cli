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

use Symfony\Component\Console\Input\InputInterface,
    Symfony\Component\Console\Output\OutputInterface;

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
        $status = $this->getClient()->getServerStatus();
        $output->write(array(
            'Date on the server: <info>' . $status['date']->format('r') . '</info>',
            'Database: ' . ($status['database'] ? '<info>ok</info>' : '<error>error</error>'),
            'Storage: ' . ($status['storage'] ? '<info>ok</info>' : '<error>error</error>'),
        ), true);
    }
}
