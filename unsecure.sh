#!/bin/bash
# attributomok beállítása 777 -re
find . -type d -exec chmod 777 {} \;
find . -type f -exec chmod 777 {} \;

