#!/bin/bash

# FROM https://aws.amazon.com/blogs/apn/aws-lambda-custom-runtime-for-php-a-practical-example/

PHP_VERSION=$1
if [ -z "$PHP_VERSION" ]; then
    echo "Usage: compile_php.sh <PHP version>"
    exit 1
fi

# Update packages and install needed compilation dependencies
sudo yum update -y
sudo yum install autoconf bison gcc gcc-c++ libcurl-devel libxml2-devel openssl-devel -y

# Download the PHP 7.3.7 source
mkdir ~/php-7-bin
curl -sL https://github.com/php/php-src/archive/php-${PHP_VERSION}.tar.gz | tar -xvz
cd php-src-php-${PHP_VERSION}

# Compile PHP 7.3.7 with OpenSSL 1.0.1 support, and install to /home/ec2-user/php-7-bin
./buildconf --force
./configure --prefix=/home/ec2-user/php-7-bin/ --with-openssl --with-curl --with-zlib --enable-mbstring
make install
