<?php

use Phinx\Migration\AbstractMigration;

class AddMissingModifiedColumn extends AbstractMigration
{
    public function change()
    {
        $user_tokens = $this->table('user_tokens');
        $user_tokens
            ->addColumn('modified', 'integer', array('after' => 'created'))
            ->update();

        $login_attempts = $this->table('login_attempts');
        $login_attempts
        ->addColumn('modified', 'integer', array('after' => 'created'))
        ->update();
    }
}
