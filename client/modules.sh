#!/bin/bash

set -e

CONFIGS='/usr/local/etc/php/conf.d/docker-php'

MODULES=(
	'zip'
)

PACKAGES=(
	'libzip-dev'
)

apt-get update
echo ${PACKAGES[*]} | xargs -rn100 apt-get install -y
apt-get clean

echo ${MODULES[*]}  | xargs -rn1   docker-php-ext-configure
echo ${MODULES[*]}  | xargs -rn100 docker-php-ext-install
echo ${MODULES[*]}  | xargs -rn100 docker-php-ext-enable
