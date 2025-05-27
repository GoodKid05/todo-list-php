<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateRefreshTokensTable extends AbstractMigration
{

    public function up(): void
    {
        $this->execute("CREATE TABLE IF NOT EXISTS refresh_tokens (
            id SERIAL PRIMARY KEY,
            user_id INTEGER NOT NULL,
            token TEXT NOT NULL,
            expires_at TIMESTAMP NOT NULL,
            revoked BOOLEAN DEFAULT FALSE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            CONSTRAINT fk_user FOREIGN KEY (user_id) REFERENCES users(id)

        )");
    }

    public function down(): void
    {
        $this->execute("DROP TABLE IF EXISTS refresh_tokens");
    }
}
