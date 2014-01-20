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
 * @covers ImboClientCli\Command\NumImages
 */
class NumImagesTest extends RemoteCommandTests {
    /**
     * @return NumImages
     */
    protected function getCommand() {
        return new NumImages();
    }

    public function testCanFetchTheNumberOfImages() {
        $this->client->expects($this->at(0))->method('getNumImages')->will($this->returnValue(2));
        $this->client->expects($this->at(1))->method('getNumImages')->will($this->returnValue(1));

        $display = $this->executeCommand();
        $this->assertContains('user has 2 images', $display);

        $display = $this->executeCommand();
        $this->assertContains('user has 1 image', $display);
    }
}
