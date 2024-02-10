CREATE DATABASE php_test_1;

USE php_test_1;

CREATE TABLE humans_list(Id int primary key NOT NULL AUTO_INCREMENT,Name varchar(255),Age decimal(3,0));

INSERT INTO humans_list(Name,Age) VALUES ('Vasya','25'),('John','25'),('Igor','100');
INSERT INTO humans_list(Name,Age) VALUES ('Вася','26'),('Маша','25'),('Даша','35');

CREATE USER 'php_test_1_user'@'localhost' IDENTIFIED BY 'changeme';

GRANT ALL ON php_test_1.* TO 'php_test_1_user'@'localhost';

FLUSH PRIVILEGES;