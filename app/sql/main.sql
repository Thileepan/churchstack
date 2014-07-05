create database CHURCHSTACK collate latin1_general_cs;
use CHURCHSTACK;

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

create table CHURCH_DETAILS (
	CHURCH_ID INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
	CHURCH_NAME VARCHAR(128) NOT NULL,
	DESCRIPTION VARCHAR(1024),
	ADDRESS VARCHAR(1024),
	LANDLINE VARCHAR(20),
	MOBILE VARCHAR(20),
	EMAIL VARCHAR(255),
	WEBSITE VARCHAR(255),
	SIGNUP_TIME TIMESTAMP,
	LAST_UPDATE_TIME TIMESTAMP,
	SHARDED_DATABASE VARCHAR(255),
	CURRENCY_ID SMALLINT UNSIGNED NOT NULL,
	UNIQUE_HASH VARCHAR(128) NOT NULL,
	STATUS TINYINT UNSIGNED NOT NULL DEFAULT 1,

	constraint CHURCH_DETAILS_PK PRIMARY KEY (CHURCH_ID)
);

create table LICENSE_PLANS (
	PLAN_ID SMALLINT UNSIGNED NOT NULL,
	PLAN_NAME VARCHAR(128),
	PLAN_DESCRIPTION VARCHAR(1024),
	PLAN_TYPE SMALLINT UNSIGNED,
	MAX_COUNT INTEGER,
	PRICING DECIMAL(14,6),
	VALIDITY_IN_SECONDS BIGINT UNSIGNED,
	VALIDITY_IN_DAYS INTEGER UNSIGNED,
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
	ROLE_ID SMALLINT UNSIGNED NOT NULL DEFAULT 2,
	PASSWORD VARCHAR(128) NOT NULL,
	UNIQUE_HASH VARCHAR(128),
	PASSWORD_RESET_HASH VARCHAR(128),
	PASSWORD_RESET_EXPIRY BIGINT UNSIGNED,
	STATUS TINYINT UNSIGNED NOT NULL DEFAULT 1,

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
	SUBTOTAL DECIMAL(14,6),
	ADDITIONAL_CHARGE DECIMAL(14,6),
	DISCOUNT_PERCENTAGE DECIMAL(6,3),
	DISCOUNT_AMOUNT DECIMAL(14,6),
	TAX_PERCENTAGE DECIMAL(6,3),
	TAX_AMOUNT DECIMAL(14,6),
	TAX_2_PERCENTAGE DECIMAL(6,3),
	TAX_2_AMOUNT DECIMAL(14,6),
	VAT_PERCENTAGE DECIMAL(6,3),
	VAT_AMOUNT DECIMAL(14,6),
	NET_TOTAL DECIMAL(14,6),
	COUPON_CODE VARCHAR(64),
	INVOICE_NOTES VARCHAR(512),
	PAYMENT_GATEWAY VARCHAR(128),
	PAYMENT_MODE VARCHAR(128),
	IP_ADDRESS VARCHAR(128),
	PURCHASE_STATUS_CODE TINYINT UNSIGNED NOT NULL DEFAULT 1,
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
	PLAN_COST DECIMAL(14,6),
	QUANTITY INTEGER UNSIGNED,
	TOTAL_COST DECIMAL(14,6),
	IS_AUTORENEWAL_ENABLED TINYINT UNSIGNED NOT NULL DEFAULT 0,
	
	index INVOICED_ITEMS_IDX_1 (INVOICE_ID),
	constraint INVOICED_ITEMS_FK_1 FOREIGN KEY (INVOICE_ID) REFERENCES INVOICE_REPORT (INVOICE_ID)
);

