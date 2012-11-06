#!/bin/bash

export CWD=`pwd`
export CHANNEL_ROOT=/root/pear
export CHANNEL_NAME=empathy
export PACKAGE_NAME=Empathy
export VERSION=0.9.5
export TEMP_DIR=./tmp

export DATE=`date --rfc-3339=date`
export TIME=`date +%T`

extra=("src/Empathy" "config.yml" "licence.txt" "README" "empathyf")

# removing package from channel
cd ${CHANNEL_ROOT}
if [ -e ./get/${PACKAGE_NAME}-${VERSION}.tar ]; then
    rm ./get/${PACKAGE_NAME}-${VERSION}.tar
    rm ./get/${PACKAGE_NAME}-${VERSION}.tgz
    pirum build ${CHANNEL_ROOT}
fi

# return to source dir
cd ${CWD}


# initialise temp packaging dir
if [ -e ${TEMP_DIR} ]; then
    rm -rf ${TEMP_DIR}
fi

mkdir ${TEMP_DIR}
cd ${TEMP_DIR}


# bundle up base app architype
zip -q -r ./eaa.zip ../eaa/


# cp code and other files
for i in "${extra[@]}"
do
    cp -r ../$i .
done


# repackage and add back to channel
cat ../package.xml.base | sed s/CURRENT_DATE/${DATE}/ | sed s/CURRENT_TIME/${TIME}/ > package.xml
pear package
pirum add ${CHANNEL_ROOT} ${PACKAGE_NAME}-${VERSION}.tgz




