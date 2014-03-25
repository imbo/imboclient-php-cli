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
    Symfony\Component\Finder\Finder,
    Exception,
    InvalidArgumentException;

/**
 * Command used to add images to an imbo server
 *
 * @package Commands
 * @author Christer Edvartsen <cogo@starzinger.net>
 */
class AddImages extends RemoteCommand {
    /**
     * @var Finder
     */
    private $finder;

    /**
     * Class constructor
     */
    public function __construct() {
        parent::__construct('add-images');

        $this->setAliases(array('add'));

        $this->setDescription('Add one or more images to an Imbo server');
        $this->setHelp('Add one or more images to one of the Imbo servers defined in the configuration');

        $this->addArgument('path', InputArgument::REQUIRED, 'Path to an image or a directory containing images');
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

        if (!is_dir($path) && !is_file($path)) {
            throw new InvalidArgumentException('The specified path does not exist: ' . $path);
        }

        if (is_file($path)) {
            $files = array($path);
        } else {
            $suffixes = $input->getOption('suffixes');
            $pattern = '/\.(' . str_replace(',', '|', $suffixes) . ')$/i';

            $finder = $this->getFinder();
            $finder->files()->in($path)->name($pattern);

            $depth = (int) $input->getOption('depth');

            if ($depth > -1) {
                if ($depth == 0) {
                    $depth = '== 0';
                } else {
                    $depth = '<= ' . $depth;
                }

                $finder->depth($depth);
            }

            foreach ($finder as $file) {
                $files[] = (string) $file;
            }
        }

        $numImages = count($files);
        $question = sprintf(
            '<question>You are about to add %d image%s to "%s". Continue? [yN]</question> ',
            $numImages,
            ($numImages !== 1 ? 's' : ''),
            $this->server['name']
        );
        $dialog = $this->getHelper('dialog');

        if (!$dialog->askConfirmation($output, $question, false)) {
            return;
        }

        $addedImages = 0;
        $notAdded = array();
        $client = $this->getClient();

        foreach ($files as $i => $file) {
            try {
                $response = $client->addImage($file);
                $padding = strlen($numImages);
                $output->writeln(sprintf('(%' . $padding . 'd/%d) %s: %s', ($i + 1), $numImages, $file, $response['imageIdentifier']));
                $addedImages++;
            } catch (Exception $e) {
                $notAdded[] = $file . ' (' . $e->getMessage() . ')';
            }
        }

        $output->writeln(sprintf(
            '<info>%d images added to "%s".</info>',
            $addedImages,
            $this->server['name']
        ));

        if ($num = count($notAdded)) {
            $output->writeln(sprintf(
                '<error>%d image%s was not added:</error>',
                $num,
                ($num === 1 ? '' : 's')
            ));
            $output->write($notAdded, true);
        }
    }

    /**
     * Set a Finder instance
     *
     * @param Finder $finder
     */
    public function setFinder(Finder $finder) {
        $this->finder = $finder;
    }

    /**
     * Get a Finder instance
     *
     * @return Finder
     */
    public function getFinder() {
        if ($this->finder === null) {
            $this->finder = new Finder();
        }

        return $this->finder;
    }
}
