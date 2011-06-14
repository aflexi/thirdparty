#!/bin/bash
# ------------------------------------------------------------------------------
# Name: install.sh - Accessor to afx-cppinstall.
# Sypnosis: install.sh [ROOT_DIR]
# Description:
#   Install the cPanel CDN Enabler. 
#
#   ROOT_DIR is optionally, default is '/', it is used to determine CPANEL_HOME
#   - /usr/local/cpanel and CPANEL_DATA - /var/cpanel directory.
# 
# Author: yclian
# Since: 2.4
# Version: 2.6.1.20100810
# See: common/sbin/afx-cppinstall
# ------------------------------------------------------------------------------

# We need to go back here later.
export CURRENT_DIR=$PWD
# afx-cppinstall will guess too, but let's be explicit here, a full path, i.e. 
# the current working dir + relative path to script dir. The sed part removes 
# extra '//' that can be possibly generated if our current directory is '/'.
export SOURCE_DIR="$(echo $PWD/$(dirname $0) | sed -e 's/\/\//\//')"

# Using common/sbin as the working directory.
cd "$(dirname $0)/common/sbin"
bash afx-whmcsinstall $*

# Go back to $CURRENT_DIR
cd $CURRENT_DIR