create table COUPONS (
	COUPON_ID BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
	COUPON_CODE VARCHAR(64) UNIQUE,
	CHURCH_ID BIGINT UNSIGNED,
	DISCOUNT_PERCENTAGE DECIMAL(6,3),
	DISCOUNT_FLAT_AMOUNT DECIMAL(10,4),
	MINIMUM_SUBTOTAL DECIMAL(10,4),
	VALIDITY DATETIME,
	VALID_FOR_ALL TINYINT UNSIGNED NOT NULL DEFAULT 0,

	index COUPONS_IDX_1 (COUPON_CODE, CHURCH_ID, VALIDITY, MINIMUM_SUBTOTAL),
	index COUPONS_IDX_2 (COUPON_CODE, VALID_FOR_ALL, VALIDITY, MINIMUM_SUBTOTAL),
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

insert into CURRENCY_LIST values(0, 'AED', '784', 'United Arab Emirates Dirham', 'United Arab Emirates');
insert into CURRENCY_LIST values(0, 'AFN', '971', 'Afghan Afghani', 'Afghanistan');
insert into CURRENCY_LIST values(0, 'ALL', '008', 'Albanian Lek', 'Albania');
insert into CURRENCY_LIST values(0, 'AMD', '051', 'Armenian Dram', 'Armenia');
insert into CURRENCY_LIST values(0, 'ANG', '532', 'Netherlands Antillean Guilder', 'Curaçao (CW),  Sint Maarten (SX)');
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
insert into CURRENCY_LIST values(0, 'EUR', '978', 'Euro', 'Andorra (AD),  Austria (AT),  Belgium (BE),  Cyprus (CY) except  Northern Cyprus,  Estonia (EE),  Finland (FI),  France (FR),  Germany (DE),  Greece (GR),  Ireland (IE),  Italy (IT),  Kosovo,  Latvia (LV),  Luxembourg (LU),  Malta (MT),  Martinique (MQ),  Mayotte (YT),  Monaco (MC),  Montenegro (ME),  Netherlands (NL),  Portugal (PT),  Reunion (RE),  San Marino (SM),  Saint Barthélemy (BL),  Slovakia (SK),  Slovenia (SI),  Spain (ES),  Saint Pierre and Miquelon (PM),   Vatican City (VA); see Eurozone');
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
insert into CURRENCY_LIST values(0, 'ISK', '352', 'Icelandic Króna', 'Iceland');
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
insert into CURRENCY_LIST values(0, 'NIO', '558', 'Nicaraguan Córdoba', 'Nicaragua');
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
insert into CURRENCY_LIST values(0, 'PYG', '600', 'Paraguayan Guaraní', 'Paraguay');
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
insert into CURRENCY_LIST values(0, 'STD', '678', 'São Tomé and Príncipe Dobra', 'São Tomé and Príncipe');
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
insert into CURRENCY_LIST values(0, 'VEF', '937', 'Venezuelan Bolívar', 'Venezuela');
insert into CURRENCY_LIST values(0, 'VND', '704', 'Vietnamese Dong', 'Vietnam');
insert into CURRENCY_LIST values(0, 'VUV', '548', 'Vanuatu Vatu', 'Vanuatu');
insert into CURRENCY_LIST values(0, 'WST', '882', 'Samoan Tala', 'Samoa');
insert into CURRENCY_LIST values(0, 'XAF', '950', 'CFA Franc BEAC', 'Cameroon (CM),  Central African Republic (CF),  Republic of the Congo (CG),  Chad (TD),  Equatorial Guinea (GQ),  Gabon (GA)');
insert into CURRENCY_LIST values(0, 'XCD', '951', 'East Caribbean Dollar', 'Anguilla (AI),  Antigua and Barbuda (AG),  Dominica (DM),  Grenada (GD),  Montserrat (MS),  Saint Kitts and Nevis (KN),  Saint Lucia (LC),  Saint Vincent and the Grenadines (VC)');
insert into CURRENCY_LIST values(0, 'XDR', '960', 'Special Drawing Rights','International Monetary Fund');
insert into CURRENCY_LIST values(0, 'XOF', '952', 'CFA Franc BCEAO', 'Benin (BJ),  Burkina Faso (BF),  Côte dIvoire (CI),  Guinea-Bissau (GW),  Mali (ML),  Niger (NE),  Senegal (SN),  Togo (TG)');
insert into CURRENCY_LIST values(0, 'XPF', '953', 'CFP Franc (Franc Pacifique)', 'French territories of the Pacific Ocean:  French Polynesia (PF),  New Caledonia (NC),  Wallis and Futuna (WF)');
insert into CURRENCY_LIST values(0, 'YER', '886', 'Yemeni Rial', 'Yemen');
insert into CURRENCY_LIST values(0, 'ZAR', '710', 'South African Rand', 'South Africa');
insert into CURRENCY_LIST values(0, 'ZMW', '967', 'Zambian Kwacha', 'Zambia');
insert into CURRENCY_LIST values(0, 'ZWD', '932', 'Zimbabwe Dollar', 'Zimbabwe');

insert into USER_ROLES values (1,'ChurchStack Admin');
insert into USER_ROLES values (2,'Church Admin');
