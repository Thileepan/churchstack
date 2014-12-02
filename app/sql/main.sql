create database churchstack collate latin1_general_cs;
use churchstack;

create table CURRENCY_LIST (
	CURRENCY_ID SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT,
	CURRENCY_CODE VARCHAR(64) NOT NULL UNIQUE,
	CURRENCY_NUMBER VARCHAR(64) NOT NULL UNIQUE,
	CURRENCY_DESCRIPTION VARCHAR(512),
	COUNTRY VARCHAR(1024),
	index CURRENCY_LIST_IDX_1 (CURRENCY_CODE),
	index CURRENCY_LIST_IDX_2 (CURRENCY_NUMBER),
	constraint CURRENCY_LIST_PK PRIMARY KEY (CURRENCY_ID)
);

create table COUNTRY_LIST (
	COUNTRY_ID SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT,
	COUNTRY_ISO_CODE VARCHAR(5),
	COUNTRY_NAME_CAPS VARCHAR(128),
	COUNTRY_NAME VARCHAR(128),
	COUNTRY_ISO3_CODE VARCHAR(5),
	COUNTRY_NUMERIC_CODE INTEGER,
	COUNTRY_CALLING_CODE INTEGER,

	index COUNTRY_LIST_IDX_1 (COUNTRY_ISO_CODE),
	index COUNTRY_LIST_IDX_2 (COUNTRY_NAME_CAPS),
	index COUNTRY_LIST_IDX_3 (COUNTRY_NAME),
	index COUNTRY_LIST_IDX_4 (COUNTRY_ISO3_CODE),
	index COUNTRY_LIST_IDX_5 (COUNTRY_NUMERIC_CODE),
	index COUNTRY_LIST_IDX_6 (COUNTRY_CALLING_CODE),
	constraint COUNTRY_LIST_PK PRIMARY KEY (COUNTRY_ID)
);

create table CHURCH_DETAILS (
	CHURCH_ID INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
	CHURCH_NAME VARCHAR(128) NOT NULL,
	DESCRIPTION VARCHAR(1024),
	ADDRESS VARCHAR(1024),
	LANDLINE VARCHAR(20),
	MOBILE VARCHAR(20),
	EMAIL VARCHAR(255),
	WEBSITE VARCHAR(255),
	SIGNUP_TIME DATETIME,
	LAST_UPDATE_TIME DATETIME,
	SHARDED_DATABASE VARCHAR(255),
	CURRENCY_ID SMALLINT UNSIGNED,
	UNIQUE_HASH VARCHAR(128) NOT NULL,
	STATUS TINYINT UNSIGNED NOT NULL DEFAULT 1,
	COUNTRY_ID SMALLINT UNSIGNED,
	REFERRER_CHURCH_ID INTEGER UNSIGNED,
	TIME_ZONE VARCHAR(128),

	constraint CHURCH_DETAILS_PK PRIMARY KEY (CHURCH_ID)
);

create table LICENSE_PLANS (
	PLAN_ID SMALLINT UNSIGNED NOT NULL,
	PLAN_NAME VARCHAR(128),
	PLAN_DESCRIPTION VARCHAR(1024),
	PLAN_TYPE SMALLINT UNSIGNED,
	MAX_COUNT INTEGER,
	PRICING DECIMAL(15,2),
	VALIDITY_IN_SECONDS BIGINT UNSIGNED,
	VALIDITY_IN_DAYS INTEGER UNSIGNED,

	index LICENSE_PLANS_IDX_1 (PLAN_TYPE, PLAN_ID),
	constraint LICENSE_PLANS_PK PRIMARY KEY (PLAN_ID)
);

create table USER_ROLES (
	ROLE_ID SMALLINT UNSIGNED NOT NULL,
	ROLE_DESCRIPTION VARCHAR(255),

	constraint USER_ROLES_PK PRIMARY KEY (ROLE_ID)
);

create table USER_DETAILS (
	USER_ID BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
	CHURCH_ID INTEGER UNSIGNED NOT NULL,
	USER_NAME VARCHAR(128) NOT NULL UNIQUE,
	EMAIL VARCHAR(128) NOT NULL UNIQUE,
	ROLE_ID SMALLINT UNSIGNED NOT NULL DEFAULT 1,
	PASSWORD VARCHAR(128) NOT NULL,
	UNIQUE_HASH VARCHAR(128),
	PASSWORD_RESET_HASH VARCHAR(128),
	PASSWORD_RESET_EXPIRY BIGINT UNSIGNED,
	STATUS TINYINT UNSIGNED NOT NULL DEFAULT 1,
	PROFILE_ID BIGINT DEFAULT 0,
	PROFILE_FULL_NAME VARCHAR(512),

	index USER_DETAILS_IDX_1 (USER_NAME, PASSWORD),
	index USER_DETAILS_IDX_2 (EMAIL, PASSWORD),
	index USER_DETAILS_IDX_3 (CHURCH_ID),
	index USER_DETAILS_IDX_4 (UNIQUE_HASH),
	index USER_DETAILS_IDX_5 (EMAIL, PASSWORD_RESET_HASH),
	constraint USER_DETAILS_PK PRIMARY KEY (USER_ID),
	constraint USER_DETAILS_FK_1 FOREIGN KEY (CHURCH_ID) REFERENCES CHURCH_DETAILS (CHURCH_ID),
	constraint USER_DETAILS_FK_2 FOREIGN KEY (ROLE_ID) REFERENCES USER_ROLES (ROLE_ID)
);

create table INVOICE_REPORT (
	INVOICE_ID BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
	INVOICE_DATE DATETIME,
	TRANSACTION_ID VARCHAR(256),
	REFERENCE_ID VARCHAR(256),
	UNIQUE_HASH VARCHAR(128),
	CHURCH_ID INTEGER UNSIGNED,
	CHURCH_NAME VARCHAR(128),
	USER_ID INTEGER UNSIGNED,
	EMAIL VARCHAR(128),
	BILLING_NAME VARCHAR(128),
	BILLING_ADDRESS VARCHAR(1024),
	OTHER_ADDRESS VARCHAR(1024),
	PHONE VARCHAR(20),
	CURRENCY_CODE VARCHAR(64),
	SUBTOTAL DECIMAL(15,2),
	ADDITIONAL_CHARGE DECIMAL(15,2),
	DISCOUNT_PERCENTAGE DECIMAL(5,2),
	DISCOUNT_AMOUNT DECIMAL(15,2),
	TAX_PERCENTAGE DECIMAL(5,2),
	TAX_AMOUNT DECIMAL(15,2),
	TAX_2_PERCENTAGE DECIMAL(5,2),
	TAX_2_AMOUNT DECIMAL(15,2),
	VAT_PERCENTAGE DECIMAL(5,2),
	VAT_AMOUNT DECIMAL(15,2),
	NET_TOTAL DECIMAL(15,2),
	COUPON_CODE VARCHAR(64),
	INVOICE_NOTES VARCHAR(512),
	PAYMENT_GATEWAY VARCHAR(128),
	PAYMENT_MODE VARCHAR(128),
	IP_ADDRESS VARCHAR(128),
	PURCHASE_STATUS_CODE TINYINT UNSIGNED,
	PURCHASE_STATUS_REMARKS VARCHAR(512),
	PG_STATUS_CODE VARCHAR(256),
	PG_STATUS_REMARKS VARCHAR(512),
	LAST_UPDATE_DATE DATETIME,
	IS_REFUND TINYINT UNSIGNED NOT NULL DEFAULT 0,

	index INVOICE_REPORT_IDX_1 (INVOICE_DATE),
	index INVOICE_REPORT_IDX_2 (UNIQUE_HASH),
	index INVOICE_REPORT_IDX_3 (CHURCH_ID),
	index INVOICE_REPORT_IDX_4 (USER_ID),
	index INVOICE_REPORT_IDX_5 (PURCHASE_STATUS_CODE),
	index INVOICE_REPORT_IDX_6 (PG_STATUS_CODE),
	index INVOICE_REPORT_IDX_7 (LAST_UPDATE_DATE),
	index INVOICE_REPORT_IDX_8 (TRANSACTION_ID),
	index INVOICE_REPORT_IDX_9 (REFERENCE_ID),
	constraint INVOICE_REPORT_PK PRIMARY KEY (INVOICE_ID)
);

create table INVOICED_ITEMS (
	INVOICE_ID BIGINT UNSIGNED,
	SUBORDER_ID VARCHAR(64),
	PLAN_ID SMALLINT UNSIGNED,
	PLAN_NAME VARCHAR(128),
	PLAN_DESCRIPTION VARCHAR(512),
	PLAN_TYPE SMALLINT UNSIGNED,
	VALIDITY_PERIOD_TEXT VARCHAR(64),
	VALIDITY_IN_SECONDS BIGINT UNSIGNED,
	PLAN_COST DECIMAL(15,2),
	QUANTITY INTEGER UNSIGNED,
	TOTAL_COST DECIMAL(15,2),
	IS_AUTORENEWAL_ENABLED TINYINT UNSIGNED NOT NULL DEFAULT 0,
	
	index INVOICED_ITEMS_IDX_1 (INVOICE_ID),
	constraint INVOICED_ITEMS_FK_1 FOREIGN KEY (INVOICE_ID) REFERENCES INVOICE_REPORT (INVOICE_ID)
);

create table COUPONS (
	COUPON_ID BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
	COUPON_CODE VARCHAR(64) UNIQUE,
	CHURCH_ID BIGINT UNSIGNED,
	DISCOUNT_PERCENTAGE DECIMAL(5,2),
	DISCOUNT_FLAT_AMOUNT DECIMAL(15,2),
	MINIMUM_SUBTOTAL DECIMAL(15,2),
	VALIDITY DATETIME,
	VALID_FOR_ALL TINYINT UNSIGNED NOT NULL DEFAULT 0,
	IS_USED TINYINT UNSIGNED NOT NULL DEFAULT 0,

	index COUPONS_IDX_1 (COUPON_CODE, CHURCH_ID, IS_USED, VALIDITY, MINIMUM_SUBTOTAL),
	index COUPONS_IDX_2 (COUPON_CODE, VALID_FOR_ALL, IS_USED, VALIDITY, MINIMUM_SUBTOTAL),
	index COUPONS_IDX_3 (IS_USED, COUPON_CODE),
	constraint COUPON_CODES_PK PRIMARY KEY (COUPON_ID)
);

create table LICENSE_DETAILS (
	CHURCH_ID INTEGER UNSIGNED NOT NULL,
	PLAN_ID SMALLINT UNSIGNED,
	PLAN_TYPE SMALLINT UNSIGNED,
	LICENSE_EXPIRY_DATE DATETIME,
	LAST_INVOICE_ID BIGINT UNSIGNED,
	LAST_PURCHASE_DATE DATETIME,
	IS_ON_TRIAL TINYINT UNSIGNED NOT NULL DEFAULT 1,
	TRIAL_EXPIRY_DATE DATETIME,

	constraint LICENSE_DETAILS_FK_1 FOREIGN KEY (CHURCH_ID) REFERENCES CHURCH_DETAILS (CHURCH_ID),
	constraint LICENSE_DETAILS_FK_2 FOREIGN KEY (PLAN_ID) REFERENCES LICENSE_PLANS (PLAN_ID)
	);

