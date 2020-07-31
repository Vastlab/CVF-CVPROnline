#!/bin/sh
DOMAIN='cvpr20.com'
# Fetching desktop version
wget --spider -o wget.log -e robots=off -r -l 5 -p -S --header="X-Bypass-Cache: 1" $DOMAIN --wait 1                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                
