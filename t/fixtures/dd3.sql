

DROP DATABASE   IF EXISTS                       etest;
CREATE DATABASE                                 etest;
use                                             etest;
drop table if exists                            fake,related;


CREATE TABLE    fake(
id                      INT(11)                 AUTO_INCREMENT PRIMARY KEY,
name                    VARCHAR(64)             NOT NULL,
age                     INT(11)                 NOT NULL,
stamp                   TIMESTAMP               NOT NULL,
nonempty                   INT(11)                 NULL,
foo                     VARCHAR(3)              NULL DEFAULT 'bar'
) ENGINE=InnoDB;


CREATE TABLE    related(
id                      INT(11)                 AUTO_INCREMENT PRIMARY KEY,
fake_id                 INT(11)                 NOT NULL,
name                    VARCHAR(64)             NOT NULL,
FOREIGN KEY (fake_id) REFERENCES fake(id)) ENGINE=InnoDB;




