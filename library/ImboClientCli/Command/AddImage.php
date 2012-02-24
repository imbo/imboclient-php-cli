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
    Symfony\Component\Finder\Finder,
    RuntimeException,
    InvalidArgumentException;

/**
 * Command used to add images to an imbo server
 *
 * @package Commands
 * @author Christer Edvartsen <cogo@starzinger.net>
 * @copyright Copyright (c) 2011-2012, Christer Edvartsen <cogo@starzinger.net>
 * @license http://www.opensource.org/licenses/mit-license MIT License
 * @link https://github.com/christeredvartsen/imboclient-php-cli
 */
class AddImage extends Command {
    /**
     * Class constructor
     */
    public function __construct() {
        parent::__construct('add-image');

        $this->setDescription('Add an image to imbo');
        $this->setHelp('Add an image to one of the imbo servers defined in the configuration');
        $this->addArgument('path', InputArgument::REQUIRED, 'Path to the image or a directory containing images');
        $this->addOption('server', null, InputOption::VALUE_OPTIONAL, 'Which configured imbo server to add the image to. If not specified the default server will be used');
        $this->addOption('suffixes', null, InputOption::VALUE_OPTIONAL, 'Comma separated list of suffixes to include when specifying a directory as a path (case insensitive)', 'jpg,png,gif');
        $this->addOption('depth', null, InputOption::VALUE_OPTIONAL, 'Directory depth. -1 = enter all subdirectories, 0 = don\'t enter subdirectories, N = enter N subdirectories', -1);
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

        $path = $input->getArgument('path');
        $fullPath = realpath($path);

        if (!$fullPath) {
            throw new InvalidArgumentException('The specified path does not exist: ' . $path);
        }

        if (is_file($fullPath)) {
            $files = array($fullPath);
        } else {
            $suffixes = $input->getOption('suffixes');
            $pattern = '/\.(' . str_replace(',', '|', $suffixes) . ')$/i';

            $finder = new Finder();
            $finder->files()->in($fullPath)->name($pattern);

            $depth = (int) $input->getOption('depth');

            if ($depth > -1) {
                if ($depth == 0) {
                    $depth = '== 0';
                } else {
                    $depth = '< ' . $depth;
                }

                $finder->depth($depth);
            }

            foreach ($finder as $file) {
                $files[] = (string) $file;
            }
        }

        $dialog = $this->getHelper('dialog');
        $result = $dialog->askConfirmation($output, 'You are about to add ' . count($files) . ' images to "' . $server . '". Continue? [yN] ', false);

        if ($result) {
            $client = new ImboClient($imbo['url'], $imbo['publicKey'], $imbo['privateKey']);
            $addedImages = 0;

            foreach ($files as $file) {
                try {
                    $response = $client->addImage($file);

                    if ($response->isSuccess()) {
                        $addedImages++;
                    }
                } catch (RuntimeException $e) {
                    // Just continue
                }
            }

            $output->writeln($addedImages . ' images added to "' . $server. '".');
        } else {
            $output->writeln('Command aborted');
        }
    }
}
