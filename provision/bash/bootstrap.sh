#!/usr/bin/env bash

src_folder=$1

if [ -z "$src_folder" ]; then
    echo "Usage: $0 src_folder"
    exit 1
fi

apt-get update >/dev/null 2>&1
echo "Installing apache2..."
apt-get install -y apache2 >/dev/null 2>&1
#echo "Linking sources to /var/www/html"
#if ! [ -L /var/www/html ]; then
#  rm -rf /var/www/html
  # For apache2 the 000-default file in /etc/apache2/sites-enabled points at /var/www/html
#  ln -fs $src_folder /var/www/html
#fi
echo "Installing php..."
apt install -y php libapache2-mod-php >/dev/null 2>&1
echo "Done with apache2 and php install!"
