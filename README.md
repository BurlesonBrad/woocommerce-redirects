# WooCommerce Redirects #

## Overview ##

This repo contains source code for the WooCommerce Redirects plugin. It also contains Docker files used for local
development and testing.

This plugin should not be considered production ready, which is why it has not yet been published on wordpress.org.

## Usage ##

To use the plugin in your own development or test environment, symlink or copy the `src` directory to
`wp-content/plugins`, using the name `woocommerce-redirects`.

The plugin itself is quite simple. It adds a tab called 'Redirects' to the WooCommerce Product Data metabox. On this
tab you will find a dropdown box that allows you to configure an add-to-cart redirect on a per-product basis.
 
## Docker ##

Using Docker Compose, you can set up a development environment for the WooCommerce Redirects plugin. With Docker and
Docker Compose already installed, this should be as simple as:

    docker-compose up
    
This will launch a web server and install WordPress, making it available at http://localhost:8080. The admin username
and password are both set to 'dev'.

## License ##

*WooCommerce Redirects* is free software, and is released under the terms of the GPL version 2 or (at your option) any
later version. See license.txt for details.
