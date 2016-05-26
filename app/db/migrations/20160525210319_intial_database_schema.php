<?php

use Phinx\Migration\AbstractMigration;

class IntialDatabaseSchema extends AbstractMigration
{
    public function change()
    {
        $users_table = $this->table('users');
        $users_table
            ->addColumn('email', 'string', array('null' => false))
            ->addColumn('password', 'string', array('null' => false))
            ->addColumn('first_name', 'string', array('limit' => 35))
            ->addColumn('last_name', 'string', array('limit' => 35))
            ->addColumn('status', 'string', array('limit' => 20, 'null' => false))
            ->addColumn('created', 'integer', array('null' => false))
            ->addColumn('modified', 'integer')
            ->addIndex('email', array('unique' => true))
            ->create();

        $login_attempts = $this->table('login_attempts');
        $login_attempts
            ->addColumn('email', 'string', array('null' => false))
            ->addColumn('successful', 'boolean', array('default' => 0))
            ->addColumn('failure_reason', 'string')
            ->addColumn('user_agent', 'string')
            ->addColumn('ip_address', 'string')
            ->addColumn('created', 'integer', array('null' => false))
            ->create();

        $forgotten_password_requests = $this->table('forgotten_password_requests');
        $forgotten_password_requests
            ->addColumn('email', 'string', array('null' => false))
            ->addColumn('token', 'string')
            ->addColumn('ip_address', 'string')
            ->addColumn('user_agent', 'string')
            ->addColumn('status', 'string', array('limit' => 20, 'null' => false))
            ->addColumn('created', 'integer', array('null' => false))
            ->addColumn('modified', 'integer')
            ->create();

        $tokens_table = $this->table('user_tokens');
        $tokens_table
            ->addColumn('user_id', 'integer', array('null' => false))
            ->addColumn('token', 'string', array('null' => false))
            ->addColumn('expires_after', 'integer', array('null' => false))
            ->addColumn('last_accessed', 'integer', array('null' => false))
            ->addColumn('user_agent', 'string')
            ->addColumn('created', 'integer', array('null' => false))
            ->addIndex('token', array('unique' => true))
            ->create();
    }
}
