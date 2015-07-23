-- This DDL was reverse engineered by
-- Toad for MySQL, Version 1.0.3
--
-- at:   JLT5160
-- from: localhost:patrol_report, an MySQL 4.1.8-nt-log database
--
-- on:   Mon Apr 11 11:32:46 2005
--
-- Generating CREATE statement for:
-- TABLE FAMILY:pamdemo.changes

CREATE TABLE pamdemo.changes (
    `ID` INT ( 4 ) NOT NULL AUTO_INCREMENT,
    `RequestID` INT ( 4 ) DEFAULT NULL,
    `AgentID` INT ( 4 ) DEFAULT NULL,
    `variable` text,
    `oldvalue` text,
    `newvalue` text,
    `date_modified` datetime DEFAULT NULL,
    PRIMARY KEY ( `ID` )
) ENGINE = MyISAM DEFAULT CHARSET = latin1;

