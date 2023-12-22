CREATE TABLE 'login' (
	'no' INTEGER, 
	'rfid' varchar(32),
	'id' varchar(128) NOT NULL, 
	'psw' varchar(128), 
	'name' varchar(256) NOT NULL,
	'group' varchar(4) NOT NULL,
	'state' INTEGER, 
	'logindatetime' varchar(14), 
	'logoutdatetime' varchar(14), 
	'createdatetime' varchar(14), 
	'stopdatetime' varchar(14), 
	PRIMARY KEY ('id', 'rfid', 'name')
);
;
