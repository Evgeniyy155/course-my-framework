<?php

namespace Web\Framework\Console\Commands;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Types;
use Web\Framework\Console\CommandInterface;

class MigrateCommand implements CommandInterface
{
    private string $name = 'migrate';
    private const string MIGRATIONS_TABLE = 'migrations';

    public function __construct(
        private Connection $connection,
        private string $migrationPath
    )
    {

    }
    public function execute(array $parameters = []): int
    {
        try {
            // 1. Створити таблицю міграцій (migration), якщо тамблиці ще немає
            $this->connection->setAutoCommit(false);

            $this->createMigrationsTable();
            $this->connection->beginTransaction();
            // 2. Получить $appliedMigrations (міграції, які уже єсть в таблиці migrations)
            $appliedMigrations = $this->getAppliedMigrations();
            // 3. Получить $migrationFiles з папки міграцій
            $migrationFiles = $this->getMigrationFiles();
            // 4. Получить міграції для виконання
            $migrationsToApply = array_values(array_diff($migrationFiles, $appliedMigrations));
            $schema = new Schema();
            foreach ($migrationsToApply as $migration){
                $migrationInstance = require $this->migrationPath . "/$migration";

                // 5. Створити SQL-запит для міграцій, які ще не були виконані
                $migrationInstance->up($schema);

                // 6. Добавить міграції в базу даних
                $this->addMigration($migration);

            }

            // 7. Виконати SQL-запит
            $sqlArray = $schema->toSql($this->connection->getDatabasePlatform());
            foreach ($sqlArray as $sql){
                $this->connection->executeQuery($sql);
            }

            $this->connection->commit();

            return 0;

        } catch (\Throwable $e){
            $this->connection->rollBack();
            throw  $e;
        }

        $this->connection->setAutoCommit(true);
        return 0;

    }

    private function createMigrationsTable(): void
    {
        $schemaManager = $this->connection->createSchemaManager();

        if(!$schemaManager->tablesExist(self::MIGRATIONS_TABLE)){
            $schema = new Schema();
            $table = $schema->createTable(self::MIGRATIONS_TABLE);
            $table->addColumn('id', Types::INTEGER, [
                'unsigned' => true,
                'autoincrement' => true
            ]);
            $table->addColumn('migration', Types::STRING);
            $table->addColumn('created_at', Types::DATETIMETZ_IMMUTABLE, [
                'default' =>  'CURRENT_TIMESTAMP'
            ]);
            $table->setPrimaryKey(['id']);
            $sqlArray = $schema->toSql($this->connection->getDatabasePlatform());
            $this->connection->executeQuery($sqlArray[0]);
            echo 'Migrations table created' . PHP_EOL;
        }

    }

    private function getAppliedMigrations(): array
    {
        $queryBuilder = $this->connection->createQueryBuilder();
        return $queryBuilder
            ->select('migration')
            ->from(self::MIGRATIONS_TABLE)
            ->executeQuery()
            ->fetchFirstColumn();

    }

    private function getMigrationFiles(): array
    {
        $migrationFiles = scandir($this->migrationPath);
        $filteredFiles = array_filter($migrationFiles, function ($fileName) {
           return !in_array($fileName, ['.', '..']);
        });

        return array_values($filteredFiles);
    }

    private function addMigration(string $migration): void
    {
        $queryBuilder = $this->connection->createQueryBuilder();
        $queryBuilder->insert(self::MIGRATIONS_TABLE)
            ->values(['migration' => ':migration'])
            ->setParameter('migration', $migration)
            ->executeQuery();
    }
}