<?php
/**
 * ImboClientCli
 *
 * Copyright (c) 2011-2012, Christer Edvartsen <cogo@starzinger.net>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to
 * deal in the Software without restriction, including without limitation the
 * rights to use, copy, modify, merge, publish, distribute, sublicense, and/or
 * sell copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * * The above copyright notice and this permission notice shall be included in
 *   all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS
 * IN THE SOFTWARE.
 *
 * @package Commands
 * @author Christer Edvartsen <cogo@starzinger.net>
 * @copyright Copyright (c) 2011-2012, Christer Edvartsen <cogo@starzinger.net>
 * @license http://www.opensource.org/licenses/mit-license MIT License
 * @link https://github.com/imbo/imboclient-php-cli
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
 * @copyright Copyright (c) 2011-2012, Christer Edvartsen <cogo@starzinger.net>
 * @license http://www.opensource.org/licenses/mit-license MIT License
 * @link https://github.com/imbo/imboclient-php-cli
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
