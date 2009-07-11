#!/bin/sh
cd /home/abesto/jsdoc/jsdoc_toolkit-2.2.0/jsdoc-toolkit
sh ./jsrun.sh -v -a -t=templates/outline /srv/http/form/js/builder -d=/srv/http/form/doc/js
#sh ./jsrun.sh -v -a -t=templates/jsdoc /srv/http/form/js/builder -d=/srv/http/form/doc
cd -
