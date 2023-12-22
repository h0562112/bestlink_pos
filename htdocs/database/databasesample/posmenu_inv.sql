CREATE TABLE `itemsdata` (
  `company` varchar(128) NOT NULL,
  `inumber` varchar(10) NOT NULL,
  `imgfile` varchar(64) DEFAULT NULL,
  `isgroup` int(11) NOT NULL DEFAULT '0',
  `childtype` varchar(128) DEFAULT NULL,
  `frontsq` int(11) DEFAULT NULL,
  `rearsq` int(11) DEFAULT NULL,
  `fronttype` varchar(5) NOT NULL,
  `reartype` varchar(5) NOT NULL,
  `taste` varchar(256) DEFAULT NULL,
  `sale` varchar(256) DEFAULT NULL,
  `createtime` datetime NOT NULL
);
CREATE TABLE `invlist` (
  `invnumber` varchar(15) NOT NULL,
  `createdate` varchar(10) NOT NULL,
  `createtime` varchar(8) NOT NULL,
  `buyerid` varchar(10) NOT NULL,
  `buyname` varchar(20) NOT NULL,
  `sellerid` varchar(10) NOT NULL,
  `sellername` varchar(20) NOT NULL,
  `relatenumber` varchar(20) NOT NULL,
  `invtype` varchar(5) NOT NULL DEFAULT '07',
  `donatemark` varchar(2) NOT NULL,
  `carriertype` varchar(10) DEFAULT NULL,
  `carrierid1` varchar(15) DEFAULT NULL,
  `carrierid2` varchar(15) DEFAULT NULL,
  `printmark` varchar(2) NOT NULL,
  `npoban` varchar(10) DEFAULT NULL,
  `randomnumber` varchar(4) NOT NULL,
  `totalamount` int(11) NOT NULL,
  `canceldate` varchar(10) DEFAULT NULL,
  `canceltime` varchar(8) DEFAULT NULL,
  `cancelreason` varchar(256) DEFAULT NULL,
  `replaceinv` varchar(15) DEFAULT NULL,
  `state` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`invnumber`,`createdate`,`createtime`,`buyerid`)
);
CREATE TABLE `number` (
  `company` varchar(32) NOT NULL,
  `story` varchar(16) NOT NULL,
  `banno` varchar(10) NOT NULL,
  `datetime` varchar(5) NOT NULL,
  `state` int(1) NOT NULL,
  PRIMARY KEY (`banno`)
);
CREATE TABLE `salelist` (
  `listno` varchar(20) NOT NULL,
  `invnumber` varchar(15) NOT NULL,
  `createdate` varchar(10) NOT NULL,
  `createtime` varchar(8) NOT NULL,
  `lineno` int(11) NOT NULL,
  `name` varchar(32) NOT NULL,
  `qty` int(11) NOT NULL,
  `unitprice` int(11) NOT NULL,
  `money` int(11) NOT NULL,
  `state` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`listno`,`invnumber`,`createdate`,`createtime`,`lineno`)
);
CREATE TABLE 'reprint' (
	'rdate' varchar(8) NOT NULL, 
	'rtime' varchar(8) NOT NULL,
	'invnumber' varchar(10) NOT NULL, 
	'invdate' varchar(8), 
	'invimte' varchar(8) NOT NULL,
	PRIMARY KEY ('rdate', 'rtime', 'invnumber')
);
;
;
;
;
