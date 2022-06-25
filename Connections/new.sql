#
# Table structure for table `oabs_adverts`
#

DROP TABLE IF EXISTS `oabs_adverts`;
CREATE TABLE `oabs_adverts` (
  `id` int(4) NOT NULL auto_increment,
  `adcode` text NOT NULL,
  `impcount` int(10) NOT NULL default '0',
  `implimit` int(10) NOT NULL default '0',
  `address` text NOT NULL,
  `email` text NOT NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

# --------------------------------------------------------

#
# Table structure for table `oabs_customers`
#

DROP TABLE IF EXISTS `oabs_customers`;
CREATE TABLE `oabs_customers` (
  `name` text NOT NULL,
  `address1` text NOT NULL,
  `address2` text NOT NULL,
  `county` text NOT NULL,
  `postcode` text NOT NULL,
  `customer` tinyint(4) NOT NULL auto_increment,
  `seats` text NOT NULL,
  `number` tinyint(4) NOT NULL default '0',
  `class` text NOT NULL,
  PRIMARY KEY  (`customer`),
  UNIQUE KEY `customer` (`customer`)
) TYPE=MyISAM;

# --------------------------------------------------------

#
# Table structure for table `oabs_flights`
#

DROP TABLE IF EXISTS `oabs_flights`;
CREATE TABLE `oabs_flights` (
  `number` tinyint(4) NOT NULL auto_increment,
  `Dest` text NOT NULL,
  `datetime` timestamp(14) NOT NULL,
  `passb` int(4) NOT NULL default '0',
  `passf` int(4) NOT NULL default '0',
  `passe` int(4) NOT NULL default '0',
  `cost` int(10) NOT NULL default '0',
  `priceb` int(4) NOT NULL default '0',
  `pricef` int(4) NOT NULL default '0',
  `pricee` int(4) NOT NULL default '0',
  `senior` int(4) NOT NULL default '0',
  `child` int(4) NOT NULL default '0',
  PRIMARY KEY  (`number`),
  UNIQUE KEY `number` (`number`)
) TYPE=MyISAM;

# --------------------------------------------------------

#
# Table structure for table `oabs_offers`
#

DROP TABLE IF EXISTS `oabs_offers`;
CREATE TABLE `oabs_offers` (
  `no` tinyint(4) NOT NULL auto_increment,
  `offer` longtext NOT NULL,
  KEY `no` (`no`)
) TYPE=MyISAM;

# --------------------------------------------------------

#
# Table structure for table `oabs_user`
#

DROP TABLE IF EXISTS `oabs_user`;
CREATE TABLE `oabs_user` (
  `ID` smallint(3) NOT NULL auto_increment,
  `Name` varchar(30) NOT NULL default '',
  `Password` varchar(32) NOT NULL default '',
  PRIMARY KEY  (`ID`),
  UNIQUE KEY `Name` (`Name`)
) TYPE=MyISAM;
