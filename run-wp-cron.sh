#!/bin/bash

/usr/local/bin/php -d mail.add_x_header=Off -d error_log= ~/public_html/wp-cron.php 2>&1 \
    | ~/bin/ingest.php wp-cron
