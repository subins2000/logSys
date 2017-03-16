<?php
class TestDBSetup extends PHPUnit_Framework_TestCase
{
    private $pdo = null;

    public function setUp()
    {
        try {
            $this->pdo = new PDO($GLOBALS['DB_DSN'], $GLOBALS['DB_USERNAME'], $GLOBALS['DB_PASSWORD'], array(
                \PDO::ATTR_PERSISTENT => true,
                \PDO::ATTR_ERRMODE    => \PDO::ERRMODE_EXCEPTION,
            ));
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
        }
    }

    public function testCreateTables()
    {
        if ($GLOBALS['DB_TYPE'] === 'sqlite') {
            $sql = file_get_contents(__DIR__ . '/../../sql/sqlite.sql');
            $this->pdo->exec($sql);

            $sth = $this->pdo->query("SELECT COUNT(1) FROM `sqlite_master` WHERE type='table' AND `name` LIKE 'users'");
            $this->assertEquals(1, $sth->fetchColumn());

            $sth = $this->pdo->query("SELECT COUNT(1) FROM `sqlite_master` WHERE type='table' AND `name` LIKE 'user_devices'");
            $this->assertEquals(1, $sth->fetchColumn());

            $sth = $this->pdo->query("SELECT COUNT(1) FROM `sqlite_master` WHERE type='table' AND `name` LIKE 'user_tokens'");
            $this->assertEquals(1, $sth->fetchColumn());
        } elseif ($GLOBALS['DB_TYPE'] === 'postgresql') {
            $sql = file_get_contents(__DIR__ . '/../../sql/postgresql.sql');
            $this->pdo->exec($sql);

            $sth = $this->pdo->query("SELECT * FROM pg_catalog.pg_tables WHERE tablename = 'users'");
            $this->assertEquals(1, $sth->rowCount());

            $sth = $this->pdo->query("SELECT * FROM pg_catalog.pg_tables WHERE tablename = 'user_devices'");
            $this->assertEquals(1, $sth->rowCount());

            $sth = $this->pdo->query("SELECT * FROM pg_catalog.pg_tables WHERE tablename = 'user_tokens'");
            $this->assertEquals(1, $sth->rowCount());
        } else {
            $sql = file_get_contents(__DIR__ . '/../../sql/mysql.sql');
            $this->pdo->exec($sql);

            $sth = $this->pdo->query("SHOW TABLES LIKE 'users'");
            $this->assertEquals(1, $sth->rowCount());

            $sth = $this->pdo->query("SHOW TABLES LIKE 'user_devices'");
            $this->assertEquals(1, $sth->rowCount());

            $sth = $this->pdo->query("SHOW TABLES LIKE 'user_tokens'");
            $this->assertEquals(1, $sth->rowCount());
        }
    }
}
