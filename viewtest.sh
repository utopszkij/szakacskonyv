#!/bin/bash
totalError=0
for file in ./tests/*.js
do
    node "$file"
    totalError=$((totalError+$?))
done
echo "================="
if test $totalError -eq 0
then
	echo "$(tput setaf 2)Total error: $totalError"
else
	echo "$(tput setaf 1)Total error: $totalError"
fi
echo "$(tput setaf 7)================="
exit $totalError