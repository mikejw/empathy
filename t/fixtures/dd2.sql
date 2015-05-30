

DROP DATABASE   IF EXISTS                       etest;
CREATE DATABASE                                 etest;
use                                             etest;
drop table if exists                            fake;


CREATE TABLE    fake(
id                      INT(11)                 AUTO_INCREMENT PRIMARY KEY,
name                    VARCHAR(64)             NOT NULL,
age                     INT(11)                 NOT NULL,
stamp                   TIMESTAMP               NOT NULL,
empty                   INT(11)                 NULL,
foo                     VARCHAR(3)              NULL DEFAULT 'bar',
anotherfield            VARCHAR(255)            NULL
) ENGINE=InnoDB;


