UPDATE mysql.user SET plugin = 'mysql_native_password', authentication_string = PASSWORD('changeme') WHERE User = 'root';
FLUSH PRIVILEGES;

SELECT plugin from mysql.user where User='root';