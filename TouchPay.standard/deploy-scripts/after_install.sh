#!/bin/bash
chown -R ec2-user:apache /var/www/html/$APPLICATION_NAME
chmod -R 777 /var/www/html/$APPLICATION_NAME/app/tmp
chmod -R 777 /var/www/html/$APPLICATION_NAME/app/webroot/menu
