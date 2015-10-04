<?php

use Phinx\Migration\AbstractMigration;

class DbInit extends AbstractMigration
{

/**
 * Migrate Up.
 */
    public function up()
    {
        $this->table('credentials')
            ->addColumn('user_id', 'integer', ['limit' => 11])
            ->addColumn('email', 'string', ['limit' => 100])
            ->addColumn('token', 'string', ['limit' => 255])
            ->addColumn('provider', 'string', ['limit' => 16])
            ->addColumn('created', 'datetime')
            ->addColumn('modified', 'datetime')
            ->create();

        $this->table('organizations')
            ->addColumn('name', 'string', ['limit' => '64'])
            ->create();

        $this->table('organizations_projects')
            ->addColumn('organization_id', 'integer', ['limit' => 11])
            ->addColumn('project_id', 'integer', ['limit' => 11])
            ->create();

        $this->table('organizations_users')
            ->addColumn('organization_id', 'integer', ['limit' => 11])
            ->addColumn('user_id', 'integer', ['limit' => 11])
            ->create();

        $this->table('projects')
            ->addColumn('provider', 'string', ['limit' => 16])
            ->addColumn('username', 'string', ['limit' => 20])
            ->addColumn('name', 'string', ['limit' => 40])
            ->addColumn('description', 'text', ['null' => true])
            ->addColumn('watchers', 'integer')
            ->addColumn('fork', 'boolean')
            ->addColumn('state', 'string', ['limit' => 20, 'default' => 'hide'])
            ->addColumn('visible', 'boolean', ['default' => 0])
            ->addColumn('created', 'datetime')
            ->addColumn('modified', 'datetime')
            ->create();

        $this->table('projects_users')
            ->addColumn('project_id', 'integer', ['limit' => 11])
            ->addColumn('user_id', 'integer', ['limit' => 11])
            ->create();

        $this->table('users')
            ->addColumn('email', 'string', ['limit' => 100])
            ->addColumn('username', 'string', ['limit' => 20])
            ->addColumn('created', 'datetime')
            ->addColumn('modified', 'datetime')
            ->create();
    }

    public function down()
    {
        $this->table('credentials')->drop();
        $this->table('organizations')->drop();
        $this->table('organizations_projects')->drop();
        $this->table('organizations_users')->drop();
        $this->table('projects')->drop();
        $this->table('projects_users')->drop();
        $this->table('users')->drop();
    }
}
