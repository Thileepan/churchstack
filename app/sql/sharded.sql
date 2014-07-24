create table PROFILE_DETAILS (
	PROFILE_ID INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
	SALUTATION_ID VARCHAR(255),
	NAME VARCHAR(128),
	UNIQUE_ID INTEGER UNSIGNED,
	DOB DATE,
	GENDER TINYINT NOT NULL,
	RELATION_SHIP SMALLINT,
	MARITAL_STATUS TINYINT,
	MARRIAGE_DATE DATE,
	MARRIAGE_PLACE VARCHAR(128),
	ADDRESS1 VARCHAR(128),
	ADDRESS2 VARCHAR(128),
	ADDRESS3 VARCHAR(128),
	AREA VARCHAR(64),
	PINCODE VARCHAR(10),
	LANDLINE VARCHAR(20),
	MOBILE1 VARCHAR(20),
	MOBILE2 VARCHAR(20),
	EMAIL VARCHAR(64),
	PROFILE_STATUS TINYINT,
	NOTES VARCHAR(1024),
	BABTISED TINYINT,
	CONFIRMATION TINYINT,
	OCCUPATION VARCHAR(256),
	IS_ANOTHER_CHURCH_MEMBER TINYINT,
	PARENT_PROFILE_ID INTEGER,
	MIDDLE_NAME VARCHAR(128),
	LAST_NAME VARCHAR(128),
	WORK_PHONE VARCHAR(20),
	FAMILY_PHOTO_LOCATION VARCHAR(256),
	PROFILE_PHOTO_LOCATION VARCHAR(256),
	EMAIL_NOTIFICATION TINYINT DEFAULT 1,
	SMS_NOTIFICATION TINYINT DEFAULT 1,

	index PROFILE_DETAILS_IDX_1 (NAME),
	index PROFILE_DETAILS_IDX_2 (UNIQUE_ID),
	index PROFILE_DETAILS_IDX_3 (DOB),
	index PROFILE_DETAILS_IDX_4 (GENDER),
	index PROFILE_DETAILS_IDX_5 (MARITAL_STATUS),
	index PROFILE_DETAILS_IDX_6 (MARRIAGE_DATE),
	index PROFILE_DETAILS_IDX_7 (EMAIL),
	index PROFILE_DETAILS_IDX_8 (PROFILE_STATUS),
	index PROFILE_DETAILS_IDX_9 (BABTISED),
	index PROFILE_DETAILS_IDX_10 (CONFIRMATION),
	index PROFILE_DETAILS_IDX_11 (PARENT_PROFILE_ID),
	index PROFILE_DETAILS_IDX_12 (MIDDLE_NAME),
	index PROFILE_DETAILS_IDX_13 (LAST_NAME),
	index PROFILE_DETAILS_IDX_14 (EMAIL_NOTIFICATION),
	index PROFILE_DETAILS_IDX_15 (SMS_NOTIFICATION),
	constraint PROFILE_DETAILS_PK PRIMARY KEY (PROFILE_ID, UNIQUE_ID)
);

create table PROFILE_CUSTOM_FIELDS (
	FIELD_ID INTEGER UNSIGNED NOT NULL unique AUTO_INCREMENT,
	FIELD_NAME VARCHAR(50),
	FIELD_TYPE SMALLINT,
	FIELD_OPTIONS TEXT,
	FIELD_HELP_MESSAGE VARCHAR(1024),
	IS_REQUIRED TINYINT,	
	VALIDATION VARCHAR(250),
	DISPLAY_ORDER INTEGER
);

create table PROFILE_CUSTOM_FIELD_VALUES (
	PROFILE_ID INTEGER UNSIGNED,
	FIELD_ID INTEGER UNSIGNED,
	FIELD_VALUE TEXT
);

create table FUND_DETAILS (
	FUND_ID INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
	FUND_NAME VARCHAR(64),
	FUND_DESCRIPTION VARCHAR(512),

	constraint FUND_DETAILS_PK PRIMARY KEY (FUND_ID)
);

create table BATCH_DETAILS (
	BATCH_ID INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
	BATCH_NAME VARCHAR(64),
	BATCH_DESCRIPTION VARCHAR(512),

	constraint BATCH_DETAILS_PK PRIMARY KEY (BATCH_ID)
);

create table CONTRIBUTION_DETAILS (
	CONTRIBUTION_ID BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
	CONTRIBUTION_DATE DATETIME,
	BATCH_ID INTEGER UNSIGNED,	
	PROFILE_ID INTEGER UNSIGNED, 
	TRANSACTION_TYPE TINYINT,
	PAYMENT_MODE SMALLINT,
	REFERENCE_NUMBER VARCHAR(255),
	LAST_UPDATE_TIME DATETIME,
	LAST_UPDATE_USER_ID BIGINT UNSIGNED,
	LAST_UPDATE_USER_NAME VARCHAR(128),

	constraint CONTRIBUTION_DETAILS PRIMARY KEY (CONTRIBUTION_ID)
);

