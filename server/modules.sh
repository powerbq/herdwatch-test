#!/bin/bash

set -e

CONFIGS='/usr/local/etc/php/conf.d/docker-php'

MODULES=(
	'zip'
	'opcache'
)

MODULES+=(
	'pdo_mysql'
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

pecl install apcu
echo "extension=apcu.so" > "${CONFIGS}-acpu.ini"
cat > "${CONFIGS}-performance.ini" << EOF
opcache.enable=${OPCODE_ENABLED}
opcache.memory_consumption=256
opcache.max_accelerated_files=100000
EOF

a2enmod rewrite

sed -i 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf
