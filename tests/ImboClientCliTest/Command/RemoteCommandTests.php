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


use ImboClientCli\Command\NumImages,
    ImboClientCli\Application,
    ImboClient\ImboClient,
    Symfony\Component\Console\Tester\CommandTester;

/**
 * @package Test suite
 * @author Christer Edvartsen <cogo@starzinger.net>
 * @covers ImboClientCli\Command\RemoteCommand
 */
abstract class RemoteCommandTests extends \PHPUnit_Framework_TestCase {
    /**
     * @var RemoteCommand
     */
    protected $command;

    /**
     * @var ImboClient\ImboClient
     */
    protected $client;

    /**
     * Get the command to test
     *
     * @return RemoteCommand
     */
    abstract protected function getCommand();

    /**
     * Execute the current command
     *
     * @param array $options Options for the command
     * @return string Returns the output from the command
     */
    protected function executeCommand(array $options = array()) {
        $application = new Application();
        $application->add($this->command);

        $command = $application->find($this->command->getName());

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
        $this->client = $this->getMockBuilder('ImboClient\ImboClient')
                             ->disableOriginalConstructor()
                             ->getMock();

        $this->command = $this->getCommand();
        $this->command->setClient($this->client);
    }

    /**
     * Tear down the command
     */
    public function tearDown() {
        $this->command = null;
        $this->client = null;
    }
}
