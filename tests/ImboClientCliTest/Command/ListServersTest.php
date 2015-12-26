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

use ImboClientCli\Command\ListServers,
    ImboClientCli\Application,
    Symfony\Component\Console\Tester\CommandTester;

/**
 * @package Test suite
 * @author Christer Edvartsen <cogo@starzinger.net>
 * @covers ImboClientCli\Command\ListServers
 */
class ListServersTest extends CommandTests {
    /**
     * @return ListServers
     */
    protected function getCommand() {
        return new ListServers();
    }

    public function testCanListServersInTheConfigurationFile() {
        $expected = <<<SERVERS
Name: local (default)
URL: http://imbo
Active: yes
User: user
Public key: user

Name: remote
URL: http://imbo-remote
Active: no
User: remoteuser
Public key: remotepub
SERVERS;

        $this->assertContains($expected, $this->executeCommand());
    }

    public function testCanIncludeThePrivateKeyOfEachUserInTheOutput() {
        $expected = <<<SERVERS
Name: local (default)
URL: http://imbo
Active: yes
User: user
Public key: user
Private key: key

Name: remote
URL: http://imbo-remote
Active: no
User: remoteuser
Public key: remotepub
Private key: otherkey
SERVERS;

        $this->assertContains($expected, $this->executeCommand(array('--show-private-keys' => 1)));
    }
}