create table CONTRIBUTION_SPLIT_DETAILS (
	CONTRIBUTION_SPLIT_ID BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
	CONTRIBUTION_ID BIGINT UNSIGNED,
	FUND_ID INTEGER UNSIGNED,	
	AMOUNT DECIMAL(15,2),
	NOTES VARCHAR(255),

	constraint CONTRIBUTION_SPLIT_DETAILS PRIMARY KEY (CONTRIBUTION_SPLIT_ID),
	constraint CONTRIBUTION_SPLIT_DETAILS_FK_1 FOREIGN KEY (CONTRIBUTION_ID) REFERENCES CONTRIBUTION_DETAILS (CONTRIBUTION_ID)
);

create table SUBSCRIPTION_FIELDS (
	FIELD_ID INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
	FIELD_NAME VARCHAR(255),
	HIDE TINYINT UNSIGNED NOT NULL DEFAULT 0,
	IS_ACTIVE TINYINT UNSIGNED NOT NULL DEFAULT 0,
	index SUBSCRIPTION_FIELDS_IDX_1 (FIELD_NAME),
	index SUBSCRIPTION_FIELDS_IDX_2 (HIDE),
	index SUBSCRIPTION_FIELDS_IDX_3 (IS_ACTIVE),
	constraint SUBSCRIPTION_FIELDS_PK PRIMARY KEY (FIELD_ID)
);

create table SUBSCRIPTION_DETAILS (
	SUBSCRIPTION_ID BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
	PROFILE_ID INTEGER UNSIGNED,
	DATE_OF_SUBSCRIPTION DATE,
	SUB_FIELD_1 DECIMAL(15,2),
	SUB_FIELD_2 DECIMAL(15,2),
	SUB_FIELD_3 DECIMAL(15,2),
	SUB_FIELD_4 DECIMAL(15,2),
	SUB_FIELD_5 DECIMAL(15,2),
	SUB_FIELD_6 DECIMAL(15,2),
	SUB_FIELD_7 DECIMAL(15,2),
	SUB_FIELD_8 DECIMAL(15,2),
	SUB_FIELD_9 DECIMAL(15,2),
	SUB_FIELD_10 DECIMAL(15,2),
	SUB_FIELD_11 DECIMAL(15,2),
	SUB_FIELD_12 DECIMAL(15,2),
	SUB_FIELD_13 DECIMAL(15,2),
	SUB_FIELD_14 DECIMAL(15,2),
	SUB_FIELD_15 DECIMAL(15,2),
	SUB_FIELD_16 DECIMAL(15,2),
	SUB_FIELD_17 DECIMAL(15,2),
	SUB_FIELD_18 DECIMAL(15,2),
	SUB_FIELD_19 DECIMAL(15,2),
	SUB_FIELD_20 DECIMAL(15,2),
	TOTAL_AMOUNT DECIMAL(15,2),
	
	index SUBSCRIPTION_DETAILS_IDX_1 (PROFILE_ID),
	index SUBSCRIPTION_DETAILS_IDX_2 (DATE_OF_SUBSCRIPTION),
	constraint SUBSCRIPTION_DETAILS_PK PRIMARY KEY (SUBSCRIPTION_ID),
	constraint SUBSCRIPTION_DETAILS_FK_1 FOREIGN KEY (PROFILE_ID) REFERENCES PROFILE_DETAILS (PROFILE_ID)
);

create table HARVEST_DETAILS (
	HARVEST_ID BIGINT NOT NULL unique AUTO_INCREMENT,
	PROFILE_ID INTEGER UNSIGNED NOT NULL,
	DATE_OF_HARVEST DATE NOT NULL,
	ITEM_DESCRIPTION VARCHAR(255) NOT NULL,
	ITEM_AMOUNT DECIMAL(15,2) NOT NULL,
		
	constraint HARVEST_DETAILS_PK PRIMARY KEY (HARVEST_ID)
);

create table PROFILE_SETTINGS (
	SETTINGS_ID INTEGER UNSIGNED NOT NULL,
	OPTION_ID INTEGER UNSIGNED NOT NULL,
	OPTION_VALUE VARCHAR(255) NOT NULL,

	constraint PROFILE_SETTINGS_PK PRIMARY KEY (SETTINGS_ID, OPTION_ID)
);

