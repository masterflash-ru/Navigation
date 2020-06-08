<?php

namespace Mf\Navigation;

use Mf\Migrations\AbstractMigration;
use Mf\Migrations\MigrationInterface;
use Laminas\Db\Sql\Ddl;

class Version20200605182641 extends AbstractMigration implements MigrationInterface
{
    public static $description = "Site Menu";

    public function up($schema, $adapter)
    {
        $table = new Ddl\AlterTable("menu");
        $table->addColumn(new Ddl\Column\Text('options', null,true,null,["COMMENT"=>"Опции в формате JSON"]));
        $this->addSql($table);
    }

    public function down($schema, $adapter)
    {
        $table = new Ddl\AlterTable("menu");
        $table->dropColumn('options');
        $this->addSql($table);
    }
}
