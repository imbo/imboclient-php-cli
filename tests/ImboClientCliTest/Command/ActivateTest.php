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

use ImboClientCli\Command\Activate,
    org\bovigo\vfs\vfsStream;

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

    public function testOutputsAnErrorWhenTheConfigurationFileIsNotWritable() {
        $originalConfigFile = file_get_contents(__DIR__ . '/../../config-files/config.yml');
        $root = vfsStream::setup('config-file-dir');
        $configFile = vfsStream::newFile('config.yml', 0400);
        $configFile->setContent($originalConfigFile);
        $root->addChild($configFile);

        $output = $this->executeCommand(array(
            'server' => 'remote',
            '--config' => $configFile->url(),
        ));

        $this->assertContains('An error occured. The configuration file was not updated.', $output);
        $this->assertSame(file_get_contents($configFile->url()), $originalConfigFile, 'Config files differ. The virtual one should not have been changed');
    }

    public function testOutputsAMessageWhenTheConfigurationHasBeenUpdated() {
        $originalConfigFile = file_get_contents(__DIR__ . '/../../config-files/config.yml');
        $root = vfsStream::setup('config-file-dir');
        $configFile = vfsStream::newFile('config.yml', 0655);
        $configFile->setContent($originalConfigFile);
        $root->addChild($configFile);

        $output = $this->executeCommand(array(
            'server' => 'remote',
            '--config' => $configFile->url(),
        ));

        $this->assertContains('The configuration file has been updated.', $output);
        $this->assertContains('remote: { url: \'http://imbo-remote\', user: remoteuser, publicKey: remotepub, privateKey: otherkey, active: true }', file_get_contents($configFile->url()));
    }
}
