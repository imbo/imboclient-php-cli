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


use ImboClient\ImboClient;

/**
 * @package Test suite
 * @author Christer Edvartsen <cogo@starzinger.net>
 * @covers ImboClientCli\Command\RemoteCommand
 */
abstract class RemoteCommandTests extends CommandTests {
    /**
     * @var ImboClient\ImboClient
     */
    protected $client;

    /**
     * Set up the client
     */
    public function setUp() {
        parent::setUp();

        $this->client = $this->getMockBuilder('ImboClient\ImboClient')
                             ->disableOriginalConstructor()
                             ->getMock();

        $this->command->setClient($this->client);
    }

    /**
     * Tear down the client
     */
    public function tearDown() {
        parent::tearDown();

        $this->client = null;
    }
}
