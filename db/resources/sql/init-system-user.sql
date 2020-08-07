CREATE USER IF NOT EXISTS 'system'@'%';
GRANT ALL ON *.* to 'system'@'%';
FLUSH PRIVILEGES;
