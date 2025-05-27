<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class MakeTokenUniqueInRefreshTokens extends AbstractMigration
{
    public function up(): void
    {
        $this->execute("ALTER TABLE refresh_tokens ADD CONSTRAINT unique_token UNIQUE(token);");
    }

    public function down(): void
    {
        $this->execute("ALTER TABLE refresh_tokens DROP CONSTRAINT unique_token;");
    }
}
