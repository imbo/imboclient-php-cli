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
    Symfony\Component\Console\Output\OutputInterface,
    Symfony\Component\Console\Input\InputArgument,
    Symfony\Component\Yaml\Dumper,
    InvalidArgumentException;

/**
 * Command used to activate a server on the config file
 *
 * @package Commands
 * @author Christer Edvartsen <cogo@starzinger.net>
 */
class Activate extends Command {
    /**
     * Class constructor
     */
    public function __construct() {
        parent::__construct('activate');

        $this->setDescription('Activate an imbo server');
        $this->setHelp('Activate an imbo server in the configuration file');
        $this->addArgument('server', InputArgument::REQUIRED, 'The imbo server to activate');
    }

    /**
     * Execute the command
     *
     * @see Symfony\Components\Console\Command\Command::execute()
     */
    protected function execute(InputInterface $input, OutputInterface $output) {
        $server = $input->getArgument('server');

        if (!isset($this->configuration['servers'][$server])) {
            $output->writeln('<error>There is no server named ' . $server . ' in the configuration file.</error>');
            return 1;
        }

        if ($this->configuration['servers'][$server]['active']) {
            $output->writeln('<error>The server is already activated.</error>');
            return 1;
        }

        // Activate the server
        $this->configuration['servers'][$server]['active'] = true;

        $dumper = new Dumper();
        $yaml = $dumper->dump($this->configuration, 2);

        if (!@file_put_contents($this->configPath, $yaml)) {
            $output->writeln('<error>An error occured. The configuration file was not updated.</error>');
            return 1;
        } else {
            $output->writeln('The configuration file has been updated.');
        }
    }
}