create table AUTO_NOTIFICATIONS_REPORT (
	NOTIFICATION_TYPE VARCHAR(64),
	SUBJECT_INTERNAL_ID BIGINT UNSIGNED,
	FOR_OCCURRENCE VARCHAR(256),
	UPDATED_ON DATETIME,

	constraint AUTO_NOTIFICATIONS_REPORT_PK PRIMARY KEY (NOTIFICATION_TYPE, SUBJECT_INTERNAL_ID, FOR_OCCURRENCE)
);

insert into CURRENCY_LIST values(0, 'AED', '784', 'United Arab Emirates Dirham', 'United Arab Emirates');
insert into CURRENCY_LIST values(0, 'AFN', '971', 'Afghan Afghani', 'Afghanistan');
insert into CURRENCY_LIST values(0, 'ALL', '008', 'Albanian Lek', 'Albania');
insert into CURRENCY_LIST values(0, 'AMD', '051', 'Armenian Dram', 'Armenia');
insert into CURRENCY_LIST values(0, 'ANG', '532', 'Netherlands Antillean Guilder', 'Curacao (CW),  Sint Maarten (SX)');
insert into CURRENCY_LIST values(0, 'AOA', '973', 'Angolan Kwanza', 'Angola');
insert into CURRENCY_LIST values(0, 'ARS', '032', 'Argentine Peso', 'Argentina');
insert into CURRENCY_LIST values(0, 'AUD', '036', 'Australian Dollar', 'Australia,  Christmas Island (CX),  Cocos (Keeling) Islands (CC),  Heard and McDonald Islands (HM),  Kiribati (KI),  Nauru (NR),  Norfolk Island (NF),  Tuvalu (TV), and Australian Antarctic Territory');
insert into CURRENCY_LIST values(0, 'AWG', '533', 'Aruban Florin', 'Aruba');
insert into CURRENCY_LIST values(0, 'AZN', '944', 'Azerbaijani Manat', 'Azerbaijan');
insert into CURRENCY_LIST values(0, 'BAM', '977', 'Bosnia and Herzegovina convertible Mark', 'Bosnia and Herzegovina');
insert into CURRENCY_LIST values(0, 'BBD', '052', 'Barbados Dollar', 'Barbados');
insert into CURRENCY_LIST values(0, 'BDT', '050', 'Bangladeshi Taka', 'Bangladesh');
insert into CURRENCY_LIST values(0, 'BGN', '975', 'Bulgarian Lev', 'Bulgaria');
insert into CURRENCY_LIST values(0, 'BHD', '048', 'Bahraini Dinar', 'Bahrain');
insert into CURRENCY_LIST values(0, 'BIF', '108', 'Burundian Franc', 'Burundi');
insert into CURRENCY_LIST values(0, 'BMD', '060', 'Bermudian Dollar', 'Bermuda');
insert into CURRENCY_LIST values(0, 'BND', '096', 'Brunei Dollar', 'Brunei, auxiliary in  Singapore (SG)');
insert into CURRENCY_LIST values(0, 'BOB', '068', 'Boliviano', 'Bolivia');
insert into CURRENCY_LIST values(0, 'BRL', '986', 'Brazilian Real', 'Brazil');
insert into CURRENCY_LIST values(0, 'BSD', '044', 'Bahamian Dollar', 'Bahamas');
insert into CURRENCY_LIST values(0, 'BTN', '064', 'Bhutanese Ngultrum', 'Bhutan');
insert into CURRENCY_LIST values(0, 'BWP', '072', 'Botswana Pula', 'Botswana');
insert into CURRENCY_LIST values(0, 'BYR', '974', 'Belarusian Ruble', 'Belarus');
insert into CURRENCY_LIST values(0, 'BZD', '084', 'Belize Dollar', 'Belize');
insert into CURRENCY_LIST values(0, 'CAD', '124', 'Canadian Dollar', 'Canada');
insert into CURRENCY_LIST values(0, 'CDF', '976', 'Congolese Franc', 'Democratic Republic of Congo');
insert into CURRENCY_LIST values(0, 'CHF', '756', 'Swiss Franc', ' Switzerland,  Liechtenstein (LI)');
insert into CURRENCY_LIST values(0, 'CLP', '152', 'Chilean Peso', 'Chile');
insert into CURRENCY_LIST values(0, 'CNY', '156', 'Chinese Yuan', 'China');
insert into CURRENCY_LIST values(0, 'COP', '170', 'Colombian Peso', 'Colombia');
insert into CURRENCY_LIST values(0, 'CRC', '188', 'Costa Rican Colon', 'Costa Rica');
insert into CURRENCY_LIST values(0, 'CUC', '931', 'Cuban convertible Peso', 'Cuba');
insert into CURRENCY_LIST values(0, 'CUP', '192', 'Cuban Peso', 'Cuba');
insert into CURRENCY_LIST values(0, 'CVE', '132', 'Cape Verde Escudo', 'Cape Verde');
insert into CURRENCY_LIST values(0, 'CZK', '203', 'Czech Koruna', 'Czech Republic');
insert into CURRENCY_LIST values(0, 'DJF', '262', 'Djiboutian Franc', 'Djibouti');
insert into CURRENCY_LIST values(0, 'DKK', '208', 'Danish Krone', 'Denmark,  Faroe Islands (FO),  Greenland (GL)');
insert into CURRENCY_LIST values(0, 'DOP', '214', 'Dominican Peso', 'Dominican Republic');
insert into CURRENCY_LIST values(0, 'DZD', '012', 'Algerian Dinar', 'Algeria');
insert into CURRENCY_LIST values(0, 'EGP', '818', 'Egyptian Pound', 'Egypt, auxiliary in Gaza Strip');
insert into CURRENCY_LIST values(0, 'ERN', '232', 'Eritrean Nakfa', 'Eritrea');
insert into CURRENCY_LIST values(0, 'ETB', '230', 'Ethiopian Birr', 'Ethiopia');
insert into CURRENCY_LIST values(0, 'EUR', '978', 'Euro', 'Andorra (AD),  Austria (AT),  Belgium (BE),  Cyprus (CY) except  Northern Cyprus,  Estonia (EE),  Finland (FI),  France (FR),  Germany (DE),  Greece (GR),  Ireland (IE),  Italy (IT),  Kosovo,  Latvia (LV),  Luxembourg (LU),  Malta (MT),  Martinique (MQ),  Mayotte (YT),  Monaco (MC),  Montenegro (ME),  Netherlands (NL),  Portugal (PT),  Reunion (RE),  San Marino (SM),  Saint Barthelemy (BL),  Slovakia (SK),  Slovenia (SI),  Spain (ES),  Saint Pierre and Miquelon (PM),   Vatican City (VA); see Eurozone');
insert into CURRENCY_LIST values(0, 'FJD', '242', 'Fiji Dollar', 'Fiji');
insert into CURRENCY_LIST values(0, 'FKP', '238', 'Falkland Islands Pound', 'Falkland Islands (pegged to GBP 1:1)');
insert into CURRENCY_LIST values(0, 'GBP', '826', 'Pound Sterling', 'United Kingdom, the  Isle of Man (IM, see Manx pound),  Jersey (JE, see Jersey pound), Guernsey (GG, see Guernsey pound),  South Georgia and the South Sandwich Islands (GS),  British Indian Ocean Territory (IO) (also uses USD),  Tristan da Cunha (SH-TA), and  British Antarctic Territory');
insert into CURRENCY_LIST values(0, 'GEL', '981', 'Georgian Lari', 'Georgia (country) (except Abkhazia (GE-AB) and South Ossetia)');
insert into CURRENCY_LIST values(0, 'GHS', '936', 'Ghanaian Cedi', 'Ghana');
insert into CURRENCY_LIST values(0, 'GIP', '292', 'Gibraltar Pound', 'Gibraltar');
insert into CURRENCY_LIST values(0, 'GMD', '270', 'Gambian Dalasi', 'Gambia');
insert into CURRENCY_LIST values(0, 'GNF', '324', 'Guinean Franc', 'Guinea');
insert into CURRENCY_LIST values(0, 'GTQ', '320', 'Guatemalan Quetzal', 'Guatemala');
insert into CURRENCY_LIST values(0, 'GYD', '328', 'Guyanese Dollar', 'Guyana');
insert into CURRENCY_LIST values(0, 'HKD', '344', 'Hong Kong Dollar', 'Hong Kong,  Macao (MO)');
insert into CURRENCY_LIST values(0, 'HNL', '340', 'Honduran Lempira', 'Honduras');
insert into CURRENCY_LIST values(0, 'HRK', '191', 'Croatian Kuna', 'Croatia');
insert into CURRENCY_LIST values(0, 'HTG', '332', 'Haitian Gourde', 'Haiti');
insert into CURRENCY_LIST values(0, 'HUF', '348', 'Hungarian Forint', 'Hungary');
insert into CURRENCY_LIST values(0, 'IDR', '360', 'Indonesian Rupiah', 'Indonesia');
insert into CURRENCY_LIST values(0, 'ILS', '376', 'Israeli New Shekel', 'Israel,  State of Palestine (PS)[8]');
insert into CURRENCY_LIST values(0, 'INR', '356', 'Indian Rupee', 'India');
insert into CURRENCY_LIST values(0, 'IQD', '368', 'Iraqi Dinar', 'Iraq');
insert into CURRENCY_LIST values(0, 'IRR', '364', 'Iranian Rial', 'Iran');
insert into CURRENCY_LIST values(0, 'ISK', '352', 'Icelandic Krona', 'Iceland');
insert into CURRENCY_LIST values(0, 'JMD', '388', 'Jamaican Dollar', 'Jamaica');
insert into CURRENCY_LIST values(0, 'JOD', '400', 'Jordanian Dinar', 'Jordan, auxiliary in West Bank');
insert into CURRENCY_LIST values(0, 'JPY', '392', 'Japanese Yen', 'Japan');
insert into CURRENCY_LIST values(0, 'KES', '404', 'Kenyan Shilling', 'Kenya');
insert into CURRENCY_LIST values(0, 'KGS', '417', 'Kyrgyzstani Som', 'Kyrgyzstan');
insert into CURRENCY_LIST values(0, 'KHR', '116', 'Cambodian Riel', 'Cambodia');
insert into CURRENCY_LIST values(0, 'KMF', '174', 'Comoro Franc', 'Comoros');
insert into CURRENCY_LIST values(0, 'KPW', '408', 'North Korean Won', 'North Korea');
insert into CURRENCY_LIST values(0, 'KRW', '410', 'South Korean Won', 'South Korea');
insert into CURRENCY_LIST values(0, 'KWD', '414', 'Kuwaiti Dinar', 'Kuwait');
insert into CURRENCY_LIST values(0, 'KYD', '136', 'Cayman Islands Dollar', 'Cayman Islands');
insert into CURRENCY_LIST values(0, 'KZT', '398', 'Kazakhstani Tenge', 'Kazakhstan');
insert into CURRENCY_LIST values(0, 'LAK', '418', 'Lao Kip', 'Laos');
insert into CURRENCY_LIST values(0, 'LBP', '422', 'Lebanese Pound', 'Lebanon');
insert into CURRENCY_LIST values(0, 'LKR', '144', 'Sri Lankan Rupee', 'Sri Lanka');
insert into CURRENCY_LIST values(0, 'LRD', '430', 'Liberian Dollar', 'Liberia');
insert into CURRENCY_LIST values(0, 'LSL', '426', 'Lesotho Loti', 'Lesotho');
insert into CURRENCY_LIST values(0, 'LTL', '440', 'Lithuanian Litas', 'Lithuania');
insert into CURRENCY_LIST values(0, 'LYD', '434', 'Libyan Dinar', 'Libya');
insert into CURRENCY_LIST values(0, 'MAD', '504', 'Moroccan Dirham', 'Morocco');
insert into CURRENCY_LIST values(0, 'MDL', '498', 'Moldovan Leu', 'Moldova (except  Transnistria)');
insert into CURRENCY_LIST values(0, 'MGA', '969', 'Malagasy Ariary', 'Madagascar');
insert into CURRENCY_LIST values(0, 'MKD', '807', 'Macedonian Denar', 'Macedonia');
insert into CURRENCY_LIST values(0, 'MMK', '104', 'Myanma Kyat', 'Myanmar');
insert into CURRENCY_LIST values(0, 'MNT', '496', 'Mongolian Tugrik', 'Mongolia');
insert into CURRENCY_LIST values(0, 'MOP', '446', 'Macanese Pataca', 'Macao');
insert into CURRENCY_LIST values(0, 'MRO', '478', 'Mauritanian Ouguiya', 'Mauritania');
insert into CURRENCY_LIST values(0, 'MUR', '480', 'Mauritian Rupee', 'Mauritius');
insert into CURRENCY_LIST values(0, 'MVR', '462', 'Maldivian Rufiyaa', 'Maldives');
insert into CURRENCY_LIST values(0, 'MWK', '454', 'Malawian Kwacha', 'Malawi');
insert into CURRENCY_LIST values(0, 'MXN', '484', 'Mexican Peso', 'Mexico');
insert into CURRENCY_LIST values(0, 'MYR', '458', 'Malaysian Ringgit', 'Malaysia');
insert into CURRENCY_LIST values(0, 'MZN', '943', 'Mozambican Metical', 'Mozambique');
insert into CURRENCY_LIST values(0, 'NAD', '516', 'Namibian Dollar', 'Namibia');
insert into CURRENCY_LIST values(0, 'NGN', '566', 'Nigerian Naira', 'Nigeria');
insert into CURRENCY_LIST values(0, 'NIO', '558', 'Nicaraguan Cordoba', 'Nicaragua');
insert into CURRENCY_LIST values(0, 'NOK', '578', 'Norwegian Krone', 'Norway,  Svalbard and  Jan Mayen (SJ),  Bouvet Island (BV), Queen Maud Land, Peter I Island');
insert into CURRENCY_LIST values(0, 'NPR', '524', 'Nepalese Rupee', '  Nepal');
insert into CURRENCY_LIST values(0, 'NZD', '554', 'New Zealand Dollar', 'New Zealand,  Cook Islands (CK),  Niue (NU),  Pitcairn (PN; see also Pitcairn Islands dollar),  Tokelau (TK), Ross Dependency');
insert into CURRENCY_LIST values(0, 'OMR', '512', 'Omani Rial', 'Oman');
insert into CURRENCY_LIST values(0, 'PAB', '590', 'Panamanian Balboa', 'Panama');
insert into CURRENCY_LIST values(0, 'PEN', '604', 'Peruvian Nuevo Sol', 'Peru');
insert into CURRENCY_LIST values(0, 'PGK', '598', 'Papua New Guinean Kina', 'Papua New Guinea');
insert into CURRENCY_LIST values(0, 'PHP', '608', 'Philippine Peso', 'Philippines');
insert into CURRENCY_LIST values(0, 'PKR', '586', 'Pakistani Rupee', 'Pakistan');
insert into CURRENCY_LIST values(0, 'PLN', '985', 'Polish Zloty', 'Poland');
insert into CURRENCY_LIST values(0, 'PYG', '600', 'Paraguayan Guarani', 'Paraguay');
insert into CURRENCY_LIST values(0, 'QAR', '634', 'Qatari Riyal', 'Qatar');
insert into CURRENCY_LIST values(0, 'RON', '946', 'Romanian New Leu', 'Romania');
insert into CURRENCY_LIST values(0, 'RSD', '941', 'Serbian Dinar', 'Serbia');
insert into CURRENCY_LIST values(0, 'RUB', '643', 'Russian Ruble', 'Russia,  Abkhazia (GE-AB),  South Ossetia,  Crimea');
insert into CURRENCY_LIST values(0, 'RWF', '646', 'Rwandan Franc', 'Rwanda');
insert into CURRENCY_LIST values(0, 'SAR', '682', 'Saudi Riyal', 'Saudi Arabia');
insert into CURRENCY_LIST values(0, 'SBD', '090', 'Solomon Islands Dollar', 'Solomon Islands');
insert into CURRENCY_LIST values(0, 'SCR', '690', 'Seychelles Rupee', 'Seychelles');
insert into CURRENCY_LIST values(0, 'SDG', '938', 'Sudanese Pound', 'Sudan');
insert into CURRENCY_LIST values(0, 'SEK', '752', 'Swedish Krona/Kronor', 'Sweden');
insert into CURRENCY_LIST values(0, 'SGD', '702', 'Singapore Dollar', 'Singapore, auxiliary in  Brunei (BN)');
insert into CURRENCY_LIST values(0, 'SHP', '654', 'Saint Helena Pound', 'Saint Helena (SH-SH),  Ascension Island (SH-AC) (pegged to GBP 1:1)');
insert into CURRENCY_LIST values(0, 'SLL', '694', 'Sierra Leonean Leone', 'Sierra Leone');
insert into CURRENCY_LIST values(0, 'SOS', '706', 'Somali Shilling', 'Somalia (except  Somaliland)');
insert into CURRENCY_LIST values(0, 'SRD', '968', 'Surinamese Dollar', 'Suriname');
insert into CURRENCY_LIST values(0, 'SSP', '728', 'South Sudanese Pound', 'South Sudan');
insert into CURRENCY_LIST values(0, 'STD', '678', 'Sao Tome and Principe Dobra', 'Sao Tome and Principe');
insert into CURRENCY_LIST values(0, 'SYP', '760', 'Syrian Pound', 'Syria');
insert into CURRENCY_LIST values(0, 'SZL', '748', 'Swazi Lilangeni', 'Swaziland');
insert into CURRENCY_LIST values(0, 'THB', '764', 'Thai Baht', 'Thailand');
insert into CURRENCY_LIST values(0, 'TJS', '972', 'Tajikistani Somoni', 'Tajikistan');
insert into CURRENCY_LIST values(0, 'TMT', '934', 'Turkmenistani Manat', 'Turkmenistan');
insert into CURRENCY_LIST values(0, 'TND', '788', 'Tunisian Dinar', 'Tunisia');
insert into CURRENCY_LIST values(0, 'TOP', '776', 'Tongan Pa?anga', 'Tonga');
insert into CURRENCY_LIST values(0, 'TRY', '949', 'Turkish Lira', 'Turkey,  Northern Cyprus');
insert into CURRENCY_LIST values(0, 'TTD', '780', 'Trinidad and Tobago Dollar', 'Trinidad and Tobago');
insert into CURRENCY_LIST values(0, 'TWD', '901', 'New Taiwan Dollar', 'Taiwan');
insert into CURRENCY_LIST values(0, 'TZS', '834', 'Tanzanian Shilling', 'Tanzania');
insert into CURRENCY_LIST values(0, 'UAH', '980', 'Ukrainian Hryvnia', 'Ukraine');
insert into CURRENCY_LIST values(0, 'UGX', '800', 'Ugandan Shilling', 'Uganda');
insert into CURRENCY_LIST values(0, 'USD', '840', 'United States Dollar', 'United States,  American Samoa (AS),  Barbados (BB) (as well as Barbados Dollar),  Bermuda (BM) (as well as Bermudian Dollar),  British Indian Ocean Territory (IO) (also uses GBP),  British Virgin Islands (VG), Caribbean Netherlands (BQ - Bonaire, Sint Eustatius and Saba),  Ecuador (EC),  El Salvador (SV),  Guam (GU),  Haiti (HT),  Marshall Islands (MH),  Federated States of Micronesia (FM),  Northern Mariana Islands (MP),  Palau (PW),  Panama (PA),  Puerto Rico (PR),  Timor-Leste (TL),  Turks and Caicos Islands (TC),  U.S. Virgin Islands (VI),  Zimbabwe (ZW)');
insert into CURRENCY_LIST values(0, 'UYU', '858', 'Uruguayan Peso', 'Uruguay');
insert into CURRENCY_LIST values(0, 'UZS', '860', 'Uzbekistan Som', 'Uzbekistan');
insert into CURRENCY_LIST values(0, 'VEF', '937', 'Venezuelan Bolivar', 'Venezuela');
insert into CURRENCY_LIST values(0, 'VND', '704', 'Vietnamese Dong', 'Vietnam');
insert into CURRENCY_LIST values(0, 'VUV', '548', 'Vanuatu Vatu', 'Vanuatu');
insert into CURRENCY_LIST values(0, 'WST', '882', 'Samoan Tala', 'Samoa');
insert into CURRENCY_LIST values(0, 'XAF', '950', 'CFA Franc BEAC', 'Cameroon (CM),  Central African Republic (CF),  Republic of the Congo (CG),  Chad (TD),  Equatorial Guinea (GQ),  Gabon (GA)');
insert into CURRENCY_LIST values(0, 'XCD', '951', 'East Caribbean Dollar', 'Anguilla (AI),  Antigua and Barbuda (AG),  Dominica (DM),  Grenada (GD),  Montserrat (MS),  Saint Kitts and Nevis (KN),  Saint Lucia (LC),  Saint Vincent and the Grenadines (VC)');
insert into CURRENCY_LIST values(0, 'XDR', '960', 'Special Drawing Rights','International Monetary Fund');
insert into CURRENCY_LIST values(0, 'XOF', '952', 'CFA Franc BCEAO', 'Benin (BJ),  Burkina Faso (BF),  Cote dIvoire (CI),  Guinea-Bissau (GW),  Mali (ML),  Niger (NE),  Senegal (SN),  Togo (TG)');
insert into CURRENCY_LIST values(0, 'XPF', '953', 'CFP Franc (Franc Pacifique)', 'French territories of the Pacific Ocean:  French Polynesia (PF),  New Caledonia (NC),  Wallis and Futuna (WF)');
insert into CURRENCY_LIST values(0, 'YER', '886', 'Yemeni Rial', 'Yemen');
insert into CURRENCY_LIST values(0, 'ZAR', '710', 'South African Rand', 'South Africa');
insert into CURRENCY_LIST values(0, 'ZMW', '967', 'Zambian Kwacha', 'Zambia');
insert into CURRENCY_LIST values(0, 'ZWD', '932', 'Zimbabwe Dollar', 'Zimbabwe');


