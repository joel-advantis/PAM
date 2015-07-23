-- This DDL was reverse engineered by
-- Toad for MySQL, Version 1.0.3
--
-- at:   JLT5160
-- from: localhost:patrol_report, an MySQL 4.1.8-nt-log database
--
-- on:   Mon Apr 11 11:32:46 2005
--
-- Generating CREATE statement for:
-- TABLE FAMILY:pamdemo.rules

CREATE TABLE pamdemo.rules (
    `id` INT ( 4 ) NOT NULL AUTO_INCREMENT,
    `rulesetid` INT ( 4 ) NOT NULL DEFAULT '0',
    `variable` text CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
    `operation` VARCHAR ( 7 ) NOT NULL DEFAULT '',
    `value` text,
    `date_entered` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
    PRIMARY KEY ( `id` ),
    KEY `rulesetid` ( `rulesetid` )
) ENGINE = MyISAM DEFAULT CHARSET = latin1;

