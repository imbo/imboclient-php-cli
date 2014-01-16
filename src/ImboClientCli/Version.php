<?php
/**
 * This file is part of the Imbo package
 *
 * (c) Christer Edvartsen <cogo@starzinger.net>
 *
 * For the full copyright and license information, please view the LICENSE file that was
 * distributed with this source code.
 */

namespace ImboClientCli;

/**
 * Version class
 *
 * @package Application
 * @author Christer Edvartsen <cogo@starzinger.net>
 */
class Version {
    /**
     * The current version
     *
     * This string will be replaced by the actual version when installed via pear
     *
     * @var string
     */
    static private $id = '@package_version@';

    /**
     * Get the version number only
     *
     * @return string
     */
    static public function getVersionNumber() {
        if (strpos(self::$id, '@package_version') === 0) {
            return 'dev';
        }

        return self::$id;
    }

    /**
     * Get the version string
     *
     * @return string
     */
    static public function getVersionString() {
        return 'ImboClientCli-' . self::getVersionNumber() . ' by Christer Edvartsen';
    }
}
