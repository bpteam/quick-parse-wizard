<?php

namespace bpteam\QuickParserWizard\ApiKeyRotator;

use DateInterval;
use PDO;

class ApiKeyPostgresqlProvider implements ApiKeyProviderInterface
{
    public function __construct(
        private readonly PDO $pdo,
        private readonly float $blockWaitTime = 1.0,
        private readonly string $keyGroup = 'default',
        private readonly string $tableName = 'api_key_rotator',
    ) {}

    public function find(int $price, bool $blocking = false): ?KeyInterface
    {
        while (true) {
            $query = $this->pdo->prepare(<<<SQL
UPDATE "$this->tableName" SET "next_flush" = NOW() + "time_window", "limit" = "init_limit"
WHERE "key_group" = :key_group AND "next_flush" < NOW()
SQL);
            $query->execute(['key_group' => $this->keyGroup]);

            $query = $this->pdo->prepare(<<<SQL
SELECT "key", "key_name"
FROM "$this->tableName"
WHERE "key_group" = :key_group AND "limit" >= :price LIMIT 1
FOR UPDATE
SQL);

            $query->execute(['key_group' => $this->keyGroup, 'price' => $price]);
            $data = $query->fetch(PDO::FETCH_ASSOC);

            if ($data) {
                $query = $this->pdo->prepare(<<<SQL
UPDATE "$this->tableName" SET
    "limit" = "limit" - :price
WHERE "key_group" = :key_group AND "key_name" = :key_name
SQL);
                $query->execute(['key_group' => $this->keyGroup, 'key_name' => $data['key_name'], 'price' => $price]);

                return unserialize(base64_decode($data['key']));
            }

            if ($blocking) {
                usleep($this->blockWaitTime * 1000000);
            } else {
                break;
            }
        }

        return null;
    }

    public function add(
        string $keyName,
        KeyInterface $key,
        int $limit,
        DateInterval $timeWindow = new DateInterval('PT1M')
    ): void {
        $query = $this->pdo->prepare(<<<SQL
INSERT INTO "$this->tableName" ("key_group", "key_name", "key", "limit", "init_limit", "time_window", "next_flush")
VALUES (:group_name, :key_name, :key, $limit, $limit, :time_window, NOW() + :time_window)
ON CONFLICT ("key_group", "key_name") DO UPDATE SET
    "key" = EXCLUDED."key",
    "limit" = EXCLUDED."limit",
    "init_limit" = EXCLUDED."init_limit",
    "time_window" = EXCLUDED."time_window",
    "next_flush" = EXCLUDED."next_flush";
SQL);
        $query->execute([
            'group_name' => $this->keyGroup,
            'key_name' => $keyName,
            'key' => base64_encode(serialize($key)),
            'time_window' => $timeWindow->format('%y years %m months %d days %h hours %i minutes %s seconds'),
        ]);
    }

    public function remove(string $keyName): void
    {
        $query = $this->pdo->prepare(<<<SQL
DELETE FROM "$this->tableName"
WHERE "key_group" = :group_name AND "key_name" = :key_name;
SQL);
        $query->execute([
            'group_name' => $this->keyGroup,
            'key_name' => $keyName,
        ]);
    }

    public function init(): void
    {
        $this->pdo->exec(<<<SQL
CREATE TABLE IF NOT EXISTS "$this->tableName" (
    "key_group" VARCHAR(255) NOT NULL DEFAULT '$this->keyGroup',
    "key_name" VARCHAR(255) NOT NULL,
    "key" TEXT NOT NULL,
    "limit" INT NOT NULL,
    "init_limit" INT NOT NULL,
    "time_window" INTERVAL NOT NULL,
    "next_flush" TIMESTAMP NOT NULL,
    PRIMARY KEY ("key_group", "key_name")
);
SQL
        );
    }

    public function deleteStorage(): void
    {
        $this->pdo->exec(<<<SQL
DROP TABLE IF EXISTS "$this->tableName";
SQL);
    }


    public function flush(): void
    {
        $query = $this->pdo->prepare(
            <<<SQL
DELETE FROM "$this->tableName" WHERE "key_group" = :key_group;
SQL
        );

        $query->execute(['key_group' => $this->keyGroup]);
    }

}