# ImboClientCli - A command line client for Imbo
This is a command line wrapper around the official [ImboClient](https://github.com/imbo/imboclient-php) client.

## Installation
Install using [Composer](http://getcomposer.org):

```
curl -s https://getcomposer.org/installer | php
php composer.phar create-project imbo/imboclient [<dir>] [<version>]
```

### Requirements
ImboClientCli requires [PHP-5.3.2](http://php.net/) or above.

## Configuration file
ImboClientCli requires a configuration file and will look for `config.yml` in the following directories (in the order specified), and will use the first it finds:

* Current working directory
* `~/.imboclient/config.yml`
* `/etc/imboclient/config.yml`

You may also specify a custom configuration file by using the `--config /path/to/config.yml` global option. By doing this the client will not look in any of the above directories (unless the file you specify does not exist).

An [example configuration file](https://github.com/imbo/imboclient-php-cli/blob/master/config.yml.dist) is included in the package.

## Usage
Simply run the executable (`imboclient.php`) and you will see something like this:

![Screenshot](https://github.com/imbo/imboclient-php-cli/raw/master/screenshots/imboclientcli.png "Command executed without any options")

More docs will be added later.
