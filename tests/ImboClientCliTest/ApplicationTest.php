<?php
/**
 * This file is part of the ImboClientCli package
 *
 * (c) Christer Edvartsen <cogo@starzinger.net>
 *
 * For the full copyright and license information, please view the LICENSE file that was
 * distributed with this source code.
 */

namespace ImboClientCliTest;


use ImboClientCli\Application,
    ImboClientCli\Version,
    Symfony\Component\Console\Tester\ApplicationTester;

/**
 * @package Test suite
 * @author Christer Edvartsen <cogo@starzinger.net>
 * @covers ImboClientCli\Application
 */
class ApplicationTest extends \PHPUnit_Framework_TestCase {
    /**
     * @var ApplicationTester
     */
    private $application;

    /**
     * Set up the application
     */
    public function setUp() {
        $imboClientCli = new Application();
        $imboClientCli->setAutoExit(false);

        $this->application = new ApplicationTester($imboClientCli);
    }

    /**
     * Tear down the application
     */
    public function tearDown() {
        $this->application = null;
    }

    public function testDisplaysCorrectVersion() {
        $this->application->run(array());
        $display = $this->application->getDisplay();
        $this->assertContains('ImboClientCli version ' . Version::VERSION, $display);
    }

    public function testListsTheAddedCommands() {
        $this->application->run(array());
        $display = $this->application->getDisplay();

        foreach (array(
            'activate        Activate an imbo server',
            'add-images      Add one or more images to an Imbo server',
            'deactivate      Deactivate an imbo server',
            'delete-image    Delete an image from imbo',
            'list-servers    List configured servers',
            'num-images      Get the number of images on an imbo server',
            'server-status   Check server status',
        ) as $command) {
            $this->assertContains($command, $display);
        }
    }
}
