#!/usr/bin/env sh

# This script builds the PHAR using Box.
# It copies over the Composer files to
# avoid any assumed references by Box.
#
# Assumes Box is installed globally.

mv composer-dev.json composer.json
rm -rf vendor composer.lock
composer update --no-dev
box compile --no-parallel
git add builds/shift-cli
git commit -m "Build PHAR"
git push origin HEAD
git checkout .
