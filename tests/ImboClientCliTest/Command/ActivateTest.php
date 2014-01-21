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

use ImboClientCli\Command\Activate;

/**
 * @package Test suite
 * @author Christer Edvartsen <cogo@starzinger.net>
 * @covers ImboClientCli\Command\Activate
 */
class ActivateTest extends CommandTests {
    /**
     * @return Activate
     */
    protected function getCommand() {
        return new Activate();
    }

    public function testOutputsAnErrorWhenTryingToDeactivateAServerThatDoesNotExist() {
        $this->assertContains('There is no server named foobar in the configuration file.', $this->executeCommand(array(
            'server' => 'foobar',
        )));
    }

    public function testOutputsAnErrorWhenTryingToDeactivateAServerThatIsAlreadyActivated() {
        $this->assertContains('The server is already activated.', $this->executeCommand(array(
            'server' => 'local',
        )));
    }
}
