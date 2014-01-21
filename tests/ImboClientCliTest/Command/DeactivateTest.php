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

use ImboClientCli\Command\Deactivate;

/**
 * @package Test suite
 * @author Christer Edvartsen <cogo@starzinger.net>
 * @covers ImboClientCli\Command\Deactivate
 */
class DeactivateTest extends CommandTests {
    /**
     * @return Deactivate
     */
    protected function getCommand() {
        return new Deactivate();
    }

    public function testOutputsAnErrorWhenTryingToDeactivateAServerThatDoesNotExist() {
        $this->assertContains('There is no server named foobar in the configuration file.', $this->executeCommand(array(
            'server' => 'foobar',
        )));
    }

    public function testOutputsAnErrorWhenTryingToDeactivateAServerThatIsAlreadyDeactivated() {
        $this->assertContains('The server is already deactivated.', $this->executeCommand(array(
            'server' => 'remote',
        )));
    }
}
