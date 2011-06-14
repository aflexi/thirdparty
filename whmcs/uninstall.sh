#!/bin/bash
# ------------------------------------------------------------------------------
# Name: uninstall.sh - Accessor to afx-cppuninstall.
# Sypnosis: uninstall.sh [ROOT_DIR]
# Description:
#   Uninstall the cPanel CDN Enabler. 
#
#   ROOT_DIR is optionally, default is '/', it is used to determine CPANEL_HOME
#   - /usr/local/cpanel and CPANEL_DATA - /var/cpanel directory.
# 
# Author: yclian
# Since: 2.6.1
# Version: 2.6.1.20100809
# See: common/sbin/afx-cppuninstall
# ------------------------------------------------------------------------------

export CURRENT_DIR=$PWD
export SOURCE_DIR="$(echo $PWD/$(dirname $0) | sed -e 's/\/\//\//')"

cd "$(dirname $0)/common/sbin"
bash afx-whmuninstall $*
cd $CURRENT_DIR
