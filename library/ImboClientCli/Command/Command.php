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
 * @package Commands
 * @author Christer Edvartsen <cogo@starzinger.net>
 * @copyright Copyright (c) 2011-2012, Christer Edvartsen <cogo@starzinger.net>
 * @license http://www.opensource.org/licenses/mit-license MIT License
 * @link https://github.com/christeredvartsen/imboclient-php-cli
 */

namespace ImboClientCli\Command;

use ImboClientCli\Version,
    Symfony\Component\Console\Command\Command as BaseCommand,
    Symfony\Component\Console\Input\InputInterface,
    Symfony\Component\Console\Output\OutputInterface,
    Symfony\Component\Yaml\Parser,
    Symfony\Component\Yaml\Exception\ParseException,
    InvalidArgumentException,
    RuntimeException;

/**
 * Base command for other ImboClientCli commands
 *
 * @package Commands
 * @author Christer Edvartsen <cogo@starzinger.net>
 * @copyright Copyright (c) 2011-2012, Christer Edvartsen <cogo@starzinger.net>
 * @license http://www.opensource.org/licenses/mit-license MIT License
 * @link https://github.com/christeredvartsen/imboclient-php-cli
 */
abstract class Command extends BaseCommand {
    /**
     * Application configuration
     */
    protected $configuration;

    /**
     * Set the configuration
     *
     * @param array $configuration
     */
    protected function setConfiguration(array $configuration) {
        $this->configuration = $configuration;
    }

    /**
     * Get the configuration
     *
     * @return array
     */
    protected function getConfiguration() {
        return $this->configuration;
    }

    /**
     * Initialization method
     *
     * This method is triggered before any commands will be executed. It will try to load a
     * configuration file and store its contents for commands using the configuration.
     *
     * @see Symfony\Components\Console\Command\Command::initialize()
     */
    protected function initialize(InputInterface $input, OutputInterface $output) {
        $output->writeln(Version::getVersionString());

        // Paths to look for the config
        $configPaths = $paths = array(
            $_SERVER['HOME'] . '/.imboclient/config.yml',
            '/etc/imboclient/config.yml',
        );

        $config = $input->getOption('config');

        if ($config !== null) {
            $config = realpath($config);
        }

        if ($config && is_file($config)) {
            // Prepend the config option
            array_unshift($paths, $config);
        }

        $fullPath = null;

        // Loop through the paths and use the first viable option
        foreach ($paths as $path) {
            if (is_file($path)) {
                $fullPath = $path;
                break;
            }
        }

        if (empty($fullPath)) {
            throw new RuntimeException(
                'No available configuration. Please place a valid configuration file in one of the following directories: ' . PHP_EOL .
                PHP_EOL .
                implode(PHP_EOL, $configPaths) . PHP_EOL .
                PHP_EOL .
                'or specify the path to a file using the --config option.'
            );
        }

        $parser = new Parser();

        try {
            $this->configuration = $parser->parse(file_get_contents($fullPath));
        } catch (ParseException $e) {
            throw new InvalidArgumentException(
                'Invalid configuration file: ' . $fullPath . ' (Parser message: ' . $e->getMessage() . ')'
            );
        }

        $output->writeln('Configuration read from ' . $fullPath);
    }
}
