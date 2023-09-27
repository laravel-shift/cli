#!/usr/bin/env sh

# This script builds the PHAR using Box.
# It copies over the Composer files to
# avoid any assumed references by Box.
#
# Assumes Box is installed globally.

mv composer-dev.json composer.json
rm -rf vendor composer.lock
composer update --no-dev
rm vendor/shift-tasks.php
sed -i '' -e "s/Shift CLI', '.*'/Shift CLI', '$1'/" shift-cli
box compile --no-parallel
git add builds/shift-cli shift-cli
git commit -m "Build PHAR"
git push origin HEAD
git tag v"$1"
git push origin v"$1"
git checkout .
