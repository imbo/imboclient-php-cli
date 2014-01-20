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

use ImboClientCli\Command\ServerStatus;

/**
 * @package Test suite
 * @author Christer Edvartsen <cogo@starzinger.net>
 * @covers ImboClientCli\Command\ServerStatus
 */
class ServerStatusTest extends RemoteCommandTests {
    /**
     * @return ServerStatus
     */
    protected function getCommand() {
        return new ServerStatus();
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
        $datetime = $this->getMock('DateTime');
        $datetime->expects($this->once())->method('format')->with('r')->will($this->returnValue('formatted date'));

        $this->client->expects($this->once())->method('getServerStatus')->will($this->returnValue(array(
            'date' => $datetime,
            'database' => $databaseStatus,
            'storage' => $storageStatus,
        )));

        $display = $this->executeCommand();

        foreach ($contains as $line) {
            $this->assertContains($line, $display);
        }
    }
}
