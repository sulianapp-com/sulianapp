#!/bin/sh

# set up Apache for hhvm-fcgi
# @see https://github.com/travis-ci/travis-ci.github.com/blob/master/docs/user/languages/php.md#apache--php

sudo a2enmod rewrite actions fastcgi alias ssl

# configure apache virtual hosts
sudo cp -f tests/ci/travis/apache_vhost_hhvm /etc/apache2/sites-available/default
sudo sed -e "s?%TRAVIS_BUILD_DIR%?$(pwd)?g" --in-place /etc/apache2/sites-available/default
sudo service apache2 restart
