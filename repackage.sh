#!/bin/bash

export CHANNEL_ROOT=/root/pear
export VERSION=0.9.5
export PACKAGE_NAME=Empathy


# removing package from channel
cd ${CHANNEL_ROOT}
rm ./get/${PACKAGE_NAME}-${VERSION}.tar
rm ./get/${PACKAGE_NAME}-${VERSION}.tgz
pirum build ${CHANNEL_ROOT}


