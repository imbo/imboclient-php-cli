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

use Symfony\Component\Console\Input\InputInterface,
    Symfony\Component\Console\Output\OutputInterface,
    Symfony\Component\Console\Input\InputOption;

/**
 * Command used to list configured imbo servers
 *
 * @package Commands
 * @author Christer Edvartsen <cogo@starzinger.net>
 */
class ListImboServers extends Command {
    /**
     * Class constructor
     */
    public function __construct() {
        parent::__construct('list-servers');

        $this->setDescription('List configured servers');
        $this->addOption('show-private-keys', null, InputOption::VALUE_NONE, 'Whether or not to show private keys in the output');
        $this->setHelp('List the imbo servers found in the configuration file');
    }

    /**
     * Execute the command
     *
     * @see Symfony\Components\Console\Command\Command::execute()
     */
    protected function execute(InputInterface $input, OutputInterface $output) {
        $default = isset($this->configuration['servers']['default'])
                 ? $this->configuration['servers']['default']
                 : '';

        foreach ($this->configuration['servers'] as $server => $config) {
            if ($server === 'default') {
                continue;
            }

            // Prepare output
            $result = array(
                'Name: ' . $server . ($server == $default ? ' (default)' : ''),
                'URL: ' . $config['url'],
                'Active: ' . ($config['active'] ? 'yes' : 'no'),
                'Public key: ' . $config['publicKey'],
            );

            if ($input->getOption('show-private-keys')) {
                $result[] = 'Private key: ' . $config['privateKey'];
            }

            $result[] = '';

            $output->write($result, true);
        }
    }
}
