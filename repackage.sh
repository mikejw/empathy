#!/bin/bash

export CWD=`pwd`
export CHANNEL_ROOT=/root/pear
export CHANNEL_NAME=empathy
export PACKAGE_NAME=Empathy
export VERSION=0.9.5


# removing package from channel
cd ${CHANNEL_ROOT}
rm ./get/${PACKAGE_NAME}-${VERSION}.tar
rm ./get/${PACKAGE_NAME}-${VERSION}.tgz
pirum build ${CHANNEL_ROOT}

# repackage and add back to channel
cd CWD
pear package
pirum add ${CHANNEL_ROOT} ${PACKAGE_NAME}-${VERSION}.tgz



