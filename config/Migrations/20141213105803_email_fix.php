<?php

use Phinx\Migration\AbstractMigration;

class EmailFix extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up()
    {
        // $this->table('credentials')->removeColumn('email')->update();
        $this->table('users')->changeColumn('email', 'string', ['limit' => 100, 'null' => true])->update();
    }

    /**
     * Migrate Down.
     */
    public function down()
    {

    }
}
