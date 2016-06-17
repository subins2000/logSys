<?php
class TestDBSetup extends PHPUnit_Framework_TestCase {

  private $pdo = null;

  public function setUp(){
    $this->pdo = new PDO($GLOBALS['DB_DSN'], $GLOBALS['DB_USERNAME'], $GLOBALS['DB_PASSWORD']);
  }
  
  public function testCreateTables(){
    $sql = file_get_contents(__DIR__ . "/../src/table.sql");
    $this->pdo->exec($sql);
    
    $sth = $this->pdo->query("SHOW TABLES LIKE 'users'");
    $this->assertEquals(1, $sth->rowCount());
    
    $sth = $this->pdo->query("SHOW TABLES LIKE 'user_devices'");
    $this->assertEquals(1, $sth->rowCount());
    
    $sth = $this->pdo->query("SHOW TABLES LIKE 'resetTokens'");
    $this->assertEquals(1, $sth->rowCount());
  }
  
}
