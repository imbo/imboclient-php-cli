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
 * @link https://github.com/christeredvartsen/imboclient-php-cli
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
 * @link https://github.com/christeredvartsen/imboclient-php-cli
 */
class DeleteImage extends Command {
    /**
     * Class constructor
     */
    public function __construct() {
        parent::__construct('delete-image');

        $this->setDescription('Delete an image from imbo');
        $this->setHelp('Delete an image from one of the imbo servers defined in the configuration');
        $this->addArgument('imageIdentifier', InputArgument::REQUIRED, 'The identifier of the image to delete');
        $this->addOption('server', null, InputOption::VALUE_OPTIONAL, 'Which configured imbo server to add the image to. If not specified the default server will be used');
    }

    /**
     * Execute the command
     *
     * @see Symfony\Components\Console\Command\Command::execute()
     */
    protected function execute(InputInterface $input, OutputInterface $output) {
        $servers = $this->configuration['servers'];
        $default = isset($servers['default'])
                 ? $servers['default']
                 : '';

        $server = $input->getOption('server');

        if (!$server) {
            $server = $default;
        }

        if (!$default) {
            throw new RuntimeException('No default server is configured. Please set up a default server or specify which one to use with --server.');
        }

        if (empty($servers[$server]) || $servers[$server]['active'] !== 'yes') {
            throw new InvalidArgumentException('No active server called "' . $server . '" exists in the configuration.');
        }

        $imbo = $servers[$server];

        $imageIdentifier = $input->getArgument('imageIdentifier');

        $dialog = $this->getHelper('dialog');
        $result = $dialog->askConfirmation($output, 'Are you sure you want to delete an image from the server? [yN] ', false);

        if ($result) {
            $client = new ImboClient($imbo['url'], $imbo['publicKey'], $imbo['privateKey']);

            try {
                $response = $client->deleteImage($imageIdentifier);
            } catch (RuntimeException $e) {
                $output->writeln('An error occured. Could not complete the action.');
                return;
            }

            if ($response->isSuccess()) {
                $output->writeln('The image has been deleted from "' . $server . '".');
            } else {
                $output->writeln('The image was not removed. The response code from the server was: ' . $response->getStatusCode());
            }
        } else {
            $output->writeln('Command aborted');
        }
    }
}
