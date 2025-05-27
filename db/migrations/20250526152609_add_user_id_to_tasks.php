<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddUserIdToTasks extends AbstractMigration
{
    public function up(): void
    {
        $this->execute(
            "ALTER TABLE tasks 
            ADD COLUMN user_id INT,
            ADD CONSTRAINT fk_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL;"
        );
    }
    public function down(): void
    {
        $this->execute("ALTER TABLE tasks DROP COLUMN IF EXISTS user_id;");
        $this->execute("ALTER TABLE tasks DROP CONSTRAINT IF EXISTS fk_user;");
    }
}
