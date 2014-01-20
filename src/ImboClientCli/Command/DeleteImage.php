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

use ImboClient\Client as ImboClient,
    Symfony\Component\Console\Input\InputInterface,
    Symfony\Component\Console\Output\OutputInterface,
    Symfony\Component\Console\Input\InputArgument,
    Symfony\Component\Console\Input\InputOption,
    RuntimeException,
    InvalidArgumentException;

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
        $result = $dialog->askConfirmation($output, 'Are you sure you want to delete an image from the "' . $this->server['name'] . '" server? [yN] ', false);

        if ($result) {
            $client = new ImboClient($this->server['url'], $this->server['publicKey'], $this->server['privateKey']);

            try {
                $response = $client->deleteImage($imageIdentifier);
            } catch (RuntimeException $e) {
                $output->writeln('An error occured. Could not complete the action.');
                return;
            }

            if ($response->isSuccess()) {
                $output->writeln('The image has been deleted from "' . $this->server['name'] . '".');
            } else {
                $output->writeln('The image was not removed. The response code from the server was: ' . $response->getStatusCode());
            }
        } else {
            $output->writeln('Command aborted');
        }
    }
}
