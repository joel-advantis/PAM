-- This DDL was reverse engineered by
-- Toad for MySQL, Version 1.0.3
--
-- at:   JLT5160
-- from: localhost:patrol_report, an MySQL 4.1.8-nt-log database
--
-- on:   Mon Apr 11 11:32:46 2005
--
-- Generating CREATE statement for:
-- TABLE FAMILY:pamdemo.rights

CREATE TABLE pamdemo.rights (
    `ID` INT ( 4 ) NOT NULL AUTO_INCREMENT,
    `Usergroupid` INT ( 4 ) DEFAULT NULL,
    `AgentID` INT ( 4 ) DEFAULT NULL,
    `GroupID` INT ( 4 ) DEFAULT NULL,
    `RulesetID` INT ( 4 ) DEFAULT NULL,
    `ChangeTargetID` INT ( 4 ) DEFAULT NULL,
    `CategoryID` INT ( 4 ) DEFAULT NULL,
    `Actionid` INT ( 4 ) DEFAULT NULL,
    PRIMARY KEY ( `ID` )
) ENGINE = MyISAM DEFAULT CHARSET = latin1;

