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
 */
abstract class Command extends BaseCommand {
    /**
     * Application configuration
     *
     * @var array
     */
    protected $configuration;

    /**
     * Path to the active configuration
     *
     * @var string
     */
    protected $configPath;

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
        // Paths to look for the config
        $configPaths = $paths = array(
            getcwd() . '/config.yml',
            $_SERVER['HOME'] . '/.imboclient/config.yml',
            '/etc/imboclient/config.yml',
        );

        $config = $input->getOption('config');

        if ($config !== null) {
            $config = realpath($config);

            if ($config && is_file($config)) {
                // Prepend the config option
                array_unshift($paths, $config);
            } else {
                throw new RuntimeException(
                    '"' . $input->getOption('config') . '" is not a valid configuration file.'
                );
            }
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

        $this->configPath = $fullPath;

        $parser = new Parser();

        try {
            $this->configuration = $parser->parse(file_get_contents($this->configPath));
        } catch (ParseException $e) {
            throw new InvalidArgumentException(
                'Invalid configuration file: ' . $this->configPath . ' (Parser message: ' . $e->getMessage() . ')'
            );
        }

        $output->write(array(
            'Configuration read from <info>' . $this->configPath . '</info>',
            '',
        ), true);
    }
}
