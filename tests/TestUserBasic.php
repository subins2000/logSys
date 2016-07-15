<?php
class TestUserBasic extends PHPUnit_Framework_TestCase {

  private static $pdo = null;

  public function setUp(){
    self::$pdo = new PDO($GLOBALS['DB_DSN'], $GLOBALS['DB_USERNAME'], $GLOBALS['DB_PASSWORD']);
    \Fr\LS::config(array(
      "db" => array(
        "host" => $GLOBALS['DB_HOST'],
        "port" => $GLOBALS['DB_PORT'],
        "username" => $GLOBALS['DB_USERNAME'],
        "password" => $GLOBALS['DB_PASSWORD'],
        "name" => $GLOBALS['DB_DBNAME']
      ),
      "features" => array(
        "auto_init" => false,
        "start_session" => false
      )
    ));
  }
  
  public function testUserRegister(){
    $info = array(
      "email" => "test@test.com",
      "name" => "ABC",
      "created" => date("Y-m-d H:i:s")
    );
    \Fr\LS::register("test", "abc", $info);
    
    $sth = self::$pdo->query("SELECT * FROM `users` WHERE `id` = '1'");
    $r = $sth->fetch(\PDO::FETCH_ASSOC);
    
    $this->assertEquals("test", $r["username"]);
    $this->assertEquals("test@test.com", $r["email"]);
    $this->assertEquals($info["name"], $r["name"]);
    $this->assertEquals($info["created"], $r["created"]);
  }
  
  public function testUserInfo(){
    $user = \Fr\LS::getUser("*", 1);
    
    $sth = self::$pdo->query("SELECT * FROM `users` WHERE `id` = '1'");
    $r = $sth->fetch(\PDO::FETCH_ASSOC);
    
    $this->assertEquals($r["username"], $user["username"]);
    $this->assertEquals($r["email"], $user["email"]);
    $this->assertEquals($r["name"], $user["name"]);
    $this->assertEquals($r["created"], $user["created"]);
  }
  
  public static function tearDownAfterClass(){
    self::$pdo->exec("DROP TABLE `users`;DROP TABLE `user_devices`;DROP TABLE `resetTokens`;");
  }
  
}
