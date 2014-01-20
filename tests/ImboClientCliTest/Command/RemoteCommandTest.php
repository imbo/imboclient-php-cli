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

use ImboClientCli\Command\NumImages;

/**
 * @package Test suite
 * @author Christer Edvartsen <cogo@starzinger.net>
 * @covers ImboClientCli\Command\RemoteCommand
 */
class RemoteCommandTest extends RemoteCommandTests {
    /**
     * @return RemoteCommand
     */
    protected function getCommand() {
        return new NumImages();
    }

    public function testWillAlwaysAddTheUrlToTheRemoteServer() {
        $this->client->expects($this->once())->method('getNumImages')->will($this->returnValue(2));
        $this->assertContains('Remote command on http://imbo:', $this->executeCommand());
    }

    public function testWillCreateAnInstanceOfTheImboClientItself() {
        $this->assertInstanceOf('ImboClient\ImboClient', $this->getCommand()->getClient());
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage No default server is configured. Please set up a default server or specify which one to use with --server.
     */
    public function testWillThrowAnExceptionWhenNoDefaultServerExistsInTheConfiguration() {
        $this->executeCommand(array('--config' => __DIR__ . '/../../config-files/no-default-server.yml'));
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage No active server called "local" exists in the configuration.
     */
    public function testWillThrowAnExceptionWhenNoActiveServerExistsInTheConfiguration() {
        $this->executeCommand(array('--config' => __DIR__ . '/../../config-files/no-active-server.yml'));
    }
}
