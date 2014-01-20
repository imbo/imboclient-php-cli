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


use ImboClientCli\Command\ServerStatus,
    ImboClientCli\Application,
    Symfony\Component\Console\Tester\CommandTester;

/**
 * @package Test suite
 * @author Christer Edvartsen <cogo@starzinger.net>
 * @covers ImboClientCli\Command\ServerStatus
 */
class ServerStatusTest extends \PHPUnit_Framework_TestCase {
    /**
     * @var ServerStatus
     */
    private $command;

    /**
     * @var ImboClient\ImboClient
     */
    private $client;

    /**
     * Set up the command
     */
    public function setUp() {
        $this->client = $this->getMockBuilder('ImboClient\ImboClient')
                             ->disableOriginalConstructor()
                             ->getMock();

        $this->command = new ServerStatus();
        $this->command->setClient($this->client);
    }

    /**
     * Tear down the command
     */
    public function tearDown() {
        $this->command = null;
        $this->client = null;
    }

    /**
     * Data provider
     *
     * @return array[]
     */
    public function getStatuses() {
        return array(
            'ok' => array(true, true, array(
                'Database: ok',
                'Storage: ok',
            )),
            'database down' => array(false, true, array(
                'Database: error',
                'Storage: ok',
            )),
            'storage down' => array(true, false, array(
                'Database: ok',
                'Storage: error',
            )),
            'both down' => array(false, false, array(
                'Database: error',
                'Storage: error',
            )),
        );
    }

    /**
     * @dataProvider getStatuses
     */
    public function testCanFetchStatus($databaseStatus, $storageStatus, array $contains) {
        $application = new Application();
        $application->add($this->command);

        $datetime = $this->getMock('DateTime');
        $datetime->expects($this->once())->method('format')->with('r')->will($this->returnValue('formatted date'));

        $this->client->expects($this->once())->method('getServerStatus')->will($this->returnValue(array(
            'date' => $datetime,
            'database' => $databaseStatus,
            'storage' => $storageStatus,
        )));

        $command = $application->find('server-status');

        $tester = new CommandTester($command);
        $tester->execute(array(
            'command' => $command->getName(),
            '--config' => __DIR__ . '/../../test-config.yml',
        ));

        $display = $tester->getDisplay();

        foreach ($contains as $line) {
            $this->assertContains($line, $display);
        }
    }
}
