#!/bin/bash

echo -e "Do you want to fix code standard automatically?\e[32m [y/N] \e[0m"
read -n 1 -r
echo ""
if [[ ! $REPLY =~ ^[Yy]$ ]]
then
    echo "Aborting..."
    exit 1
fi


SCRIPTFILE=$(readlink -f "$0")
SCRIPTDIR=$(dirname "$SCRIPTFILE")

cd $SCRIPTDIR/../.. && $SCRIPTDIR/../../vendor/bin/phpcbf --standard="$SCRIPTDIR/../../phpcs.xml"

exit 0
