FROM tristanpenman/wordpress

# Disable mail function
COPY config/docker-php-disable-functions.ini /usr/local/etc/php/conf.d

# Remove unwanted plugins and install/activate others
COPY scripts/00-plugins.sh /scripts/post-install.d
RUN chmod +x /scripts/post-install.d/00-plugins.sh
