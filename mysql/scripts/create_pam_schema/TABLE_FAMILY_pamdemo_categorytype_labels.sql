USE pamdemo;
--DROP TABLE categorytype_labels;

CREATE TABLE categorytype_labels (
    `id` INT ( 4 ) NOT NULL AUTO_INCREMENT,
    `description` VARCHAR ( 50 ) DEFAULT NULL,
    PRIMARY KEY ( `id` ),
    UNIQUE KEY `description` ( `description` )
);

INSERT INTO categorytype_labels VALUES 
    (1,'Pre-defined'),
    (2,'User-defined');


--DROP TABLE category_functions;

CREATE TABLE category_functions (
    `id` INT ( 4 ) NOT NULL AUTO_INCREMENT,
    `categoryid` INT ( 4 ) DEFAULT NULL,
    `functionid` INT ( 4 ) DEFAULT NULL,
    PRIMARY KEY ( `id` )
);


--DROP TABLE function_labels;

CREATE TABLE function_labels (
    `id` INT ( 4 ) NOT NULL AUTO_INCREMENT,
    `function` VARCHAR ( 50 ) DEFAULT NULL,
    PRIMARY KEY ( `id` ),
    UNIQUE KEY `function` ( `function` )
);

INSERT INTO function_labels VALUES 
    (1,'Thresholds'),
    (2,'Message Wording'),
    (3,'Polling Intervals'),
    (4,'Blackouts'),
    (5,'Notification Targets');


ALTER TABLE change_targets 
ADD COLUMN retry_num INT (2);


ALTER TABLE request_rules 
ADD COLUMN functionid INT(2);

ALTER TABLE change_targets 
ADD COLUMN date_modified DATETIME;

INSERT INTO parameter_descriptions (application_class,parameter,param_type,description,collector) values
('ALL','ALL','CON','This parameter is a placeholder to allow host-wide security to be created.',NULL);

ALTER TABLE thresholds
ADD COLUMN emailalarm TEXT,
ADD COLUMN emailwarning TEXT,
ADD COLUMN emailinformation TEXT;