insert into COUNTRY_LIST values (1, 'AF', 'AFGHANISTAN', 'Afghanistan', 'AFG', 4, 93);
insert into COUNTRY_LIST values (2, 'AL', 'ALBANIA', 'Albania', 'ALB', 8, 355);
insert into COUNTRY_LIST values (3, 'DZ', 'ALGERIA', 'Algeria', 'DZA', 12, 213);
insert into COUNTRY_LIST values (4, 'AS', 'AMERICAN SAMOA', 'American Samoa', 'ASM', 16, 1684);
insert into COUNTRY_LIST values (5, 'AD', 'ANDORRA', 'Andorra', 'AND', 20, 376);
insert into COUNTRY_LIST values (6, 'AO', 'ANGOLA', 'Angola', 'AGO', 24, 244);
insert into COUNTRY_LIST values (7, 'AI', 'ANGUILLA', 'Anguilla', 'AIA', 660, 1264);
insert into COUNTRY_LIST values (8, 'AQ', 'ANTARCTICA', 'Antarctica', NULL, NULL, 0);
insert into COUNTRY_LIST values (9, 'AG', 'ANTIGUA AND BARBUDA', 'Antigua and Barbuda', 'ATG', 28, 1268);
insert into COUNTRY_LIST values (10, 'AR', 'ARGENTINA', 'Argentina', 'ARG', 32, 54);
insert into COUNTRY_LIST values (11, 'AM', 'ARMENIA', 'Armenia', 'ARM', 51, 374);
insert into COUNTRY_LIST values (12, 'AW', 'ARUBA', 'Aruba', 'ABW', 533, 297);
insert into COUNTRY_LIST values (13, 'AU', 'AUSTRALIA', 'Australia', 'AUS', 36, 61);
insert into COUNTRY_LIST values (14, 'AT', 'AUSTRIA', 'Austria', 'AUT', 40, 43);
insert into COUNTRY_LIST values (15, 'AZ', 'AZERBAIJAN', 'Azerbaijan', 'AZE', 31, 994);
insert into COUNTRY_LIST values (16, 'BS', 'BAHAMAS', 'Bahamas', 'BHS', 44, 1242);
insert into COUNTRY_LIST values (17, 'BH', 'BAHRAIN', 'Bahrain', 'BHR', 48, 973);
insert into COUNTRY_LIST values (18, 'BD', 'BANGLADESH', 'Bangladesh', 'BGD', 50, 880);
insert into COUNTRY_LIST values (19, 'BB', 'BARBADOS', 'Barbados', 'BRB', 52, 1246);
insert into COUNTRY_LIST values (20, 'BY', 'BELARUS', 'Belarus', 'BLR', 112, 375);
insert into COUNTRY_LIST values (21, 'BE', 'BELGIUM', 'Belgium', 'BEL', 56, 32);
insert into COUNTRY_LIST values (22, 'BZ', 'BELIZE', 'Belize', 'BLZ', 84, 501);
insert into COUNTRY_LIST values (23, 'BJ', 'BENIN', 'Benin', 'BEN', 204, 229);
insert into COUNTRY_LIST values (24, 'BM', 'BERMUDA', 'Bermuda', 'BMU', 60, 1441);
insert into COUNTRY_LIST values (25, 'BT', 'BHUTAN', 'Bhutan', 'BTN', 64, 975);
insert into COUNTRY_LIST values (26, 'BO', 'BOLIVIA', 'Bolivia', 'BOL', 68, 591);
insert into COUNTRY_LIST values (27, 'BA', 'BOSNIA AND HERZEGOVINA', 'Bosnia and Herzegovina', 'BIH', 70, 387);
insert into COUNTRY_LIST values (28, 'BW', 'BOTSWANA', 'Botswana', 'BWA', 72, 267);
insert into COUNTRY_LIST values (29, 'BV', 'BOUVET ISLAND', 'Bouvet Island', NULL, NULL, 0);
insert into COUNTRY_LIST values (30, 'BR', 'BRAZIL', 'Brazil', 'BRA', 76, 55);
insert into COUNTRY_LIST values (31, 'IO', 'BRITISH INDIAN OCEAN TERRITORY', 'British Indian Ocean Territory', NULL, NULL, 246);
insert into COUNTRY_LIST values (32, 'BN', 'BRUNEI DARUSSALAM', 'Brunei Darussalam', 'BRN', 96, 673);
insert into COUNTRY_LIST values (33, 'BG', 'BULGARIA', 'Bulgaria', 'BGR', 100, 359);
insert into COUNTRY_LIST values (34, 'BF', 'BURKINA FASO', 'Burkina Faso', 'BFA', 854, 226);
insert into COUNTRY_LIST values (35, 'BI', 'BURUNDI', 'Burundi', 'BDI', 108, 257);
insert into COUNTRY_LIST values (36, 'KH', 'CAMBODIA', 'Cambodia', 'KHM', 116, 855);
insert into COUNTRY_LIST values (37, 'CM', 'CAMEROON', 'Cameroon', 'CMR', 120, 237);
insert into COUNTRY_LIST values (38, 'CA', 'CANADA', 'Canada', 'CAN', 124, 1);
insert into COUNTRY_LIST values (39, 'CV', 'CAPE VERDE', 'Cape Verde', 'CPV', 132, 238);
insert into COUNTRY_LIST values (40, 'KY', 'CAYMAN ISLANDS', 'Cayman Islands', 'CYM', 136, 1345);
insert into COUNTRY_LIST values (41, 'CF', 'CENTRAL AFRICAN REPUBLIC', 'Central African Republic', 'CAF', 140, 236);
insert into COUNTRY_LIST values (42, 'TD', 'CHAD', 'Chad', 'TCD', 148, 235);
insert into COUNTRY_LIST values (43, 'CL', 'CHILE', 'Chile', 'CHL', 152, 56);
insert into COUNTRY_LIST values (44, 'CN', 'CHINA', 'China', 'CHN', 156, 86);
insert into COUNTRY_LIST values (45, 'CX', 'CHRISTMAS ISLAND', 'Christmas Island', NULL, NULL, 61);
insert into COUNTRY_LIST values (46, 'CC', 'COCOS (KEELING) ISLANDS', 'Cocos (Keeling) Islands', NULL, NULL, 672);
insert into COUNTRY_LIST values (47, 'CO', 'COLOMBIA', 'Colombia', 'COL', 170, 57);
insert into COUNTRY_LIST values (48, 'KM', 'COMOROS', 'Comoros', 'COM', 174, 269);
insert into COUNTRY_LIST values (49, 'CG', 'CONGO', 'Congo', 'COG', 178, 242);
insert into COUNTRY_LIST values (50, 'CD', 'CONGO, THE DEMOCRATIC REPUBLIC OF THE', 'Congo, the Democratic Republic of the', 'COD', 180, 242);
insert into COUNTRY_LIST values (51, 'CK', 'COOK ISLANDS', 'Cook Islands', 'COK', 184, 682);
insert into COUNTRY_LIST values (52, 'CR', 'COSTA RICA', 'Costa Rica', 'CRI', 188, 506);
insert into COUNTRY_LIST values (53, 'CI', 'COTE D''IVOIRE', 'Cote D''Ivoire', 'CIV', 384, 225);
insert into COUNTRY_LIST values (54, 'HR', 'CROATIA', 'Croatia', 'HRV', 191, 385);
insert into COUNTRY_LIST values (55, 'CU', 'CUBA', 'Cuba', 'CUB', 192, 53);
insert into COUNTRY_LIST values (56, 'CY', 'CYPRUS', 'Cyprus', 'CYP', 196, 357);
insert into COUNTRY_LIST values (57, 'CZ', 'CZECH REPUBLIC', 'Czech Republic', 'CZE', 203, 420);
insert into COUNTRY_LIST values (58, 'DK', 'DENMARK', 'Denmark', 'DNK', 208, 45);
insert into COUNTRY_LIST values (59, 'DJ', 'DJIBOUTI', 'Djibouti', 'DJI', 262, 253);
insert into COUNTRY_LIST values (60, 'DM', 'DOMINICA', 'Dominica', 'DMA', 212, 1767);
insert into COUNTRY_LIST values (61, 'DO', 'DOMINICAN REPUBLIC', 'Dominican Republic', 'DOM', 214, 1809);
insert into COUNTRY_LIST values (62, 'EC', 'ECUADOR', 'Ecuador', 'ECU', 218, 593);
insert into COUNTRY_LIST values (63, 'EG', 'EGYPT', 'Egypt', 'EGY', 818, 20);
insert into COUNTRY_LIST values (64, 'SV', 'EL SALVADOR', 'El Salvador', 'SLV', 222, 503);
insert into COUNTRY_LIST values (65, 'GQ', 'EQUATORIAL GUINEA', 'Equatorial Guinea', 'GNQ', 226, 240);
insert into COUNTRY_LIST values (66, 'ER', 'ERITREA', 'Eritrea', 'ERI', 232, 291);
insert into COUNTRY_LIST values (67, 'EE', 'ESTONIA', 'Estonia', 'EST', 233, 372);
insert into COUNTRY_LIST values (68, 'ET', 'ETHIOPIA', 'Ethiopia', 'ETH', 231, 251);
insert into COUNTRY_LIST values (69, 'FK', 'FALKLAND ISLANDS (MALVINAS)', 'Falkland Islands (Malvinas)', 'FLK', 238, 500);
insert into COUNTRY_LIST values (70, 'FO', 'FAROE ISLANDS', 'Faroe Islands', 'FRO', 234, 298);
insert into COUNTRY_LIST values (71, 'FJ', 'FIJI', 'Fiji', 'FJI', 242, 679);
insert into COUNTRY_LIST values (72, 'FI', 'FINLAND', 'Finland', 'FIN', 246, 358);
insert into COUNTRY_LIST values (73, 'FR', 'FRANCE', 'France', 'FRA', 250, 33);
insert into COUNTRY_LIST values (74, 'GF', 'FRENCH GUIANA', 'French Guiana', 'GUF', 254, 594);
insert into COUNTRY_LIST values (75, 'PF', 'FRENCH POLYNESIA', 'French Polynesia', 'PYF', 258, 689);
insert into COUNTRY_LIST values (76, 'TF', 'FRENCH SOUTHERN TERRITORIES', 'French Southern Territories', NULL, NULL, 0);
insert into COUNTRY_LIST values (77, 'GA', 'GABON', 'Gabon', 'GAB', 266, 241);
insert into COUNTRY_LIST values (78, 'GM', 'GAMBIA', 'Gambia', 'GMB', 270, 220);
insert into COUNTRY_LIST values (79, 'GE', 'GEORGIA', 'Georgia', 'GEO', 268, 995);
insert into COUNTRY_LIST values (80, 'DE', 'GERMANY', 'Germany', 'DEU', 276, 49);
insert into COUNTRY_LIST values (81, 'GH', 'GHANA', 'Ghana', 'GHA', 288, 233);
insert into COUNTRY_LIST values (82, 'GI', 'GIBRALTAR', 'Gibraltar', 'GIB', 292, 350);
insert into COUNTRY_LIST values (83, 'GR', 'GREECE', 'Greece', 'GRC', 300, 30);
insert into COUNTRY_LIST values (84, 'GL', 'GREENLAND', 'Greenland', 'GRL', 304, 299);
insert into COUNTRY_LIST values (85, 'GD', 'GRENADA', 'Grenada', 'GRD', 308, 1473);
insert into COUNTRY_LIST values (86, 'GP', 'GUADELOUPE', 'Guadeloupe', 'GLP', 312, 590);
insert into COUNTRY_LIST values (87, 'GU', 'GUAM', 'Guam', 'GUM', 316, 1671);
insert into COUNTRY_LIST values (88, 'GT', 'GUATEMALA', 'Guatemala', 'GTM', 320, 502);
insert into COUNTRY_LIST values (89, 'GN', 'GUINEA', 'Guinea', 'GIN', 324, 224);
insert into COUNTRY_LIST values (90, 'GW', 'GUINEA-BISSAU', 'Guinea-Bissau', 'GNB', 624, 245);
insert into COUNTRY_LIST values (91, 'GY', 'GUYANA', 'Guyana', 'GUY', 328, 592);
insert into COUNTRY_LIST values (92, 'HT', 'HAITI', 'Haiti', 'HTI', 332, 509);
insert into COUNTRY_LIST values (93, 'HM', 'HEARD ISLAND AND MCDONALD ISLANDS', 'Heard Island and Mcdonald Islands', NULL, NULL, 0);
insert into COUNTRY_LIST values (94, 'VA', 'HOLY SEE (VATICAN CITY STATE)', 'Holy See (Vatican City State)', 'VAT', 336, 39);
insert into COUNTRY_LIST values (95, 'HN', 'HONDURAS', 'Honduras', 'HND', 340, 504);
insert into COUNTRY_LIST values (96, 'HK', 'HONG KONG', 'Hong Kong', 'HKG', 344, 852);
insert into COUNTRY_LIST values (97, 'HU', 'HUNGARY', 'Hungary', 'HUN', 348, 36);
insert into COUNTRY_LIST values (98, 'IS', 'ICELAND', 'Iceland', 'ISL', 352, 354);
insert into COUNTRY_LIST values (99, 'IN', 'INDIA', 'India', 'IND', 356, 91);
insert into COUNTRY_LIST values (100, 'ID', 'INDONESIA', 'Indonesia', 'IDN', 360, 62);
insert into COUNTRY_LIST values (101, 'IR', 'IRAN, ISLAMIC REPUBLIC OF', 'Iran, Islamic Republic of', 'IRN', 364, 98);
insert into COUNTRY_LIST values (102, 'IQ', 'IRAQ', 'Iraq', 'IRQ', 368, 964);
insert into COUNTRY_LIST values (103, 'IE', 'IRELAND', 'Ireland', 'IRL', 372, 353);
insert into COUNTRY_LIST values (104, 'IL', 'ISRAEL', 'Israel', 'ISR', 376, 972);
insert into COUNTRY_LIST values (105, 'IT', 'ITALY', 'Italy', 'ITA', 380, 39);
insert into COUNTRY_LIST values (106, 'JM', 'JAMAICA', 'Jamaica', 'JAM', 388, 1876);
insert into COUNTRY_LIST values (107, 'JP', 'JAPAN', 'Japan', 'JPN', 392, 81);
insert into COUNTRY_LIST values (108, 'JO', 'JORDAN', 'Jordan', 'JOR', 400, 962);
insert into COUNTRY_LIST values (109, 'KZ', 'KAZAKHSTAN', 'Kazakhstan', 'KAZ', 398, 7);
insert into COUNTRY_LIST values (110, 'KE', 'KENYA', 'Kenya', 'KEN', 404, 254);
insert into COUNTRY_LIST values (111, 'KI', 'KIRIBATI', 'Kiribati', 'KIR', 296, 686);
insert into COUNTRY_LIST values (112, 'KP', 'KOREA, DEMOCRATIC PEOPLE''S REPUBLIC OF', 'Korea, Democratic People''s Republic of', 'PRK', 408, 850);
insert into COUNTRY_LIST values (113, 'KR', 'KOREA, REPUBLIC OF', 'Korea, Republic of', 'KOR', 410, 82);
insert into COUNTRY_LIST values (114, 'KW', 'KUWAIT', 'Kuwait', 'KWT', 414, 965);
insert into COUNTRY_LIST values (115, 'KG', 'KYRGYZSTAN', 'Kyrgyzstan', 'KGZ', 417, 996);
insert into COUNTRY_LIST values (116, 'LA', 'LAO PEOPLE''S DEMOCRATIC REPUBLIC', 'Lao People''s Democratic Republic', 'LAO', 418, 856);
insert into COUNTRY_LIST values (117, 'LV', 'LATVIA', 'Latvia', 'LVA', 428, 371);
insert into COUNTRY_LIST values (118, 'LB', 'LEBANON', 'Lebanon', 'LBN', 422, 961);
insert into COUNTRY_LIST values (119, 'LS', 'LESOTHO', 'Lesotho', 'LSO', 426, 266);
insert into COUNTRY_LIST values (120, 'LR', 'LIBERIA', 'Liberia', 'LBR', 430, 231);
insert into COUNTRY_LIST values (121, 'LY', 'LIBYAN ARAB JAMAHIRIYA', 'Libyan Arab Jamahiriya', 'LBY', 434, 218);
insert into COUNTRY_LIST values (122, 'LI', 'LIECHTENSTEIN', 'Liechtenstein', 'LIE', 438, 423);
insert into COUNTRY_LIST values (123, 'LT', 'LITHUANIA', 'Lithuania', 'LTU', 440, 370);
insert into COUNTRY_LIST values (124, 'LU', 'LUXEMBOURG', 'Luxembourg', 'LUX', 442, 352);
insert into COUNTRY_LIST values (125, 'MO', 'MACAO', 'Macao', 'MAC', 446, 853);
insert into COUNTRY_LIST values (126, 'MK', 'MACEDONIA, THE FORMER YUGOSLAV REPUBLIC OF', 'Macedonia, the Former Yugoslav Republic of', 'MKD', 807, 389);
insert into COUNTRY_LIST values (127, 'MG', 'MADAGASCAR', 'Madagascar', 'MDG', 450, 261);
insert into COUNTRY_LIST values (128, 'MW', 'MALAWI', 'Malawi', 'MWI', 454, 265);
insert into COUNTRY_LIST values (129, 'MY', 'MALAYSIA', 'Malaysia', 'MYS', 458, 60);
insert into COUNTRY_LIST values (130, 'MV', 'MALDIVES', 'Maldives', 'MDV', 462, 960);
insert into COUNTRY_LIST values (131, 'ML', 'MALI', 'Mali', 'MLI', 466, 223);
insert into COUNTRY_LIST values (132, 'MT', 'MALTA', 'Malta', 'MLT', 470, 356);
insert into COUNTRY_LIST values (133, 'MH', 'MARSHALL ISLANDS', 'Marshall Islands', 'MHL', 584, 692);
insert into COUNTRY_LIST values (134, 'MQ', 'MARTINIQUE', 'Martinique', 'MTQ', 474, 596);
insert into COUNTRY_LIST values (135, 'MR', 'MAURITANIA', 'Mauritania', 'MRT', 478, 222);
insert into COUNTRY_LIST values (136, 'MU', 'MAURITIUS', 'Mauritius', 'MUS', 480, 230);
insert into COUNTRY_LIST values (137, 'YT', 'MAYOTTE', 'Mayotte', NULL, NULL, 269);
insert into COUNTRY_LIST values (138, 'MX', 'MEXICO', 'Mexico', 'MEX', 484, 52);
insert into COUNTRY_LIST values (139, 'FM', 'MICRONESIA, FEDERATED STATES OF', 'Micronesia, Federated States of', 'FSM', 583, 691);
insert into COUNTRY_LIST values (140, 'MD', 'MOLDOVA, REPUBLIC OF', 'Moldova, Republic of', 'MDA', 498, 373);
insert into COUNTRY_LIST values (141, 'MC', 'MONACO', 'Monaco', 'MCO', 492, 377);
insert into COUNTRY_LIST values (142, 'MN', 'MONGOLIA', 'Mongolia', 'MNG', 496, 976);
insert into COUNTRY_LIST values (143, 'MS', 'MONTSERRAT', 'Montserrat', 'MSR', 500, 1664);
insert into COUNTRY_LIST values (144, 'MA', 'MOROCCO', 'Morocco', 'MAR', 504, 212);
insert into COUNTRY_LIST values (145, 'MZ', 'MOZAMBIQUE', 'Mozambique', 'MOZ', 508, 258);
insert into COUNTRY_LIST values (146, 'MM', 'MYANMAR', 'Myanmar', 'MMR', 104, 95);
insert into COUNTRY_LIST values (147, 'NA', 'NAMIBIA', 'Namibia', 'NAM', 516, 264);
insert into COUNTRY_LIST values (148, 'NR', 'NAURU', 'Nauru', 'NRU', 520, 674);
insert into COUNTRY_LIST values (149, 'NP', 'NEPAL', 'Nepal', 'NPL', 524, 977);
insert into COUNTRY_LIST values (150, 'NL', 'NETHERLANDS', 'Netherlands', 'NLD', 528, 31);
insert into COUNTRY_LIST values (151, 'AN', 'NETHERLANDS ANTILLES', 'Netherlands Antilles', 'ANT', 530, 599);
insert into COUNTRY_LIST values (152, 'NC', 'NEW CALEDONIA', 'New Caledonia', 'NCL', 540, 687);
insert into COUNTRY_LIST values (153, 'NZ', 'NEW ZEALAND', 'New Zealand', 'NZL', 554, 64);
insert into COUNTRY_LIST values (154, 'NI', 'NICARAGUA', 'Nicaragua', 'NIC', 558, 505);
insert into COUNTRY_LIST values (155, 'NE', 'NIGER', 'Niger', 'NER', 562, 227);
insert into COUNTRY_LIST values (156, 'NG', 'NIGERIA', 'Nigeria', 'NGA', 566, 234);
insert into COUNTRY_LIST values (157, 'NU', 'NIUE', 'Niue', 'NIU', 570, 683);
insert into COUNTRY_LIST values (158, 'NF', 'NORFOLK ISLAND', 'Norfolk Island', 'NFK', 574, 672);
insert into COUNTRY_LIST values (159, 'MP', 'NORTHERN MARIANA ISLANDS', 'Northern Mariana Islands', 'MNP', 580, 1670);
insert into COUNTRY_LIST values (160, 'NO', 'NORWAY', 'Norway', 'NOR', 578, 47);
insert into COUNTRY_LIST values (161, 'OM', 'OMAN', 'Oman', 'OMN', 512, 968);
insert into COUNTRY_LIST values (162, 'PK', 'PAKISTAN', 'Pakistan', 'PAK', 586, 92);
insert into COUNTRY_LIST values (163, 'PW', 'PALAU', 'Palau', 'PLW', 585, 680);
insert into COUNTRY_LIST values (164, 'PS', 'PALESTINIAN TERRITORY, OCCUPIED', 'Palestinian Territory, Occupied', NULL, NULL, 970);
insert into COUNTRY_LIST values (165, 'PA', 'PANAMA', 'Panama', 'PAN', 591, 507);
insert into COUNTRY_LIST values (166, 'PG', 'PAPUA NEW GUINEA', 'Papua New Guinea', 'PNG', 598, 675);
insert into COUNTRY_LIST values (167, 'PY', 'PARAGUAY', 'Paraguay', 'PRY', 600, 595);
insert into COUNTRY_LIST values (168, 'PE', 'PERU', 'Peru', 'PER', 604, 51);
insert into COUNTRY_LIST values (169, 'PH', 'PHILIPPINES', 'Philippines', 'PHL', 608, 63);
insert into COUNTRY_LIST values (170, 'PN', 'PITCAIRN', 'Pitcairn', 'PCN', 612, 0);
insert into COUNTRY_LIST values (171, 'PL', 'POLAND', 'Poland', 'POL', 616, 48);
insert into COUNTRY_LIST values (172, 'PT', 'PORTUGAL', 'Portugal', 'PRT', 620, 351);
insert into COUNTRY_LIST values (173, 'PR', 'PUERTO RICO', 'Puerto Rico', 'PRI', 630, 1787);
insert into COUNTRY_LIST values (174, 'QA', 'QATAR', 'Qatar', 'QAT', 634, 974);
insert into COUNTRY_LIST values (175, 'RE', 'REUNION', 'Reunion', 'REU', 638, 262);
insert into COUNTRY_LIST values (176, 'RO', 'ROMANIA', 'Romania', 'ROM', 642, 40);
insert into COUNTRY_LIST values (177, 'RU', 'RUSSIAN FEDERATION', 'Russian Federation', 'RUS', 643, 70);
insert into COUNTRY_LIST values (178, 'RW', 'RWANDA', 'Rwanda', 'RWA', 646, 250);
insert into COUNTRY_LIST values (179, 'SH', 'SAINT HELENA', 'Saint Helena', 'SHN', 654, 290);
insert into COUNTRY_LIST values (180, 'KN', 'SAINT KITTS AND NEVIS', 'Saint Kitts and Nevis', 'KNA', 659, 1869);
insert into COUNTRY_LIST values (181, 'LC', 'SAINT LUCIA', 'Saint Lucia', 'LCA', 662, 1758);
insert into COUNTRY_LIST values (182, 'PM', 'SAINT PIERRE AND MIQUELON', 'Saint Pierre and Miquelon', 'SPM', 666, 508);
insert into COUNTRY_LIST values (183, 'VC', 'SAINT VINCENT AND THE GRENADINES', 'Saint Vincent and the Grenadines', 'VCT', 670, 1784);
insert into COUNTRY_LIST values (184, 'WS', 'SAMOA', 'Samoa', 'WSM', 882, 684);
insert into COUNTRY_LIST values (185, 'SM', 'SAN MARINO', 'San Marino', 'SMR', 674, 378);
insert into COUNTRY_LIST values (186, 'ST', 'SAO TOME AND PRINCIPE', 'Sao Tome and Principe', 'STP', 678, 239);
insert into COUNTRY_LIST values (187, 'SA', 'SAUDI ARABIA', 'Saudi Arabia', 'SAU', 682, 966);
insert into COUNTRY_LIST values (188, 'SN', 'SENEGAL', 'Senegal', 'SEN', 686, 221);
insert into COUNTRY_LIST values (189, 'CS', 'SERBIA AND MONTENEGRO', 'Serbia and Montenegro', NULL, NULL, 381);
insert into COUNTRY_LIST values (190, 'SC', 'SEYCHELLES', 'Seychelles', 'SYC', 690, 248);
insert into COUNTRY_LIST values (191, 'SL', 'SIERRA LEONE', 'Sierra Leone', 'SLE', 694, 232);
insert into COUNTRY_LIST values (192, 'SG', 'SINGAPORE', 'Singapore', 'SGP', 702, 65);
insert into COUNTRY_LIST values (193, 'SK', 'SLOVAKIA', 'Slovakia', 'SVK', 703, 421);
insert into COUNTRY_LIST values (194, 'SI', 'SLOVENIA', 'Slovenia', 'SVN', 705, 386);
insert into COUNTRY_LIST values (195, 'SB', 'SOLOMON ISLANDS', 'Solomon Islands', 'SLB', 90, 677);
insert into COUNTRY_LIST values (196, 'SO', 'SOMALIA', 'Somalia', 'SOM', 706, 252);
insert into COUNTRY_LIST values (197, 'ZA', 'SOUTH AFRICA', 'South Africa', 'ZAF', 710, 27);
insert into COUNTRY_LIST values (198, 'GS', 'SOUTH GEORGIA AND THE SOUTH SANDWICH ISLANDS', 'South Georgia and the South Sandwich Islands', NULL, NULL, 0);
insert into COUNTRY_LIST values (199, 'ES', 'SPAIN', 'Spain', 'ESP', 724, 34);
insert into COUNTRY_LIST values (200, 'LK', 'SRI LANKA', 'Sri Lanka', 'LKA', 144, 94);
insert into COUNTRY_LIST values (201, 'SD', 'SUDAN', 'Sudan', 'SDN', 736, 249);
insert into COUNTRY_LIST values (202, 'SR', 'SURINAME', 'Suriname', 'SUR', 740, 597);
insert into COUNTRY_LIST values (203, 'SJ', 'SVALBARD AND JAN MAYEN', 'Svalbard and Jan Mayen', 'SJM', 744, 47);
insert into COUNTRY_LIST values (204, 'SZ', 'SWAZILAND', 'Swaziland', 'SWZ', 748, 268);
insert into COUNTRY_LIST values (205, 'SE', 'SWEDEN', 'Sweden', 'SWE', 752, 46);
insert into COUNTRY_LIST values (206, 'CH', 'SWITZERLAND', 'Switzerland', 'CHE', 756, 41);
insert into COUNTRY_LIST values (207, 'SY', 'SYRIAN ARAB REPUBLIC', 'Syrian Arab Republic', 'SYR', 760, 963);
insert into COUNTRY_LIST values (208, 'TW', 'TAIWAN, PROVINCE OF CHINA', 'Taiwan, Province of China', 'TWN', 158, 886);
insert into COUNTRY_LIST values (209, 'TJ', 'TAJIKISTAN', 'Tajikistan', 'TJK', 762, 992);
insert into COUNTRY_LIST values (210, 'TZ', 'TANZANIA, UNITED REPUBLIC OF', 'Tanzania, United Republic of', 'TZA', 834, 255);
insert into COUNTRY_LIST values (211, 'TH', 'THAILAND', 'Thailand', 'THA', 764, 66);
insert into COUNTRY_LIST values (212, 'TL', 'TIMOR-LESTE', 'Timor-Leste', NULL, NULL, 670);
insert into COUNTRY_LIST values (213, 'TG', 'TOGO', 'Togo', 'TGO', 768, 228);
insert into COUNTRY_LIST values (214, 'TK', 'TOKELAU', 'Tokelau', 'TKL', 772, 690);
insert into COUNTRY_LIST values (215, 'TO', 'TONGA', 'Tonga', 'TON', 776, 676);
insert into COUNTRY_LIST values (216, 'TT', 'TRINIDAD AND TOBAGO', 'Trinidad and Tobago', 'TTO', 780, 1868);
insert into COUNTRY_LIST values (217, 'TN', 'TUNISIA', 'Tunisia', 'TUN', 788, 216);
insert into COUNTRY_LIST values (218, 'TR', 'TURKEY', 'Turkey', 'TUR', 792, 90);
insert into COUNTRY_LIST values (219, 'TM', 'TURKMENISTAN', 'Turkmenistan', 'TKM', 795, 7370);
insert into COUNTRY_LIST values (220, 'TC', 'TURKS AND CAICOS ISLANDS', 'Turks and Caicos Islands', 'TCA', 796, 1649);
insert into COUNTRY_LIST values (221, 'TV', 'TUVALU', 'Tuvalu', 'TUV', 798, 688);
insert into COUNTRY_LIST values (222, 'UG', 'UGANDA', 'Uganda', 'UGA', 800, 256);
insert into COUNTRY_LIST values (223, 'UA', 'UKRAINE', 'Ukraine', 'UKR', 804, 380);
insert into COUNTRY_LIST values (224, 'AE', 'UNITED ARAB EMIRATES', 'United Arab Emirates', 'ARE', 784, 971);
insert into COUNTRY_LIST values (225, 'GB', 'UNITED KINGDOM', 'United Kingdom', 'GBR', 826, 44);
insert into COUNTRY_LIST values (226, 'US', 'UNITED STATES', 'United States', 'USA', 840, 1);
insert into COUNTRY_LIST values (227, 'UM', 'UNITED STATES MINOR OUTLYING ISLANDS', 'United States Minor Outlying Islands', NULL, NULL, 1);
insert into COUNTRY_LIST values (228, 'UY', 'URUGUAY', 'Uruguay', 'URY', 858, 598);
insert into COUNTRY_LIST values (229, 'UZ', 'UZBEKISTAN', 'Uzbekistan', 'UZB', 860, 998);
insert into COUNTRY_LIST values (230, 'VU', 'VANUATU', 'Vanuatu', 'VUT', 548, 678);
insert into COUNTRY_LIST values (231, 'VE', 'VENEZUELA', 'Venezuela', 'VEN', 862, 58);
insert into COUNTRY_LIST values (232, 'VN', 'VIET NAM', 'Viet Nam', 'VNM', 704, 84);
insert into COUNTRY_LIST values (233, 'VG', 'VIRGIN ISLANDS, BRITISH', 'Virgin Islands, British', 'VGB', 92, 1284);
insert into COUNTRY_LIST values (234, 'VI', 'VIRGIN ISLANDS, U.S.', 'Virgin Islands, U.s.', 'VIR', 850, 1340);
insert into COUNTRY_LIST values (235, 'WF', 'WALLIS AND FUTUNA', 'Wallis and Futuna', 'WLF', 876, 681);
insert into COUNTRY_LIST values (236, 'EH', 'WESTERN SAHARA', 'Western Sahara', 'ESH', 732, 212);
insert into COUNTRY_LIST values (237, 'YE', 'YEMEN', 'Yemen', 'YEM', 887, 967);
insert into COUNTRY_LIST values (238, 'ZM', 'ZAMBIA', 'Zambia', 'ZMB', 894, 260);
insert into COUNTRY_LIST values (239, 'ZW', 'ZIMBABWE', 'Zimbabwe', 'ZWE', 716, 263);
insert into COUNTRY_LIST values (240, 'RS', 'SERBIA', 'Serbia', 'SRB', 688, 381);
insert into COUNTRY_LIST values (241, 'AP', 'ASIA PACIFIC REGION', 'Asia / Pacific Region', '0', 0, 0);
insert into COUNTRY_LIST values (242, 'ME', 'MONTENEGRO', 'Montenegro', 'MNE', 499, 382);
insert into COUNTRY_LIST values (243, 'AX', 'ALAND ISLANDS', 'Aland Islands', 'ALA', 248, 358);
insert into COUNTRY_LIST values (244, 'BQ', 'BONAIRE, SINT EUSTATIUS AND SABA', 'Bonaire, Sint Eustatius and Saba', 'BES', 535, 599);
insert into COUNTRY_LIST values (245, 'CW', 'CURACAO', 'Curacao', 'CUW', 531, 599);
insert into COUNTRY_LIST values (246, 'GG', 'GUERNSEY', 'Guernsey', 'GGY', 831, 44);
insert into COUNTRY_LIST values (247, 'IM', 'ISLE OF MAN', 'Isle of Man', 'IMN', 833, 44);
insert into COUNTRY_LIST values (248, 'JE', 'JERSEY', 'Jersey', 'JEY', 832, 44);
insert into COUNTRY_LIST values (249, 'XK', 'KOSOVO', 'Kosovo', '---', 0, 381);
insert into COUNTRY_LIST values (250, 'BL', 'SAINT BARTHELEMY', 'Saint Barthelemy', 'BLM', 652, 590);
insert into COUNTRY_LIST values (251, 'MF', 'SAINT MARTIN', 'Saint Martin', 'MAF', 663, 590);
insert into COUNTRY_LIST values (252, 'SX', 'SINT MAARTEN', 'Sint Maarten', 'SXM', 534, 1);
insert into COUNTRY_LIST values (253, 'SS', 'SOUTH SUDAN', 'South Sudan', 'SSD', 728, 211);


