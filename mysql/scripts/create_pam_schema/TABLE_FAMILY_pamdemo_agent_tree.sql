-- This DDL was reverse engineered by
-- Toad for MySQL, Version 1.0.3
--
-- at:   JLT5160
-- from: localhost:patrol_report, an MySQL 4.1.8-nt-log database
--
-- on:   Mon Apr 11 11:32:45 2005
--
-- Generating CREATE statement for:
-- TABLE FAMILY:pamdemo.agent_tree

CREATE TABLE pamdemo.agent_tree (
    `agentid` INT ( 4 ) DEFAULT NULL,
    `groupid` INT ( 4 ) DEFAULT NULL,
    KEY `agentid` ( `agentid`,
		    `groupid` )
) ENGINE = MyISAM DEFAULT CHARSET = latin1;

