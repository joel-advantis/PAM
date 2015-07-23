-- This DDL was reverse engineered by
-- Toad for MySQL, Version 1.0.3
--
-- at:   JLT5160
-- from: localhost:patrol_report, an MySQL 4.1.8-nt-log database
--
-- on:   Mon Apr 11 11:32:46 2005
--
-- Generating CREATE statement for:
-- TABLE FAMILY:pamdemo.people

CREATE TABLE pamdemo.people (
    `id` INT ( 4 ) NOT NULL AUTO_INCREMENT,
    `Lastname` VARCHAR ( 50 ) DEFAULT NULL,
    `Firstname` VARCHAR ( 50 ) DEFAULT NULL,
    `username` VARCHAR ( 20 ) NOT NULL DEFAULT '',
    `password` VARCHAR ( 32 ) DEFAULT NULL,
    `email` VARCHAR ( 30 ) DEFAULT NULL,
    `date_entered` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
    `last_login` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
    `status` INT ( 1 ) NOT NULL DEFAULT '1',
    PRIMARY KEY ( `id` ),
    UNIQUE KEY `username` ( `username` )
) ENGINE = MyISAM DEFAULT CHARSET = latin1;

