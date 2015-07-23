-- This DDL was reverse engineered by
-- Toad for MySQL, Version 1.0.3
--
-- at:   JLT5160
-- from: localhost:patrol_report, an MySQL 4.1.8-nt-log database
--
-- on:   Mon Apr 11 11:32:45 2005
--
-- Generating CREATE statement for:
-- TABLE FAMILY:pamdemo.agent_rules

CREATE TABLE pamdemo.agent_rules (
    `agentid` INT ( 11 ) DEFAULT '0',
    `hostname` text,
    `port` INT ( 2 ) DEFAULT '3181',
    `pos` INT ( 3 ) DEFAULT '0',
    `var` text CHARACTER SET latin1 COLLATE latin1_bin,
    `val` text,
    `vardigest` VARCHAR ( 32 ) DEFAULT NULL,
    `valdigest` VARCHAR ( 32 ) DEFAULT NULL,
    KEY `agentid` ( `agentid` )
) ENGINE = MyISAM DEFAULT CHARSET = latin1;