create table REPORTS (
	REPORT_ID BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
	TITLE VARCHAR(255) NOT NULL,
	DESCRIPTION VARCHAR(1024) NOT NULL,

	constraint REPORTS_PK PRIMARY KEY (REPORT_ID)
);

create table REPORT_RULES (
	REPORT_ID BIGINT UNSIGNED NOT NULL,
	RULE_TYPE VARCHAR(50) NOT NULL,
	RULE_SUB_TYPE VARCHAR(50) NOT NULL,
	RULE_VALUE VARCHAR(50) NOT NULL,

	index REPORT_RULES_IDX_1 (REPORT_ID),
	constraint REPORT_RULES_FK FOREIGN KEY (REPORT_ID) REFERENCES REPORTS(REPORT_ID)
);

create table REPORT_COLUMNS (
	REPORT_ID BIGINT UNSIGNED NOT NULL,
	COLUMN_CATEGORY VARCHAR(50) NOT NULL,
	COLUMN_DATA VARCHAR(50) NOT NULL,
	COLUMN_HEADING VARCHAR(50) NOT NULL,

	index REPORT_COLUMNS_IDX_1 (REPORT_ID),
	constraint REPORT_COLUMNS_FK FOREIGN KEY (REPORT_ID) REFERENCES REPORTS(REPORT_ID)
);

create table EVENT_DETAILS (
	EVENT_ID BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
	TITLE VARCHAR(255),
	DESCRIPTION VARCHAR(1024),
	EVENT_LOCATION VARCHAR(255),
	START_DATE DATE,
	END_DATE DATE,
	START_TIME SMALLINT UNSIGNED,
	END_TIME SMALLINT UNSIGNED,
	RRULE VARCHAR(512),
	PRIORITY TINYINT UNSIGNED,
	ORGANISER VARCHAR(255),
	ACCESS_LEVEL TINYINT UNSIGNED,

	index EVENT_DETAILS_IDX_1 (TITLE),
	index EVENT_DETAILS_IDX_2 (DESCRIPTION),
	index EVENT_DETAILS_IDX_3 (START_DATE),
	index EVENT_DETAILS_IDX_4 (START_TIME),
	index EVENT_DETAILS_IDX_5 (END_TIME),
	index EVENT_DETAILS_IDX_6 (RRULE),
	index EVENT_DETAILS_IDX_7 (PRIORITY),
	index EVENT_DETAILS_IDX_8 (ORGANISER),
	index EVENT_DETAILS_IDX_9 (ACCESS_LEVEL),
	constraint EVENT_DETAILS_PK PRIMARY KEY (EVENT_ID)
);

create table EVENT_PARTICIPANTS (
	EVENT_ID BIGINT UNSIGNED NOT NULL,
	PARTICIPANT_TYPE TINYINT UNSIGNED,
	PARTICIPANT_ID BIGINT UNSIGNED,

	index EVENT_PARTICIPANTS_IDX_1 (EVENT_ID),
	index EVENT_PARTICIPANTS_IDX_2 (PARTICIPANT_TYPE),
	index EVENT_PARTICIPANTS_IDX_3 (PARTICIPANT_ID),
	constraint EVENT_PARTICIPANTS_FK FOREIGN KEY (EVENT_ID) REFERENCES EVENT_DETAILS(EVENT_ID)
);

create table EVENT_NOTIFICATIONS (
	NOTIFICATION_ID BIGINT UNSIGNED NOT NULL,
	EVENT_ID BIGINT UNSIGNED NOT NULL,
	NOTIFICATION_TYPE TINYINT,
	NOTIFICATION_PERIOD INTEGER UNSIGNED,
	IS_NOTIFICATION_SENT TINYINT UNSIGNED NOT NULL DEFAULT 0,

	index EVENT_NOTIFICATIONS_IDX_1 (EVENT_ID),
	index EVENT_NOTIFICATIONS_IDX_2 (NOTIFICATION_TYPE),
	index EVENT_NOTIFICATIONS_IDX_3 (NOTIFICATION_PERIOD),
	index EVENT_NOTIFICATIONS_IDX_4 (IS_NOTIFICATION_SENT),
	constraint EVENT_PARTICIPANTS_PK PRIMARY KEY (NOTIFICATION_ID),
	constraint EVENT_NOTIFICATIONS_FK FOREIGN KEY (EVENT_ID) REFERENCES EVENT_DETAILS(EVENT_ID)
);

