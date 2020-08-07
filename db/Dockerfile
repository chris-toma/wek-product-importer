FROM mysql

COPY ./resources/sql/01-mysql-modes.cnf /etc/mysql/conf.d/01-modes.cnf
COPY ./resources/sql/init-system-user.sql /docker-entrypoint-initdb.d/init-system-user.sql

