#!/bin/bash

set -e   # (errexit) Exit if any subcommand or pipeline returns a non-zero status
set -u   # (nounset) Exit on any attempt to use an uninitialised variable

shopt -s expand_aliases
alias wp="wp --path=$DOCUMENT_ROOT --allow-root"

: ${WORDPRESS_SITE_URL:="http://localhost:8080"}

if $(wp core is-installed); then
	echo "WordPress already installed."
else
	echo "Installing WordPress..."
	wp core install \
		--url="$WORDPRESS_SITE_URL" \
		--title="Development Site" \
		--admin_user=dev \
		--admin_password=dev \
		--admin_email=dev@example.com
	echo "  Site URL:       $WORDPRESS_SITE_URL"
	echo "  Admin username: dev"
	echo "  Admin password: dev"
	echo "  Admin email:    dev@example.com  (note: PHP mail() function disabled)"
fi

if $(wp plugin is-installed akismet); then
	wp plugin delete akismet
fi

if $(wp plugin is-installed hello); then
    wp plugin delete hello
fi

if $(wp plugin is-installed woocommerce-redirects); then
	wp plugin activate woocommerce-redirects
else
	echo "Warning: Could not find and activate woocommerce-redirects plugin."
fi

if ! $(wp plugin is-installed woocommerce); then
	wp plugin install woocommerce
fi

wp plugin activate woocommerce
