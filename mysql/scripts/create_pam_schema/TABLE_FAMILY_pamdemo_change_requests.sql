-- This DDL was reverse engineered by
-- Toad for MySQL, Version 1.0.3
--
-- at:   JLT5160
-- from: localhost:patrol_report, an MySQL 4.1.8-nt-log database
--
-- on:   Mon Apr 11 11:32:46 2005
--
-- Generating CREATE statement for:
-- TABLE FAMILY:pamdemo.change_requests

CREATE TABLE pamdemo.change_requests (
    `ID` INT ( 4 ) NOT NULL AUTO_INCREMENT,
    `PID` INT ( 4 ) DEFAULT NULL,
    `UserID` INT ( 4 ) NOT NULL DEFAULT '0',
    `Priority` INT ( 1 ) NOT NULL DEFAULT '3',
    `Status` INT ( 1 ) NOT NULL DEFAULT '0',
    `ApproverID` INT ( 4 ) DEFAULT NULL,
    `Comment` text,
    `Date_Entered` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
    `Date_Modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
    `Change_Control` VARCHAR ( 15 ) DEFAULT NULL,
    PRIMARY KEY ( `ID` )
) ENGINE = MyISAM DEFAULT CHARSET = latin1;

