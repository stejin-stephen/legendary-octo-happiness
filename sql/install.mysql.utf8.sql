CREATE TABLE IF NOT EXISTS `#__tools` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,

`asset_id` INT(10) UNSIGNED NOT NULL DEFAULT '0',

`catid` TEXT NOT NULL ,
`title` VARCHAR(255)  NOT NULL ,
`alias` VARCHAR(255) COLLATE utf8_bin NOT NULL ,
`description` TEXT NOT NULL ,
`image` VARCHAR(255)  NOT NULL ,
`document` TEXT NOT NULL ,
`type` VARCHAR(255)  NOT NULL ,
`ordering` INT(11)  NOT NULL ,
`state` TINYINT(1)  NOT NULL ,
`access` INT(11)  NOT NULL ,
`language` VARCHAR(5)  NOT NULL ,
`checked_out` INT(11)  NOT NULL ,
`checked_out_time` DATETIME NOT NULL ,
`created_by` INT(11)  NOT NULL ,
`created` DATETIME NOT NULL ,
`modified_by` INT(11)  NOT NULL ,
`modified` DATETIME NOT NULL ,
PRIMARY KEY (`id`)
) DEFAULT COLLATE=utf8mb4_unicode_ci;

