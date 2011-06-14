#!/bin/bash
# ------------------------------------------------------------------------------
# Name: webinstall.sh - Script that does web installation.
# Sypnosis: bash <(curl -L http://github.com/aflexi/thirdparty/raw/master/cpanel/webinstall.sh)
# Author: yclian
# Since: 2.6.1
# Version: 2.6.1.20100810
# ------------------------------------------------------------------------------
r="$RANDOM"
webinstall_dir="aflexi-thirdparty-cpanel-$r"
strip="strip-components"

# tar-1.14 uses --strip-path, tar-1.14.90+ uses --strip-components
if [ ! `tar --help | grep $strip | wc -l` -eq 1 ]
    then
	strip="strip-path"
fi

mkdir -p $webinstall_dir &&
    cd $webinstall_dir &&
    curl -L http://github.com/aflexi/thirdparty/tarball/master | tar -xzf - --$strip=1 &&
    # Enable this if you want a clean reinstallation.
    # make clean 
    make -C cpanel install &&
    cd .. &&
    rm -rf $webinstall_dir &&
    echo "Web installation done!"
