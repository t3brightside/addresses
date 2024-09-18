CREATE TABLE tt_content (
	tx_addresses tinytext,
	tx_addresses_template varchar(25),
	tx_addresses_images int(1) DEFAULT '0' NOT NULL,
	tx_addresses_cropratio varchar(25),
	tx_addresses_vcard int(1) DEFAULT '0' NOT NULL,
	tx_addresses_information int(1) DEFAULT '0' NOT NULL,
	tx_addresses_orderby tinytext,
	tx_addresses_startfrom varchar(6),
	tx_addresses_limit varchar(6),
	tx_addresses_titlewrap varchar(2),
);

CREATE TABLE tx_addresses_domain_model_address (
	name tinytext,
	info text,
	phone tinytext,
	fax tinytext,
	email tinytext,
	address text,
	city tinytext,
	zip tinytext,
	region tinytext,
	country tinytext,
	latitude tinytext,
	longitude tinytext,
	timezone tinytext,
	images int(11) unsigned DEFAULT '0',
	selected_categories text,
	linkedin tinytext,
	xing tinytext,
	x tinytext,
	github tinytext,
	instagram tinytext,
	youtube tinytext,
	facebook tinytext,
	website tinytext,
	pages INT UNSIGNED DEFAULT 0 NOT NULL,
	KEY parent (pid,sorting),
	KEY t3ver_oid (t3ver_oid,t3ver_wsid),
	KEY language (sys_language_uid)
);

CREATE TABLE pages (
	tx_addresses INT UNSIGNED DEFAULT 0 NOT NULL,
);


CREATE TABLE tx_addresses_mm (
    uid_local int(11) DEFAULT '0' NOT NULL,
    uid_foreign int(11) DEFAULT '0' NOT NULL,
    sorting int(11) DEFAULT '0' NOT NULL,
    sorting_foreign int(11) DEFAULT '0' NOT NULL,
		tablenames varchar(255) DEFAULT '' NOT NULL,
		fieldname varchar(255) DEFAULT '' NOT NULL,
    PRIMARY KEY (uid_local, uid_foreign),
    KEY uid_local (uid_local),
    KEY uid_foreign (uid_foreign)
);
