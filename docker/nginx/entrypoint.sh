#!/bin/sh
envsubst "`env | awk -F = '{printf \" $$%s\", $$1}'`" < /usr/src/app/docker/nginx/default.conf.template > /etc/nginx/conf.d/default.conf && nginx -g 'daemon off;'
