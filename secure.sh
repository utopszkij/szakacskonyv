#!/bin/bash
# attributomok beállítása a futtatáshoz szükséges jogokkal
find . -type d -exec chmod 0755 {} \;
find . -type f -exec chmod 0644 {} \;
find ./images -type d -exec chmod 0755 {} \;
chmod -R 7777 ./images
chmod 0777 ./secure.sh
chmod 0777 ./unsecure.sh


