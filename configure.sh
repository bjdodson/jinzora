#!/bin/bash
# Jinzora initial config script
#
# Author: Ross Carlson
# Date: 3.19.05
# Version: 2.0
#

if [ ! -f settings.php ]; then
    touch settings.php
fi

chmod 777 settings.php
chmod 777 jukebox/settings.php
chmod 777 -R temp/
chmod 777 -R data/

echo ""
echo "You are now in setup mode."
echo "Please direct your web browser to the directory where you installed Jinzora"
echo "and load index.php - you will then be taken through the complete setup"
echo ""
echo ""

