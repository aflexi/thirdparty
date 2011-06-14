#!/bin/bash
# ------------------------------------------------------------------------------
# Update the Aflexi_Common dependency with the latest build.
# 
# Usage: ./update-common.sh <COMMON_DIR> <DEST_DIR>
#
# Author: yclian
# Since: 2.7
# Version: 2.7.20100823
# ------------------------------------------------------------------------------

PWD_EXIT="$PWD"

if [ -n "$1" ]
    then
        COMMON_DIR="$1"
    else
        echo "You must specify the COMMON_DIR"
        exit 1
fi

if [ -n "$2" ]
    then
        DEST_DIR="$2"
    else
        DEST_DIR="$PWD/$(dirname $0)"
fi

COMMON_DIR_TMP=/tmp/update-common-$RANDOM
mkdir -p $COMMON_DIR_TMP

echo "Building aflexi-common in '$COMMON_DIR'.."

cd $COMMON_DIR &&
make clean dist &&
tar -C $COMMON_DIR_TMP -xzf target/LATEST/aflexi-common.tar.gz

echo "Populating files to '$DEST_DIR'.."

cp -fR $COMMON_DIR_TMP/lib/* $DEST_DIR &&
cp -fR $COMMON_DIR_TMP/vendor/* $DEST_DIR &&

cd $PWD_EXIT

echo "Done!"
