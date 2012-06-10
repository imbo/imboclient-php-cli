<?php
/**
 * ImboClientCli
 *
 * Copyright (c) 2011-2012, Christer Edvartsen <cogo@starzinger.net>
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
 * @package Core
 * @author Christer Edvartsen <cogo@starzinger.net>
 * @copyright Copyright (c) 2011-2012, Christer Edvartsen <cogo@starzinger.net>
 * @license http://www.opensource.org/licenses/mit-license MIT License
 * @link https://github.com/imbo/imboclient-php-cli
 */

namespace ImboClientCli;

use ImboClientCli\Command,
    Symfony\Component\Console;

/**
 * Main application class
 *
 * @package Core
 * @author Christer Edvartsen <cogo@starzinger.net>
 * @copyright Copyright (c) 2011-2012, Christer Edvartsen <cogo@starzinger.net>
 * @license http://www.opensource.org/licenses/mit-license MIT License
 * @link https://github.com/imbo/imboclient-php-cli
 */
class Application extends Console\Application {
    /**
     * Class constructor
     *
     * Register all commands and set up some global options
     */
    public function __construct() {
        parent::__construct('ImboClientCli', Version::getVersionNumber());

        // Register commands
        $this->addCommands(array(
            new Command\Activate(),
            new Command\AddImage(),
            new Command\Deactivate(),
            new Command\DeleteImage(),
            new Command\ListImboServers(),
            new Command\NumImages(),
            new Command\ServerStatus(),
        ));

        // Add global options
        $this->getDefinition()->addOption(
            new Console\Input\InputOption(
                'config',
                null,
                Console\Input\InputOption::VALUE_OPTIONAL,
                'Path to configuration file'
            )
        );
    }
}
