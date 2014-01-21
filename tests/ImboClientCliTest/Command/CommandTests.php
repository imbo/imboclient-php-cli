<?php
/**
 * This file is part of the ImboClientCli package
 *
 * (c) Christer Edvartsen <cogo@starzinger.net>
 *
 * For the full copyright and license information, please view the LICENSE file that was
 * distributed with this source code.
 */

namespace ImboClientCliTest\Command;


use ImboClientCli\Command\Command,
    ImboClientCli\Application,
    ImboClient\ImboClient,
    Symfony\Component\Console\Tester\CommandTester;

/**
 * @package Test suite
 * @author Christer Edvartsen <cogo@starzinger.net>
 * @covers ImboClientCli\Command\RemoteCommand
 */
abstract class CommandTests extends \PHPUnit_Framework_TestCase {
    /**
     * @var Command
     */
    protected $command;

    /**
     * Get the command to test
     *
     * @return Command
     */
    abstract protected function getCommand();

    /**
     * Execute the current command
     *
     * @param array $options Options for the command
     * @param callback $initializer Initializer that can be used to inject mocks for instance
     * @return string Returns the output from the command
     */
    protected function executeCommand(array $options = array(), $initializer = null) {
        $application = new Application();
        $application->add($this->command);

        $command = $application->find($this->command->getName());

        if ($initializer) {
            $initializer($command);
        }

        $tester = new CommandTester($command);
        $tester->execute(array_merge(
            // Default configuration file
            array('--config' => __DIR__ . '/../../config-files/config.yml'),

            // Custom options
            $options,

            // Command name is not overridable
            array('command' => $command->getName())
        ));

        return $tester->getDisplay();
    }

    /**
     * Set up the command
     */
    public function setUp() {
        $this->command = $this->getCommand();
    }

    /**
     * Tear down the command
     */
    public function tearDown() {
        $this->command = null;
    }
}
