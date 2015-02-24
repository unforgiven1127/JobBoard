#!/bin/bash

sestatus -v

chown -R  apache: /opt/projects/
chmod -R 755 /opt/projects

echo -e "-"
/etc/init.d/httpd restart

echo -e "-"
/etc/init.d/mysqld restart

echo -e "-"
/etc/init.d/postgresql restart

echo -e "--- Servers started --- "
