-- This DDL was reverse engineered by
-- Toad for MySQL, Version 1.0.3
--
-- at:   JLT5160
-- from: localhost:patrol_report, an MySQL 4.1.8-nt-log database
--
-- on:   Mon Apr 11 11:32:46 2005
--
-- Generating CREATE statement for:
-- TABLE FAMILY:pamdemo.purgelist

CREATE TABLE pamdemo.purgelist (
    `agentid` INT ( 4 ) DEFAULT '0',
    `vardigest` VARCHAR ( 32 ) DEFAULT NULL,
    KEY `vardigest` ( `vardigest`,
		      `agentid` )
) ENGINE = MyISAM DEFAULT CHARSET = latin1;

