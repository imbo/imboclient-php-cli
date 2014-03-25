<?php
/**
 * This file is part of the ImboClientCli package
 *
 * (c) Christer Edvartsen <cogo@starzinger.net>
 *
 * For the full copyright and license information, please view the LICENSE file that was
 * distributed with this source code.
 */

namespace ImboClientCli\Command;

use ImboClient\ImboClient,
    Symfony\Component\Console\Input\InputInterface,
    Symfony\Component\Console\Output\OutputInterface,
    Symfony\Component\Console\Input\InputArgument,
    Guzzle\Common\Exception\GuzzleException;

/**
 * Command used to delete images from an imbo server
 *
 * @package Commands
 * @author Christer Edvartsen <cogo@starzinger.net>
 */
class DeleteImage extends RemoteCommand {
    /**
     * Class constructor
     */
    public function __construct() {
        parent::__construct('delete-image');

        $this->setAliases(array('delete'));

        $this->setDescription('Delete an image from imbo');
        $this->setHelp('Delete an image from one of the imbo servers defined in the configuration');
        $this->addArgument('imageIdentifier', InputArgument::REQUIRED, 'The identifier of the image to delete');
    }

    /**
     * Execute the command
     *
     * @see Symfony\Components\Console\Command\Command::execute()
     */
    protected function execute(InputInterface $input, OutputInterface $output) {
        $imageIdentifier = $input->getArgument('imageIdentifier');

        $dialog = $this->getHelper('dialog');
        $result = $dialog->askConfirmation($output, 'Are you sure you want to delete an image from <info>' . $this->server['url'] . '</info>? [yN] ', false);

        if ($result) {
            try {
                $response = $this->getClient()->deleteImage($imageIdentifier);
            } catch (GuzzleException $e) {
                $output->writeln('<error>An error occured. Could not delete the image: ' . $e->getMessage() . '</error>');
                return 1;
            }

            if ($response['status'] === 200) {
                $output->writeln('The image has been deleted from <info>' . $this->server['url'] . '</info>.');
            } else {
                $output->writeln('The image was not deleted. The response code from the server was: <info>' . $response['status'] . '</info>.');
                return 1;
            }
        } else {
            $output->writeln('Command aborted');
        }
    }
}
