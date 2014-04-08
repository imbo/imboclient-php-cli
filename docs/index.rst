ImboClient - CLI
================

This is a command line wrapper around the official PHP-based client for `Imbo <https://github.com/imbo/imbo>`_ servers.

Requirements
------------

The client requires `PHP >= 5.4 <http://php.net/>`_.

Installation
------------

ImboClientCli can be installed using `Composer <http://getcomposer.org/>`_ by requiring ``imbo/imboclient-cli`` in your ``composer.json`` file, or by running the following commands::

    curl -s https://getcomposer.org/installer | php
    php composer.phar create-project imbo/imboclient-cli [<dir>] [<version>]

Available versions can be located at `packagist <https://packagist.org/packages/imbo/imboclient-cli>`_.

Usage
-----

Once the client is installed you will find an executable called ``imboclient`` in the ``bin`` directory where you installed the client.

Below you will find documentation covering most features of the client.

.. contents::
    :local:

.. _configuration-file:

Configuration file
++++++++++++++++++

To be able to use the client you will need to create a `YAML <http://www.yaml.org/>`_ configuration file that contains server definitions and key pairs. The client will look for the configuration file in the following paths (in the order specified):

* ``./config.yml``
* ``~/.imboclient/config.yml``
* ``/etc/imboclient/config.yml``

The client will use the first file it finds. A custom path can be specified by using the ``--config <path>`` command line option.

The client ships with an example configuration file (shown below) that you can use as a base for your own configuration.

.. literalinclude:: ../config.yml.dist
    :language: yaml
    :linenos:

The ``default`` key should point to the server definition that is to be used per default. If you need to execute a remote command (for instance adding / deleting images) from another server, specify the ``--server <server>`` option when executing the command. When a server definition is labeled as not active (``active: false``) you can not execute remote commands for that server. These flags can be changed manually by editing the configuration file, or by using the `activate <activate-command>` or `deactivate <deactivate-command>` commands in the client.

Commands
++++++++

Below you will find all the different commands supported by the client, along with a detailed description of all options and arguments.

.. contents::
    :local:

.. _activate-command:

Activate a server - ``activate``
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

.. _add-command:

Add one or more images - ``add`` (``add-images``)
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

.. _deactivate-command:

Deactivate a server - ``deactivate``
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

.. _delete-command:

Delete an image - ``delete`` (``delete-image``)
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

.. _help-command:

Get help - ``help``
~~~~~~~~~~~~~~~~~~~

.. _list-command:

List available commands - ``list``
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

.. _list-servers-command:

List servers in the configuration file - ``list-servers``
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

.. _num-images-command:

Get the number of images - ``num-images``
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

.. _server-status-command:

Get the server status - ``server-status``
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
