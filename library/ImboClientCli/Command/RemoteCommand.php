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
 * @copyright Copyright (c) 2011-2013, Christer Edvartsen <cogo@starzinger.net>
 * @license http://www.opensource.org/licenses/mit-license MIT License
 * @link https://github.com/imbo/imboclient-php-cli
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
