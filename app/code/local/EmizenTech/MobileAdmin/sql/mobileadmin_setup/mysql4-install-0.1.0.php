<?php


 $installer = $this;

    $installer->startSetup();

    $installer->run("

        -- DROP TABLE IF EXISTS {$this->getTable('emizenmob')};
        CREATE TABLE {$this->getTable('emizenmob')} (
        `user_id` int(11) unsigned NOT NULL auto_increment,
        `username` varchar(255) NOT NULL default '',
        `firstname` varchar(255) NOT NULL default '',
        `lastname` varchar(255) NOT NULL default '',
        `email` varchar(255) NOT NULL default '',
        `apikey` varchar(40) NOT NULL default '',
        `device_token` varchar(255) NOT NULL default '',
        `notification_flag` smallint(11) NOT NULL default '1',
        PRIMARY KEY (`user_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

        ALTER TABLE `{$installer->getTable('emizenmob')}` ADD `device_type` VARCHAR( 255 ) NOT NULL DEFAULT '',
        ADD `is_logout` SMALLINT( 11 ) NOT NULL DEFAULT '0';

        ");

    $installer->endSetup(); 
	 