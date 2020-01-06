<?php

namespace Mf\Navigation;

use Mf\Migrations\AbstractMigration;
use Mf\Migrations\MigrationInterface;
use Laminas\Db\Sql\Ddl;

class Version20191104162641 extends AbstractMigration implements MigrationInterface
{
    public static $description = "Site Menu";

    public function up($schema, $adapter)
    {
        $this->mysql_add_create_table=" ENGINE=MyIsam DEFAULT CHARSET=utf8";
        $table = new Ddl\CreateTable("menu");
        $table->addColumn(new Ddl\Column\Integer('id',false,null,["AUTO_INCREMENT"=>true]));
        $table->addColumn(new Ddl\Column\Integer('level',false,0,["COMMENT"=>"уровень"]));
        $table->addColumn(new Ddl\Column\Integer('subid',false,0,["COMMENT"=>"ID родителя"]));
        $table->addColumn(new Ddl\Column\Char('locale', 20,false,null,["COMMENT"=>"Локаль"]));
        $table->addColumn(new Ddl\Column\Char('sysname', 50,false,null,["COMMENT"=>"Имя элемента"]));
        $table->addColumn(new Ddl\Column\Char('label', 255,true,null,["COMMENT"=>"Текст элемента"]));
        $table->addColumn(new Ddl\Column\Integer('poz',true,0,["COMMENT"=>"порядок"]));
        $table->addColumn(new Ddl\Column\Char('url', 255,true,null,["COMMENT"=>"URL"]));
        $table->addColumn(new Ddl\Column\Char('mvc', 255,true,null,["состояние сайта в терминах роутеров Laminas"]));

        $table->addConstraint(
            new Ddl\Constraint\PrimaryKey(['id'])
        );
        $table->addConstraint(
            new Ddl\Index\Index(['level'],'level')
        )->addConstraint(
            new Ddl\Index\Index(['subid'],'subid'));
        $this->addSql($table);
    }

    public function down($schema, $adapter)
    {
        $drop = new Ddl\DropTable('menu');
        $this->addSql($drop);
    }
}
