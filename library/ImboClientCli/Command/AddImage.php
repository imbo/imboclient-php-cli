<?php
/**
 * This file is part of the Imbo package
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
    Symfony\Component\Finder\Finder,
    RuntimeException,
    InvalidArgumentException;

/**
 * Command used to add images to an imbo server
 *
 * @package Commands
 * @author Christer Edvartsen <cogo@starzinger.net>
 */
class AddImage extends RemoteCommand {
    /**
     * Class constructor
     */
    public function __construct() {
        parent::__construct('add-image');

        $this->setDescription('Add an image to imbo');
        $this->setHelp('Add an image to one of the imbo servers defined in the configuration');
        $this->addArgument('path', InputArgument::REQUIRED, 'Path to the image or a directory containing images');
        $this->addOption('suffixes', null, InputOption::VALUE_OPTIONAL, 'Comma separated list of suffixes to include when specifying a directory as a path (case insensitive)', 'jpg,png,gif');
        $this->addOption('depth', null, InputOption::VALUE_OPTIONAL, 'Directory depth. -1 = enter all subdirectories, 0 = don\'t enter subdirectories, N = enter N subdirectories', -1);
    }

    /**
     * Execute the command
     *
     * @see Symfony\Components\Console\Command\Command::execute()
     */
    protected function execute(InputInterface $input, OutputInterface $output) {
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
        $result = $dialog->askConfirmation($output, 'You are about to add ' . count($files) . ' images to "' . $this->server['name'] . '". Continue? [yN] ', false);

        if ($result) {
            $client = new ImboClient($this->server['url'], $this->server['publicKey'], $this->server['privateKey']);
            $addedImages = 0;
            $notAdded = array();

            foreach ($files as $file) {
                try {
                    $response = $client->addImage($file);

                    if ($response->isSuccess()) {
                        $output->writeln($file . ': ' . $response->getImageIdentifier());
                        $addedImages++;
                    } else {
                        $body = $response->asArray();

                        $notAdded[] = $file . ' (' . $body['error']['message'] . ')';
                    }
                } catch (RuntimeException $e) {
                    $notAdded[] = $file . ' (' . $e->getMessage() . ')';
                }
            }

            $output->writeln($addedImages . ' images added to "' . $this->server['name']. '".');

            if ($num = count($notAdded)) {
                $output->writeln($num . ' images was not added:');
                $output->write($notAdded, true);
            }
        } else {
            $output->writeln('Command aborted');
        }
    }
}
