#!/bin/bash

# TODO
# Strictly compare data before replace

function log {
    echo $(date): $1
}

WWWROOT=/var/www/html
cd $WWWROOT >> /dev/null

time curl -s "http://overpass-api.de/api/interpreter?data=node\[barrier=border_control\];out;" > border.osm.tmp
if [ $? -ne 0 ]; then
    log 'Download failure.'
    exit 0
fi

OLDSIZE=$(wc -c < border.osm)
NEWSIZE=$(wc -c < border.osm.tmp)

if [ $OLDSIZE -ne $NEWSIZE ]; then
    mv -f border.osm.tmp border.osm
    log "$NEWSIZE bytes downloaded. prev size: $OLDSIZE bytes"
else
    log "Border data is not modified since last download."
fi

# ** count nodes
#xmllint --xpath "count(//node)" target.osm