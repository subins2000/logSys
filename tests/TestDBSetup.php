<?php
class TestDBSetup extends PHPUnit_Framework_TestCase {

  private $pdo = null;

  public function setUp(){
    $this->pdo = new PDO($GLOBALS['DB_DSN'], $GLOBALS['DB_USERNAME'], $GLOBALS['DB_PASSWORD'], array(
      \PDO::ATTR_PERSISTENT => true,
      \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION
    ));
  }

  public function testCreateTables(){
    if($GLOBALS['DB_TYPE'] === "sqlite"){
      $sql = file_get_contents(__DIR__ . "/../src/sqlite.sql");
      $this->pdo->exec($sql);

      $sth = $this->pdo->query("SELECT COUNT(1) FROM `sqlite_master` WHERE type='table' AND `name` LIKE 'users'");
      $this->assertEquals(1, $sth->fetchColumn());

      $sth = $this->pdo->query("SELECT COUNT(1) FROM `sqlite_master` WHERE type='table' AND `name` LIKE 'user_devices'");
      $this->assertEquals(1, $sth->fetchColumn());

      $sth = $this->pdo->query("SELECT COUNT(1) FROM `sqlite_master` WHERE type='table' AND `name` LIKE 'resetTokens'");
      $this->assertEquals(1, $sth->fetchColumn());
    }else{
      $sql = file_get_contents(__DIR__ . "/../src/mysql.sql");
      $this->pdo->exec($sql);

      $sth = $this->pdo->query("SHOW TABLES LIKE 'users'");
      $this->assertEquals(1, $sth->rowCount());

      $sth = $this->pdo->query("SHOW TABLES LIKE 'user_devices'");
      $this->assertEquals(1, $sth->rowCount());

      $sth = $this->pdo->query("SHOW TABLES LIKE 'resetTokens'");
      $this->assertEquals(1, $sth->rowCount());
    }
  }

}
