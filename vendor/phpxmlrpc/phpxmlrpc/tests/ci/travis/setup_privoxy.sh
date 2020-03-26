#!/bin/sh

# configure privoxy

sudo cp -f tests/ci/travis/privoxy /etc/privoxy/config
sudo service privoxy restart
