#!/bin/bash

export CWD=`pwd`
export CHANNEL_ROOT=/root/pear
export CHANNEL_NAME=empathy
export PACKAGE_NAME=Empathy
export VERSION=0.9.5

export DATE=`date --rfc-3339=date`
export TIME=`date +%T`


# removing package from channel
cd ${CHANNEL_ROOT}
if [ -e ./get/${PACKAGE_NAME}-${VERSION}.tar ]; then
    rm ./get/${PACKAGE_NAME}-${VERSION}.tar
    rm ./get/${PACKAGE_NAME}-${VERSION}.tgz
    pirum build ${CHANNEL_ROOT}
fi

# return to source dir
cd ${CWD}

cd ./tmp/

# bundle up base app architype
zip -r ./eaa.zip ../eaa/

cp -r ../src/Empathy .

# repackage and add back to channel
cat ../package.xml.base | sed s/CURRENT_DATE/${DATE}/ | sed s/CURRENT_TIME/${TIME}/ > package.xml
pear package
#pirum add ${CHANNEL_ROOT} ${PACKAGE_NAME}-${VERSION}.tgz



