#!/bin/bash

cd /tmp

wget https://pypi.python.org/packages/source/s/supervisor/supervisor-$SUPERVISOR_VERSION.tar.gz
tar xfz supervisor-$SUPERVISOR_VERSION.tar.gz
cd supervisor-$SUPERVISOR_VERSION

sudo python setup.py install
