<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateTaskTable extends AbstractMigration
{
    public function up(): void
    {
        if(!$this->hasTable('tasks')) {
            
            $this->execute("CREATE TYPE task_status AS ENUM ('not started', 'in progress', 'overdue', 'completed')");
    
            $this->execute("
                CREATE TABLE tasks (
                id BIGSERIAL PRIMARY KEY,
                title TEXT NOT NULL,
                description TEXT NOT NULL,
                status task_status DEFAULT 'not started',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                deadline TIMESTAMP DEFAULT CURRENT_TIMESTAMP + INTERVAL '7 days'
                )
            ");
        }
    }
    public function down(): void {
        $this->execute("DROP TABLE IF EXISTS tasks");
        $this->execute("DROP TYPE IF EXISTS task_status");
    }
}
