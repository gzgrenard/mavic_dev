#!/bin/sh
DATE=$(date +%Y-%m-%d-%H-%M)

tar cvzf /var/www/mavic.com/dev/sites/all/themes/custom/backup/mavic_theme_$DATE.tgz /var/www/mavic.com/dev/sites/all/themes/custom/mavic_theme 
