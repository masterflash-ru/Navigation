<?php

namespace Mf\Navigation;

use Mf\Migrations\AbstractMigration;
use Mf\Migrations\MigrationInterface;

class Version20191104162641 extends AbstractMigration implements MigrationInterface
{
    public static $description = "Migration description";

    public function up($schema)
    {
        switch ($this->db_type){
            case "mysql":{
                $this->addSql("CREATE TABLE `menu` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `subid` int(11) NOT NULL DEFAULT '0',
                  `level` int(11) NOT NULL DEFAULT '0',
                  `locale` char(20) NOT NULL COMMENT 'локаль',
                  `sysname` char(50) NOT NULL DEFAULT '0' COMMENT 'имя меню',
                  `label` char(255) NOT NULL,
                  `poz` int(11) DEFAULT NULL,
                  `url` varchar(127) NOT NULL DEFAULT '',
                  `mvc` char(255) DEFAULT NULL COMMENT 'состояние сайта в терминах роутеров Zend',
                  PRIMARY KEY (`id`),
                  KEY `subid` (`subid`,`level`),
                  KEY `locale` (`locale`),
                  KEY `sysname` (`sysname`)
                ) ENGINE=MyISAM DEFAULT CHARSET=utf8");
                break;
            }
            default:{
                throw new \Exception("the database {$this->db_type} is not supported !");
            }
        }
    }

    public function down($schema)
    {
        //throw new \RuntimeException('No way to go down!');
        switch ($this->db_type){
            case "mysql":{
                $this->addSql("DROP TABLE `menu`");
                break;
            }
            default:{
                throw new \Exception("the database {$this->db_type} is not supported !");
            }
        }
    }
}
