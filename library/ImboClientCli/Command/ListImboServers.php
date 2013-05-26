<?php
/**
 * ImboClientCli
 *
 * Copyright (c) 2011-2013, Christer Edvartsen <cogo@starzinger.net>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to
 * deal in the Software without restriction, including without limitation the
 * rights to use, copy, modify, merge, publish, distribute, sublicense, and/or
 * sell copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * * The above copyright notice and this permission notice shall be included in
 *   all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS
 * IN THE SOFTWARE.
 *
 * @package Commands
 * @author Christer Edvartsen <cogo@starzinger.net>
 * @copyright Copyright (c) 2011-2013, Christer Edvartsen <cogo@starzinger.net>
 * @license http://www.opensource.org/licenses/mit-license MIT License
 * @link https://github.com/imbo/imboclient-php-cli
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
 * @copyright Copyright (c) 2011-2013, Christer Edvartsen <cogo@starzinger.net>
 * @license http://www.opensource.org/licenses/mit-license MIT License
 * @link https://github.com/imbo/imboclient-php-cli
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
