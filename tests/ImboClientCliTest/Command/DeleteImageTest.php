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

use ImboClientCli\Command\DeleteImage,
    Guzzle\Common\Exception\RuntimeException as GuzzleRuntimeException;

/**
 * @package Test suite
 * @author Christer Edvartsen <cogo@starzinger.net>
 * @covers ImboClientCli\Command\DeleteImage
 */
class DeleteImageTest extends RemoteCommandTests {
    /**
     * @return DeleteImage
     */
    protected function getCommand() {
        return new DeleteImage();
    }

    public function testOutputsAnErrorMessageWhenTheClientThrowsAnException() {
        $this->client->expects($this->once())
                     ->method('deleteImage')
                     ->with('id')
                     ->will($this->throwException(new GuzzleRuntimeException('some message')));

        $output = $this->executeCommand(array(
            'imageIdentifier' => 'id',
        ), function($command) {
            $dialog = $this->getMock('Symfony\Component\Console\Helper\DialogHelper');
            $dialog->expects($this->once())
                   ->method('askConfirmation')
                   ->with(
                       $this->isInstanceOf('Symfony\Component\Console\Output\OutputInterface'),
                       'Are you sure you want to delete an image from <info>http://imbo</info>? [yN] '
                   )
                   ->will($this->returnValue(true));

            $command->getHelperSet()->set($dialog, 'dialog');
        });

        $this->assertContains('An error occured. Could not delete the image: some message', $output);
    }

    public function testAbortsTheDeleteOperationWhenTheUserDoesNotConfirm() {
        $output = $this->executeCommand(array(
            'imageIdentifier' => 'id',
        ), function($command) {
            $dialog = $this->getMock('Symfony\Component\Console\Helper\DialogHelper');
            $dialog->expects($this->once())
                   ->method('askConfirmation')
                   ->will($this->returnValue(false));

            $command->getHelperSet()->set($dialog, 'dialog');
        });

        $this->assertContains('Command aborted', $output);
    }

    public function testOutputsAnErrorMessageWhenTheUserConfirmsAndTheResponseCodeIsNot200Ok() {
        $this->client->expects($this->once())
                     ->method('deleteImage')
                     ->with('id')
                     ->will($this->returnValue(array('status' => 404)));

        $output = $this->executeCommand(array(
            'imageIdentifier' => 'id',
        ), function($command) {
            $dialog = $this->getMock('Symfony\Component\Console\Helper\DialogHelper');
            $dialog->expects($this->once())
                   ->method('askConfirmation')
                   ->will($this->returnValue(true));

            $command->getHelperSet()->set($dialog, 'dialog');
        });

        $this->assertContains('The image was not deleted. The response code from the server was: 404', $output);
    }

    public function testOutputsAnConfirmationWhenTheImageHasBeenDeleted() {
        $this->client->expects($this->once())
                     ->method('deleteImage')
                     ->with('id')
                     ->will($this->returnValue(array('status' => 200)));

        $output = $this->executeCommand(array(
            'imageIdentifier' => 'id',
        ), function($command) {
            $dialog = $this->getMock('Symfony\Component\Console\Helper\DialogHelper');
            $dialog->expects($this->once())
                   ->method('askConfirmation')
                   ->will($this->returnValue(true));

            $command->getHelperSet()->set($dialog, 'dialog');
        });

        $this->assertContains('The image has been deleted from http://imbo', $output);
    }
}