insert into USER_ROLES (ROLE_ID, ROLE_DESCRIPTION) values (1,'Church Admin');


insert into LICENSE_PLANS (PLAN_ID, PLAN_NAME, PLAN_DESCRIPTION, PLAN_TYPE, MAX_COUNT, PRICING, VALIDITY_IN_SECONDS, VALIDITY_IN_DAYS) values (1, 'Upto 200 Profiles - Monthly', 'Upto 200 Profiles - Monthly Plan', 1, 200, 12, 2592000, 30);
insert into LICENSE_PLANS (PLAN_ID, PLAN_NAME, PLAN_DESCRIPTION, PLAN_TYPE, MAX_COUNT, PRICING, VALIDITY_IN_SECONDS, VALIDITY_IN_DAYS) values (2, 'Upto 200 Profiles - Yearly', 'Upto 200 Profiles - Yearly Plan', 1, 200, 120, 31536000, 365);
insert into LICENSE_PLANS (PLAN_ID, PLAN_NAME, PLAN_DESCRIPTION, PLAN_TYPE, MAX_COUNT, PRICING, VALIDITY_IN_SECONDS, VALIDITY_IN_DAYS) values (3, 'Upto 600 Profiles - Monthly', 'Upto 600 Profiles - Monthly Plan', 1, 600, 25, 2592000, 30);
insert into LICENSE_PLANS (PLAN_ID, PLAN_NAME, PLAN_DESCRIPTION, PLAN_TYPE, MAX_COUNT, PRICING, VALIDITY_IN_SECONDS, VALIDITY_IN_DAYS) values (4, 'Upto 600 Profiles - Yearly', 'Upto 600 Profiles - Yearly Plan', 1, 600, 252, 31536000, 365);
insert into LICENSE_PLANS (PLAN_ID, PLAN_NAME, PLAN_DESCRIPTION, PLAN_TYPE, MAX_COUNT, PRICING, VALIDITY_IN_SECONDS, VALIDITY_IN_DAYS) values (5, 'Upto 1200 Profiles - Monthly', 'Upto 1200 Profiles - Monthly Plan', 1, 1200, 38, 2592000, 30);
insert into LICENSE_PLANS (PLAN_ID, PLAN_NAME, PLAN_DESCRIPTION, PLAN_TYPE, MAX_COUNT, PRICING, VALIDITY_IN_SECONDS, VALIDITY_IN_DAYS) values (6, 'Upto 1200 Profiles - Yearly', 'Upto 1200 Profiles - Yearly Plan', 1, 1200, 384, 31536000, 365);
insert into LICENSE_PLANS (PLAN_ID, PLAN_NAME, PLAN_DESCRIPTION, PLAN_TYPE, MAX_COUNT, PRICING, VALIDITY_IN_SECONDS, VALIDITY_IN_DAYS) values (7, 'Upto 2000 Profiles - Monthly', 'Upto 2000 Profiles - Monthly Plan', 1, 2000, 50, 2592000, 30);
insert into LICENSE_PLANS (PLAN_ID, PLAN_NAME, PLAN_DESCRIPTION, PLAN_TYPE, MAX_COUNT, PRICING, VALIDITY_IN_SECONDS, VALIDITY_IN_DAYS) values (8, 'Upto 2000 Profiles - Yearly', 'Upto 2000 Profiles - Yearly Plan', 1, 2000, 504, 31536000, 365);
insert into LICENSE_PLANS (PLAN_ID, PLAN_NAME, PLAN_DESCRIPTION, PLAN_TYPE, MAX_COUNT, PRICING, VALIDITY_IN_SECONDS, VALIDITY_IN_DAYS) values (9, 'Upto 2600 Profiles - Monthly', 'Upto 2600 Profiles - Monthly Plan', 1, 2600, 60, 2592000, 30);
insert into LICENSE_PLANS (PLAN_ID, PLAN_NAME, PLAN_DESCRIPTION, PLAN_TYPE, MAX_COUNT, PRICING, VALIDITY_IN_SECONDS, VALIDITY_IN_DAYS) values (10, 'Upto 2600 Profiles - Yearly', 'Upto 2600 Profiles - Yearly Plan', 1, 2600, 600, 31536000, 365);
insert into LICENSE_PLANS (PLAN_ID, PLAN_NAME, PLAN_DESCRIPTION, PLAN_TYPE, MAX_COUNT, PRICING, VALIDITY_IN_SECONDS, VALIDITY_IN_DAYS) values (11, 'Upto 3200 Profiles - Monthly', 'Upto 3200 Profiles - Monthly Plan', 1, 3200, 70, 2592000, 30);
insert into LICENSE_PLANS (PLAN_ID, PLAN_NAME, PLAN_DESCRIPTION, PLAN_TYPE, MAX_COUNT, PRICING, VALIDITY_IN_SECONDS, VALIDITY_IN_DAYS) values (12, 'Upto 3200 Profiles - Yearly', 'Upto 3200 Profiles - Yearly Plan', 1, 3200, 708, 31536000, 365);
insert into LICENSE_PLANS (PLAN_ID, PLAN_NAME, PLAN_DESCRIPTION, PLAN_TYPE, MAX_COUNT, PRICING, VALIDITY_IN_SECONDS, VALIDITY_IN_DAYS) values (13, 'Upto 3800 Profiles - Monthly', 'Upto 3800 Profiles - Monthly Plan', 1, 3800, 80, 2592000, 30);
insert into LICENSE_PLANS (PLAN_ID, PLAN_NAME, PLAN_DESCRIPTION, PLAN_TYPE, MAX_COUNT, PRICING, VALIDITY_IN_SECONDS, VALIDITY_IN_DAYS) values (14, 'Upto 3800 Profiles - Yearly', 'Upto 3800 Profiles - Yearly Plan', 1, 3800, 804, 31536000, 365);
insert into LICENSE_PLANS (PLAN_ID, PLAN_NAME, PLAN_DESCRIPTION, PLAN_TYPE, MAX_COUNT, PRICING, VALIDITY_IN_SECONDS, VALIDITY_IN_DAYS) values (15, 'Upto 4400 Profiles - Monthly', 'Upto 4400 Profiles - Monthly Plan', 1, 4400, 90, 2592000, 30);
insert into LICENSE_PLANS (PLAN_ID, PLAN_NAME, PLAN_DESCRIPTION, PLAN_TYPE, MAX_COUNT, PRICING, VALIDITY_IN_SECONDS, VALIDITY_IN_DAYS) values (16, 'Upto 4400 Profiles - Yearly', 'Upto 4400 Profiles - Yearly Plan', 1, 4400, 900, 31536000, 365);
insert into LICENSE_PLANS (PLAN_ID, PLAN_NAME, PLAN_DESCRIPTION, PLAN_TYPE, MAX_COUNT, PRICING, VALIDITY_IN_SECONDS, VALIDITY_IN_DAYS) values (17, 'Upto 5000 Profiles - Monthly', 'Upto 5000 Profiles - Monthly Plan', 1, 5000, 100, 2592000, 30);
insert into LICENSE_PLANS (PLAN_ID, PLAN_NAME, PLAN_DESCRIPTION, PLAN_TYPE, MAX_COUNT, PRICING, VALIDITY_IN_SECONDS, VALIDITY_IN_DAYS) values (18, 'Upto 5000 Profiles - Yearly', 'Upto 5000 Profiles - Yearly Plan', 1, 5000, 1008, 31536000, 365);
insert into LICENSE_PLANS (PLAN_ID, PLAN_NAME, PLAN_DESCRIPTION, PLAN_TYPE, MAX_COUNT, PRICING, VALIDITY_IN_SECONDS, VALIDITY_IN_DAYS) values (19, 'Upto 5600 Profiles - Monthly', 'Upto 5600 Profiles - Monthly Plan', 1, 5600, 110, 2592000, 30);
insert into LICENSE_PLANS (PLAN_ID, PLAN_NAME, PLAN_DESCRIPTION, PLAN_TYPE, MAX_COUNT, PRICING, VALIDITY_IN_SECONDS, VALIDITY_IN_DAYS) values (20, 'Upto 5600 Profiles - Yearly', 'Upto 5600 Profiles - Yearly Plan', 1, 5600, 1104, 31536000, 365);
insert into LICENSE_PLANS (PLAN_ID, PLAN_NAME, PLAN_DESCRIPTION, PLAN_TYPE, MAX_COUNT, PRICING, VALIDITY_IN_SECONDS, VALIDITY_IN_DAYS) values (21, 'Upto 6200 Profiles - Monthly', 'Upto 6200 Profiles - Monthly Plan', 1, 6200, 120, 2592000, 30);
insert into LICENSE_PLANS (PLAN_ID, PLAN_NAME, PLAN_DESCRIPTION, PLAN_TYPE, MAX_COUNT, PRICING, VALIDITY_IN_SECONDS, VALIDITY_IN_DAYS) values (22, 'Upto 6200 Profiles - Yearly', 'Upto 6200 Profiles - Yearly Plan', 1, 6200, 1200, 31536000, 365);
insert into LICENSE_PLANS (PLAN_ID, PLAN_NAME, PLAN_DESCRIPTION, PLAN_TYPE, MAX_COUNT, PRICING, VALIDITY_IN_SECONDS, VALIDITY_IN_DAYS) values (23, 'Upto 6800 Profiles - Monthly', 'Upto 6800 Profiles - Monthly Plan', 1, 6800, 130, 2592000, 30);
insert into LICENSE_PLANS (PLAN_ID, PLAN_NAME, PLAN_DESCRIPTION, PLAN_TYPE, MAX_COUNT, PRICING, VALIDITY_IN_SECONDS, VALIDITY_IN_DAYS) values (24, 'Upto 6800 Profiles - Yearly', 'Upto 6800 Profiles - Yearly Plan', 1, 6800, 1308, 31536000, 365);
insert into LICENSE_PLANS (PLAN_ID, PLAN_NAME, PLAN_DESCRIPTION, PLAN_TYPE, MAX_COUNT, PRICING, VALIDITY_IN_SECONDS, VALIDITY_IN_DAYS) values (25, 'Upto 7400 Profiles - Monthly', 'Upto 7400 Profiles - Monthly Plan', 1, 7400, 140, 2592000, 30);
insert into LICENSE_PLANS (PLAN_ID, PLAN_NAME, PLAN_DESCRIPTION, PLAN_TYPE, MAX_COUNT, PRICING, VALIDITY_IN_SECONDS, VALIDITY_IN_DAYS) values (26, 'Upto 7400 Profiles - Yearly', 'Upto 7400 Profiles - Yearly Plan', 1, 7400, 1404, 31536000, 365);
insert into LICENSE_PLANS (PLAN_ID, PLAN_NAME, PLAN_DESCRIPTION, PLAN_TYPE, MAX_COUNT, PRICING, VALIDITY_IN_SECONDS, VALIDITY_IN_DAYS) values (27, 'Upto 8000 Profiles - Monthly', 'Upto 8000 Profiles - Monthly Plan', 1, 8000, 150, 2592000, 30);
insert into LICENSE_PLANS (PLAN_ID, PLAN_NAME, PLAN_DESCRIPTION, PLAN_TYPE, MAX_COUNT, PRICING, VALIDITY_IN_SECONDS, VALIDITY_IN_DAYS) values (28, 'Upto 8000 Profiles - Yearly', 'Upto 8000 Profiles - Yearly Plan', 1, 8000, 1500, 31536000, 365);
insert into LICENSE_PLANS (PLAN_ID, PLAN_NAME, PLAN_DESCRIPTION, PLAN_TYPE, MAX_COUNT, PRICING, VALIDITY_IN_SECONDS, VALIDITY_IN_DAYS) values (29, 'Upto 8600 Profiles - Monthly', 'Upto 8600 Profiles - Monthly Plan', 1, 8600, 160, 2592000, 30);
insert into LICENSE_PLANS (PLAN_ID, PLAN_NAME, PLAN_DESCRIPTION, PLAN_TYPE, MAX_COUNT, PRICING, VALIDITY_IN_SECONDS, VALIDITY_IN_DAYS) values (30, 'Upto 8600 Profiles - Yearly', 'Upto 8600 Profiles - Yearly Plan', 1, 8600, 1608, 31536000, 365);
insert into LICENSE_PLANS (PLAN_ID, PLAN_NAME, PLAN_DESCRIPTION, PLAN_TYPE, MAX_COUNT, PRICING, VALIDITY_IN_SECONDS, VALIDITY_IN_DAYS) values (31, 'Upto 9200 Profiles - Monthly', 'Upto 9200 Profiles - Monthly Plan', 1, 9200, 170, 2592000, 30);
insert into LICENSE_PLANS (PLAN_ID, PLAN_NAME, PLAN_DESCRIPTION, PLAN_TYPE, MAX_COUNT, PRICING, VALIDITY_IN_SECONDS, VALIDITY_IN_DAYS) values (32, 'Upto 9200 Profiles - Yearly', 'Upto 9200 Profiles - Yearly Plan', 1, 9200, 1704, 31536000, 365);
insert into LICENSE_PLANS (PLAN_ID, PLAN_NAME, PLAN_DESCRIPTION, PLAN_TYPE, MAX_COUNT, PRICING, VALIDITY_IN_SECONDS, VALIDITY_IN_DAYS) values (33, 'Upto 9800 Profiles - Monthly', 'Upto 9800 Profiles - Monthly Plan', 1, 9800, 180, 2592000, 30);
insert into LICENSE_PLANS (PLAN_ID, PLAN_NAME, PLAN_DESCRIPTION, PLAN_TYPE, MAX_COUNT, PRICING, VALIDITY_IN_SECONDS, VALIDITY_IN_DAYS) values (34, 'Upto 9800 Profiles - Yearly', 'Upto 9800 Profiles - Yearly Plan', 1, 9800, 1800, 31536000, 365);
insert into LICENSE_PLANS (PLAN_ID, PLAN_NAME, PLAN_DESCRIPTION, PLAN_TYPE, MAX_COUNT, PRICING, VALIDITY_IN_SECONDS, VALIDITY_IN_DAYS) values (35, 'Upto 10400 Profiles - Monthly', 'Upto 10400 Profiles - Monthly Plan', 1, 10400, 190, 2592000, 30);
insert into LICENSE_PLANS (PLAN_ID, PLAN_NAME, PLAN_DESCRIPTION, PLAN_TYPE, MAX_COUNT, PRICING, VALIDITY_IN_SECONDS, VALIDITY_IN_DAYS) values (36, 'Upto 10400 Profiles - Yearly', 'Upto 10400 Profiles - Yearly Plan', 1, 10400, 1908, 31536000, 365);
insert into LICENSE_PLANS (PLAN_ID, PLAN_NAME, PLAN_DESCRIPTION, PLAN_TYPE, MAX_COUNT, PRICING, VALIDITY_IN_SECONDS, VALIDITY_IN_DAYS) values (37, 'Upto 11000 Profiles - Monthly', 'Upto 11000 Profiles - Monthly Plan', 1, 11000, 200, 2592000, 30);
insert into LICENSE_PLANS (PLAN_ID, PLAN_NAME, PLAN_DESCRIPTION, PLAN_TYPE, MAX_COUNT, PRICING, VALIDITY_IN_SECONDS, VALIDITY_IN_DAYS) values (38, 'Upto 11000 Profiles - Yearly', 'Upto 11000 Profiles - Yearly Plan', 1, 11000, 2004, 31536000, 365);
insert into LICENSE_PLANS (PLAN_ID, PLAN_NAME, PLAN_DESCRIPTION, PLAN_TYPE, MAX_COUNT, PRICING, VALIDITY_IN_SECONDS, VALIDITY_IN_DAYS) values (39, 'Upto 11600 Profiles - Monthly', 'Upto 11600 Profiles - Monthly Plan', 1, 11600, 210, 2592000, 30);
insert into LICENSE_PLANS (PLAN_ID, PLAN_NAME, PLAN_DESCRIPTION, PLAN_TYPE, MAX_COUNT, PRICING, VALIDITY_IN_SECONDS, VALIDITY_IN_DAYS) values (40, 'Upto 11600 Profiles - Yearly', 'Upto 11600 Profiles - Yearly Plan', 1, 11600, 2100, 31536000, 365);
insert into LICENSE_PLANS (PLAN_ID, PLAN_NAME, PLAN_DESCRIPTION, PLAN_TYPE, MAX_COUNT, PRICING, VALIDITY_IN_SECONDS, VALIDITY_IN_DAYS) values (41, 'Upto 12200 Profiles - Monthly', 'Upto 12200 Profiles - Monthly Plan', 1, 12200, 220, 2592000, 30);
insert into LICENSE_PLANS (PLAN_ID, PLAN_NAME, PLAN_DESCRIPTION, PLAN_TYPE, MAX_COUNT, PRICING, VALIDITY_IN_SECONDS, VALIDITY_IN_DAYS) values (42, 'Upto 12200 Profiles - Yearly', 'Upto 12200 Profiles - Yearly Plan', 1, 12200, 2208, 31536000, 365);
insert into LICENSE_PLANS (PLAN_ID, PLAN_NAME, PLAN_DESCRIPTION, PLAN_TYPE, MAX_COUNT, PRICING, VALIDITY_IN_SECONDS, VALIDITY_IN_DAYS) values (43, 'Upto 12800 Profiles - Monthly', 'Upto 12800 Profiles - Monthly Plan', 1, 12800, 230, 2592000, 30);
insert into LICENSE_PLANS (PLAN_ID, PLAN_NAME, PLAN_DESCRIPTION, PLAN_TYPE, MAX_COUNT, PRICING, VALIDITY_IN_SECONDS, VALIDITY_IN_DAYS) values (44, 'Upto 12800 Profiles - Yearly', 'Upto 12800 Profiles - Yearly Plan', 1, 12800, 2304, 31536000, 365);
insert into LICENSE_PLANS (PLAN_ID, PLAN_NAME, PLAN_DESCRIPTION, PLAN_TYPE, MAX_COUNT, PRICING, VALIDITY_IN_SECONDS, VALIDITY_IN_DAYS) values (45, 'Upto 13400 Profiles - Monthly', 'Upto 13400 Profiles - Monthly Plan', 1, 13400, 240, 2592000, 30);
insert into LICENSE_PLANS (PLAN_ID, PLAN_NAME, PLAN_DESCRIPTION, PLAN_TYPE, MAX_COUNT, PRICING, VALIDITY_IN_SECONDS, VALIDITY_IN_DAYS) values (46, 'Upto 13400 Profiles - Yearly', 'Upto 13400 Profiles - Yearly Plan', 1, 13400, 2400, 31536000, 365);
insert into LICENSE_PLANS (PLAN_ID, PLAN_NAME, PLAN_DESCRIPTION, PLAN_TYPE, MAX_COUNT, PRICING, VALIDITY_IN_SECONDS, VALIDITY_IN_DAYS) values (47, 'Upto 14000 Profiles - Monthly', 'Upto 14000 Profiles - Monthly Plan', 1, 14000, 250, 2592000, 30);
insert into LICENSE_PLANS (PLAN_ID, PLAN_NAME, PLAN_DESCRIPTION, PLAN_TYPE, MAX_COUNT, PRICING, VALIDITY_IN_SECONDS, VALIDITY_IN_DAYS) values (48, 'Upto 14000 Profiles - Yearly', 'Upto 14000 Profiles - Yearly Plan', 1, 14000, 2508, 31536000, 365);
insert into LICENSE_PLANS (PLAN_ID, PLAN_NAME, PLAN_DESCRIPTION, PLAN_TYPE, MAX_COUNT, PRICING, VALIDITY_IN_SECONDS, VALIDITY_IN_DAYS) values (49, 'Unlimited Profiles - Monthly', 'Unlimited Profiles - Monthly Plan', 1, 2147483647, 260, 2592000, 30);
insert into LICENSE_PLANS (PLAN_ID, PLAN_NAME, PLAN_DESCRIPTION, PLAN_TYPE, MAX_COUNT, PRICING, VALIDITY_IN_SECONDS, VALIDITY_IN_DAYS) values (50, 'Unlimited Profiles - Yearly', 'Unlimited Profiles - Yearly Plan', 1, 2147483647, 2604, 31536000, 365);
