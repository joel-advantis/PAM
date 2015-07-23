-- This DDL was reverse engineered by
-- Toad for MySQL, Version 1.0.3
--
-- at:   JLT5160
-- from: localhost:patrol_report, an MySQL 4.1.8-nt-log database
--
-- on:   Mon Apr 11 11:32:45 2005
--
-- Generating CREATE statement for:
-- TABLE FAMILY:pamdemo.agent_groups

CREATE TABLE pamdemo.agent_groups (
    `id` INT ( 4 ) NOT NULL AUTO_INCREMENT,
    `agentid` INT ( 4 ) NOT NULL DEFAULT '0',
    `groupid` INT ( 4 ) NOT NULL DEFAULT '0',
    PRIMARY KEY ( `id` )
) ENGINE = MyISAM DEFAULT CHARSET = latin1;

