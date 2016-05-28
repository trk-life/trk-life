<?php

use Phinx\Migration\AbstractMigration;

class AddRoleColumnToUsers extends AbstractMigration
{
    public function change()
    {
        $users = $this->table('users');
        $users
            ->addColumn('role', 'string', array('after' => 'last_name'))
            ->update();
    }
}
