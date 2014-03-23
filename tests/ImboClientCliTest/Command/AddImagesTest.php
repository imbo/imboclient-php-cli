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

use ImboClientCli\Command\AddImages,
    Guzzle\Http\Exception\ServerErrorResponseException;

/**
 * @package Test suite
 * @author Christer Edvartsen <cogo@starzinger.net>
 * @covers ImboClientCli\Command\AddImages
 */
class AddImagesTest extends RemoteCommandTests {
    /**
     * @return AddImages
     */
    protected function getCommand() {
        return new AddImages();
    }

    /**
     * Set up the finder property
     */
    public function setUp() {
        parent::setUp();

    }

    /**
     * Tear down the finder
     */
    public function tearDown() {
        $this->finder = null;
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage The specified path does not exist: /foo/bar
     */
    public function testThrowsAnExceptionIfThePathDoesNotExist() {
        $this->executeCommand(array(
            'path' => '/foo/bar',
        ));
    }

    public function testCanAddImagesFromADirectoryInARecursiveManner() {
        $this->client->expects($this->exactly(12))->method('addImage')->with($this->matchesRegularExpression('/\.(gif|jpg|png)$/'));

        $this->executeCommand(array(
            'path' => __DIR__ . '/../../images',
        ), function($command) {
            $dialog = $this->getMock('Symfony\Component\Console\Helper\DialogHelper');
            $dialog->expects($this->once())
                   ->method('askConfirmation')
                   ->with(
                       $this->isInstanceOf('Symfony\Component\Console\Output\OutputInterface'),
                       '<question>You are about to add 12 images to "local". Continue? [yN]</question> '
                   )
                   ->will($this->returnValue(true));

            $command->getHelperSet()->set($dialog, 'dialog');
        });
    }

    public function testCanAddSingleImages() {
        $this->client->expects($this->once())->method('addImage')->with($this->stringEndsWith('images/image.png'));

        $this->executeCommand(array(
            'path' => __DIR__ . '/../../images/image.png',
        ), function($command) {
            $dialog = $this->getMock('Symfony\Component\Console\Helper\DialogHelper');
            $dialog->expects($this->once())
                   ->method('askConfirmation')
                   ->with(
                       $this->isInstanceOf('Symfony\Component\Console\Output\OutputInterface'),
                       '<question>You are about to add 1 image to "local". Continue? [yN]</question> '
                   )
                   ->will($this->returnValue(true));

            $command->getHelperSet()->set($dialog, 'dialog');
        });
    }

    public function testWillNotAddAnyImagesWhenQuestionGetsANegativeAnswer() {
        $this->client->expects($this->never())->method('addImage');

        $this->executeCommand(array(
            'path' => __DIR__ . '/../../images/image.png',
        ), function($command) {
            $dialog = $this->getMock('Symfony\Component\Console\Helper\DialogHelper');
            $dialog->expects($this->once())
                   ->method('askConfirmation')
                   ->with(
                       $this->isInstanceOf('Symfony\Component\Console\Output\OutputInterface'),
                       '<question>You are about to add 1 image to "local". Continue? [yN]</question> '
                   )
                   ->will($this->returnValue(false));

            $command->getHelperSet()->set($dialog, 'dialog');
        });
    }

    public function getDepthArgument() {
        return array(
            'infinite (default)' => array(-1, 12),
            'don\'t enter' => array(0, 3),
            'enter 1' => array(1, 6),
            'enter 2' => array(2, 9),
            'enter 3' => array(3, 12),
        );
    }

    /**
     * @dataProvider getDepthArgument
     */
    public function testCanControlDepthWhenAddingImagesRecursively($depth, $numImages) {
        $this->client->expects($this->exactly($numImages))->method('addImage')->with($this->matchesRegularExpression('/\.(gif|jpg|png)$/'));

        $this->executeCommand(array(
            'path' => __DIR__ . '/../../images',
            '--depth' => $depth,
        ), function($command) use ($numImages) {
            $dialog = $this->getMock('Symfony\Component\Console\Helper\DialogHelper');
            $dialog->expects($this->once())
                   ->method('askConfirmation')
                   ->with(
                       $this->isInstanceOf('Symfony\Component\Console\Output\OutputInterface'),
                       '<question>You are about to add ' . $numImages . ' image' . ($numImages !== 1 ? 's' : '') . ' to "local". Continue? [yN]</question> '
                   )
                   ->will($this->returnValue(true));

            $command->getHelperSet()->set($dialog, 'dialog');
        });
    }

    public function testCanAddOnlySpecificSuffixes() {
        $this->client->expects($this->exactly(8))->method('addImage')->with($this->matchesRegularExpression('/\.(jpg|png)$/'));

        $this->executeCommand(array(
            'path' => __DIR__ . '/../../images',
            '--suffixes' => 'jpg,PNG',
        ), function($command) {
            $dialog = $this->getMock('Symfony\Component\Console\Helper\DialogHelper');
            $dialog->expects($this->once())
                   ->method('askConfirmation')
                   ->with(
                       $this->isInstanceOf('Symfony\Component\Console\Output\OutputInterface'),
                       '<question>You are about to add 8 images to "local". Continue? [yN]</question> '
                   )
                   ->will($this->returnValue(true));

            $command->getHelperSet()->set($dialog, 'dialog');
        });
    }

    public function testDisplaysInformationWhenNotAllImagesAreAdded() {
        $this->client->expects($this->exactly(3))->method('addImage')->will($this->returnCallback(function($path) {
            if (basename($path) === 'image.gif') {
                throw new ServerErrorResponseException('Could not add image.gif');
            }

            return true;
        }));

        $output = $this->executeCommand(array(
            'path' => __DIR__ . '/../../images',
            '--depth' => 0,
        ), function($command) {
            $dialog = $this->getMock('Symfony\Component\Console\Helper\DialogHelper');
            $dialog->expects($this->once())
                   ->method('askConfirmation')
                   ->with(
                       $this->isInstanceOf('Symfony\Component\Console\Output\OutputInterface'),
                       '<question>You are about to add 3 images to "local". Continue? [yN]</question> '
                   )
                   ->will($this->returnValue(true));

            $command->getHelperSet()->set($dialog, 'dialog');
        });

        $this->assertContains('2 images added to "local".', $output);
        $this->assertContains('1 image was not added:', $output);
        $this->assertContains(__DIR__ . '/../../images/image.gif (Could not add image.gif)', $output);
    }
}
