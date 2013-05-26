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
    Symfony\Component\Console\Input\InputArgument,
    Symfony\Component\Yaml\Dumper,
    InvalidArgumentException;

/**
 * Command used to activate a server on the config file
 *
 * @package Commands
 * @author Christer Edvartsen <cogo@starzinger.net>
 * @copyright Copyright (c) 2011-2013, Christer Edvartsen <cogo@starzinger.net>
 * @license http://www.opensource.org/licenses/mit-license MIT License
 * @link https://github.com/imbo/imboclient-php-cli
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
            throw new InvalidArgumentException('There is no server named ' . $server . ' in the configuration file.');
        }

        if ($this->configuration['servers'][$server]['active']) {
            throw new InvalidArgumentException('The server is already activated.');
        }

        // Activate the server
        $this->configuration['servers'][$server]['active'] = true;

        $dumper = new Dumper();
        $yaml = $dumper->dump($this->configuration, 2);

        if (!file_put_contents($this->configPath, $yaml)) {
            $output->writeln('An error occured. The configuration file was not updated.');
        } else {
            $output->writeln('The configuration file has been updated.');
        }
    }
}
