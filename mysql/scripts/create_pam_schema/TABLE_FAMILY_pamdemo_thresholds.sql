-- This DDL was reverse engineered by
-- Toad for MySQL, Version 1.0.3
--
-- at:   JLT5160
-- from: localhost:patrol_report, an MySQL 4.1.8-nt-log database
--
-- on:   Mon Apr 11 11:32:46 2005
--
-- Generating CREATE statement for:
-- TABLE FAMILY:pamdemo.thresholds

CREATE TABLE pamdemo.thresholds (
    `id` INT ( 4 ) NOT NULL AUTO_INCREMENT,
    `agentid` INT ( 4 ) NOT NULL DEFAULT '0',
    `application_class` VARCHAR ( 50 ) NOT NULL DEFAULT '',
    `instance` VARCHAR ( 200 ) NOT NULL DEFAULT '',
    `parameter` VARCHAR ( 50 ) NOT NULL DEFAULT '',
    `border_active` INT ( 1 ) NOT NULL DEFAULT '0',
    `border_min` INT ( 8 ) NOT NULL DEFAULT '0',
    `border_max` INT ( 8 ) NOT NULL DEFAULT '0',
    `border_trigger` INT ( 1 ) NOT NULL DEFAULT '0',
    `border_occur` INT ( 3 ) NOT NULL DEFAULT '0',
    `border_state` INT ( 1 ) NOT NULL DEFAULT '0',
    `border_recov` INT ( 1 ) NOT NULL DEFAULT '0',
    `alarm1_active` INT ( 1 ) NOT NULL DEFAULT '0',
    `alarm1_min` INT ( 8 ) NOT NULL DEFAULT '0',
    `alarm1_max` INT ( 8 ) NOT NULL DEFAULT '0',
    `alarm1_trigger` INT ( 1 ) NOT NULL DEFAULT '0',
    `alarm1_occur` INT ( 3 ) NOT NULL DEFAULT '0',
    `alarm1_state` INT ( 1 ) NOT NULL DEFAULT '0',
    `alarm1_recov` INT ( 1 ) NOT NULL DEFAULT '0',
    `alarm2_active` INT ( 1 ) NOT NULL DEFAULT '0',
    `alarm2_min` INT ( 8 ) NOT NULL DEFAULT '0',
    `alarm2_max` INT ( 8 ) NOT NULL DEFAULT '0',
    `alarm2_trigger` INT ( 1 ) NOT NULL DEFAULT '0',
    `alarm2_occur` INT ( 3 ) NOT NULL DEFAULT '0',
    `alarm2_state` INT ( 1 ) NOT NULL DEFAULT '0',
    `alarm2_recov` INT ( 1 ) NOT NULL DEFAULT '0',
    `polltime` INT ( 3 ) NOT NULL DEFAULT '0',
    `km_localized` VARCHAR ( 6 ) NOT NULL DEFAULT '',
    `param_value` INT ( 8 ) NOT NULL DEFAULT '0',
    `param_type` VARCHAR ( 4 ) NOT NULL DEFAULT 'CON',
    `arsalarm` INT ( 1 ) NOT NULL DEFAULT '0',
    `arswarning` INT ( 1 ) NOT NULL DEFAULT '0',
    `arsinformation` INT ( 1 ) NOT NULL DEFAULT '0',
    `msgtextalarm` VARCHAR ( 200 ) DEFAULT '',
    `msgTextWarning` VARCHAR ( 200 ) DEFAULT NULL,
    `msgTextInformation` VARCHAR ( 200 ) DEFAULT NULL,
    `alertsystem` VARCHAR ( 6 ) NOT NULL DEFAULT '',
    `customid1` text NOT NULL,
    `customid2` text NOT NULL,
    `blackout` text,
    `date_entered` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
    `date_gathered` datetime DEFAULT NULL,
    PRIMARY KEY ( `id` )
) ENGINE = MyISAM DEFAULT CHARSET = latin1;