create table EVENT_EMAIL_TEMPLATES (
	EVENT_ID BIGINT UNSIGNED NOT NULL,
	SUBJECT VARCHAR(256),
	CONTENT TEXT,
	ATTACHMENT_PATH VARCHAR(256),
	USE_TEMPLATE_FILE TINYINT UNSIGNED NOT NULL DEFAULT 0,
	TEMPLATE_FILE_PATH VARCHAR(256),

	constraint EVENT_EMAIL_TEMPLATES_PK PRIMARY KEY (EVENT_ID),
	constraint EVENT_EMAIL_TEMPLATES_FK FOREIGN KEY (EVENT_ID) REFERENCES EVENT_DETAILS(EVENT_ID)
);


create table NOTIFICATION_REPORT (
	NOTIFICATION_ID BIGINT UNSIGNED NOT NULL,
	NOTIFICATION_TYPE TINYINT,
	RECIPIENT TEXT,
	SENT_TIME TIMESTAMP,
	STATUS TINYINT UNSIGNED DEFAULT 0,
	MESSAGE VARCHAR(512),

	index NOTIFICATION_REPORT_IDX_1 (NOTIFICATION_ID),
	index NOTIFICATION_REPORT_IDX_2 (NOTIFICATION_TYPE),
	index NOTIFICATION_REPORT_IDX_3 (SENT_TIME),
	index NOTIFICATION_REPORT_IDX_4 (STATUS),
	constraint NOTIFICATION_REPORT_FK FOREIGN KEY (NOTIFICATION_ID) REFERENCES EVENT_NOTIFICATIONS(NOTIFICATION_ID)
);

create table GROUP_DETAILS (
	GROUP_ID INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
	GROUP_NAME VARCHAR(100),
	DESCRIPTION VARCHAR(1024),

	index GROUP_DETAILS_IDX_1 (GROUP_NAME),
	constraint GROUP_DETAILS_PK PRIMARY KEY (GROUP_ID, GROUP_NAME)
);

create table GROUP_MEMBERS (
	GROUP_ID INTEGER UNSIGNED NOT NULL,
	PROFILE_ID INTEGER UNSIGNED,

	index GROUP_MEMBERS_IDX_1 (PROFILE_ID),
	constraint GROUP_MEMBERS_PK PRIMARY KEY (GROUP_ID, PROFILE_ID),
	constraint GROUP_MEMBERS_FK_1 FOREIGN KEY (GROUP_ID) REFERENCES GROUP_DETAILS(GROUP_ID),
	constraint GROUP_MEMBERS_FK_2 FOREIGN KEY (PROFILE_ID) REFERENCES PROFILE_DETAILS(PROFILE_ID)
);

insert into PROFILE_SETTINGS values (1, 1, 'Mr');
insert into PROFILE_SETTINGS values (1, 2, 'Mrs');
insert into PROFILE_SETTINGS values (1, 3, 'Baby');
insert into PROFILE_SETTINGS values (1, 4, 'Dr');
insert into PROFILE_SETTINGS values (1, 5, 'Ms');
insert into PROFILE_SETTINGS values (1, 6, 'Rev');
insert into PROFILE_SETTINGS values (1, 7, 'Master');

insert into PROFILE_SETTINGS values (2, 1, 'Self');
insert into PROFILE_SETTINGS values (2, 2, 'Wife');
insert into PROFILE_SETTINGS values (2, 3, 'Son');
insert into PROFILE_SETTINGS values (2, 4, 'Daughter');
insert into PROFILE_SETTINGS values (2, 5, 'Brother');
insert into PROFILE_SETTINGS values (2, 6, 'Sister');
insert into PROFILE_SETTINGS values (2, 7, 'Husband');
insert into PROFILE_SETTINGS values (2, 8, 'Grand Son');
insert into PROFILE_SETTINGS values (2, 9, 'Grand Daughter');
insert into PROFILE_SETTINGS values (2, 10, 'Mother');
insert into PROFILE_SETTINGS values (2, 11, 'Father');
insert into PROFILE_SETTINGS values (2, 12, 'Mother in Law');
insert into PROFILE_SETTINGS values (2, 13, 'Father in Law');
insert into PROFILE_SETTINGS values (2, 14, 'Daughter in Law');
insert into PROFILE_SETTINGS values (2, 15, 'Son in Law');

insert into PROFILE_SETTINGS values (3, 1, 'Single');
insert into PROFILE_SETTINGS values (3, 2, 'Married');
insert into PROFILE_SETTINGS values (3, 3, 'Widow');
insert into PROFILE_SETTINGS values (3, 4, 'Widower');

insert into PROFILE_SETTINGS values (4, 1, 'Active');
insert into PROFILE_SETTINGS values (4, 2, 'Inactive');

insert into SUBSCRIPTION_FIELDS (FIELD_NAME, HIDE) values ('General', 0);
