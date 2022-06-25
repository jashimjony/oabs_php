# This upgrade is only for users previously running OABSv1.5
#
# Table structure for table `oabs_adverts`
#

CREATE TABLE `oabs_adverts` (
  `id` int(4) NOT NULL auto_increment,
  `adcode` text NOT NULL,
  `impcount` int(10) NOT NULL default '0',
  `implimit` int(10) NOT NULL default '0',
  `address` text NOT NULL,
  `email` text NOT NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

#Nothing else requires modification