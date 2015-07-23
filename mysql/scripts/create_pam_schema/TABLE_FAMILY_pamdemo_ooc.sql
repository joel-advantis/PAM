-- This DDL was reverse engineered by
-- Toad for MySQL, Version 1.0.3
--
-- at:   JLT5160
-- from: localhost:patrol_report, an MySQL 4.1.8-nt-log database
--
-- on:   Mon Apr 11 11:32:46 2005
--
-- Generating CREATE statement for:
-- TABLE FAMILY:pamdemo.ooc

CREATE TABLE pamdemo.ooc (
    `agentid` INT ( 11 ) DEFAULT '0',
    `hostname` VARCHAR ( 100 ) DEFAULT NULL,
	`display_name` VARCHAR ( 100 ) DEFAULT NULL,
    `port` INT ( 2 ) DEFAULT '3181',
    `variable` text CHARACTER SET latin1 COLLATE latin1_bin,
    `rulevalue` text,
    `actualvalue` text,
    KEY `agentid` ( `agentid` )
) ENGINE = MyISAM DEFAULT CHARSET = latin1;

