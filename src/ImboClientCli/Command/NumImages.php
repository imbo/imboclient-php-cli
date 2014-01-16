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
    RuntimeException;

/**
 * Command used to see if an image exist on an imbo server
 *
 * @package Commands
 * @author Christer Edvartsen <cogo@starzinger.net>
 */
class NumImages extends RemoteCommand {
    /**
     * Class constructor
     */
    public function __construct() {
        parent::__construct('num-images');

        $this->setDescription('Get the number of images on an imbo server');
        $this->setHelp('Get the number of images on an imbo server');
    }

    /**
     * Execute the command
     *
     * @see Symfony\Components\Console\Command\Command::execute()
     */
    protected function execute(InputInterface $input, OutputInterface $output) {
        $client = new ImboClient($this->server['url'], $this->server['publicKey'], $this->server['privateKey']);

        try {
            $num = $client->getNumImages();
        } catch (RuntimeException $e) {
            $output->writeln('An error occured. Could not complete the action.');
            return;
        }

        if ($num === false) {
            $output->writeln($this->server['name'] . ' did not respond correctly.');
        } else {
            $output->writeln($this->server['name'] . ' has ' . $num . ' images.');
        }
    }
}
