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

use ImboClient\Client as ImboClient,
    Symfony\Component\Console\Input\InputInterface,
    Symfony\Component\Console\Output\OutputInterface,
    RuntimeException;

/**
 * Command used to check server status
 *
 * @package Commands
 * @author Christer Edvartsen <cogo@starzinger.net>
 * @copyright Copyright (c) 2011-2013, Christer Edvartsen <cogo@starzinger.net>
 * @license http://www.opensource.org/licenses/mit-license MIT License
 * @link https://github.com/imbo/imboclient-php-cli
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
