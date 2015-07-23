-- This DDL was reverse engineered by
-- Toad for MySQL, Version 1.0.3
--
-- at:   JLT5160
-- from: localhost:patrol_report, an MySQL 4.1.8-nt-log database
--
-- on:   Mon Apr 11 11:32:46 2005
--
-- Generating CREATE statement for:
-- TABLE FAMILY:pamdemo.loaded_kms

CREATE TABLE pamdemo.loaded_kms (
    `id` INT ( 4 ) NOT NULL AUTO_INCREMENT,
    `agentid` INT ( 4 ) NOT NULL DEFAULT '0',
    `km` VARCHAR ( 50 ) NOT NULL DEFAULT '',
    `km_version` VARCHAR ( 8 ) NOT NULL DEFAULT '',
    `static` CHAR ( 3 ) NOT NULL DEFAULT '',
    `consoles` INT ( 2 ) NOT NULL DEFAULT '0',
    `date_entered` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
    `date_gathered` datetime DEFAULT NULL,
    PRIMARY KEY ( `id` )
) ENGINE = MyISAM DEFAULT CHARSET = latin1;

