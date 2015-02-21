<?php

/**
 * Scheduledemails Installer
 * 
 * @category    Aydus
 * @package     Aydus_Scheduledemails
 * @author     	Aydus Consulting <davidt@aydus.com>
 * 
 */

$installer = $this;
$installer->startSetup();

$installer->run("CREATE TABLE IF NOT EXISTS {$this->getTable('aydus_scheduledemails_campaign')} (
  `campaign_id` INT(11) NOT NULL AUTO_INCREMENT,
  `admin_user_id` INT(11) NOT NULL,
  `name` VARCHAR(255) NOT NULL,
  `description` TEXT NOT NULL,
  `frequency` ENUM('D', 'W', 'M'),
  `month_day` TINYINT(1) NOT NULL,
  `week_day` TINYINT(1) NOT NULL,
  `start_time` VARCHAR(15) NOT NULL,
  `scheduled_emails` TEXT NOT NULL,
  `attribute_set_ids` TEXT NOT NULL,
  `skus` TEXT NOT NULL,
  `date_created` DATETIME NOT NULL,
  `date_updated` DATETIME NOT NULL,
  PRIMARY KEY (`campaign_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8");

$installer->run("CREATE TABLE IF NOT EXISTS {$this->getTable('aydus_scheduledemails_campaign_schedule')} (
`schedule_id` INT(11) NOT NULL,
`campaign_id` INT(11) NOT NULL,
PRIMARY KEY ( `schedule_id` )
) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

$installer->run("CREATE TABLE IF NOT EXISTS {$this->getTable('aydus_scheduledemails_campaign_customer')} (
`id` INT(11) NOT NULL AUTO_INCREMENT,
`email` VARCHAR(255) NOT NULL,
`increment_id` VARCHAR(255) NOT NULL,
`item_id` INT(11) NOT NULL,
`product_id` INT(11) NOT NULL,
`campaign_id` INT(11) NOT NULL,
`stage` TINYINT(1) NOT NULL,
`date_created` DATETIME NOT NULL,
`date_updated` DATETIME NOT NULL,
PRIMARY KEY ( `id` )
) ENGINE=InnoDB DEFAULT CHARSET=utf8;");



$installer->endSetup();