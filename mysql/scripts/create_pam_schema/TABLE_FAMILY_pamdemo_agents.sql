-- This DDL was reverse engineered by
-- Toad for MySQL, Version 1.0.3
--
-- at:   JLT5160
-- from: localhost:patrol_report, an MySQL 4.1.8-nt-log database
--
-- on:   Mon Apr 11 11:32:46 2005
--
-- Generating CREATE statement for:
-- TABLE FAMILY:pamdemo.agents

CREATE TABLE pamdemo.agents (
    `id` INT ( 4 ) NOT NULL AUTO_INCREMENT,
    `hostname` VARCHAR ( 50 ) NOT NULL DEFAULT '',
    `ipaddress` VARCHAR ( 15 ) DEFAULT NULL,
    `display_name` VARCHAR ( 50 ) DEFAULT NULL,
    `port` INT ( 2 ) NOT NULL DEFAULT '3181',
    `protocol` CHAR ( 3 ) DEFAULT 'tcp',
    `source` VARCHAR ( 10 ) DEFAULT NULL,
    `status` VARCHAR ( 10 ) DEFAULT NULL,
    `date_entered` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
    PRIMARY KEY ( `id` ),
    KEY `i1` ( `hostname` ( 20 ) )
) ENGINE = MyISAM DEFAULT CHARSET = latin1;

