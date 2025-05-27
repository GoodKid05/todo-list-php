<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateUsersTable extends AbstractMigration
{
    
    public function up(): void
    {
        $this->execute("CREATE TABLE IF NOT EXISTS users (
            id SERIAL PRIMARY KEY,
            name TEXT NOT NULL,
            email TEXT NOT NULL UNIQUE,
            password_hash TEXT NOT NULL,
            role TEXT NOT NULL DEFAULT 'user',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            email_verified BOOLEAN DEFAULT FALSE,
            is_active BOOLEAN DEFAULT FALSE
        )");
    }

    public function down(): void {
        $this->execute("DROP TABLE IF NOT EXISTS users");
    }
}
