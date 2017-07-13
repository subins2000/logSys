<?php

use Fr\LS;

class TestUserBasic extends PHPUnit_Framework_TestCase
{
    private static $pdo = null;

    /**
     * @var LS
     */
    private $LS;

    public function setUp()
    {
        self::$pdo = new PDO($GLOBALS['DB_DSN'], $GLOBALS['DB_USERNAME'], $GLOBALS['DB_PASSWORD'], array(
            \PDO::ATTR_PERSISTENT => true,
            \PDO::ATTR_ERRMODE    => \PDO::ERRMODE_EXCEPTION,
        ));

        $config = array(
            'db'       => array(
                'type'     => $GLOBALS['DB_TYPE'],
                'host'     => isset($GLOBALS['DB_HOST']) ? $GLOBALS['DB_HOST'] : null,
                'port'     => isset($GLOBALS['DB_PORT']) ? $GLOBALS['DB_PORT'] : null,
                'username' => $GLOBALS['DB_USERNAME'],
                'password' => $GLOBALS['DB_PASSWORD'],
                'name'     => $GLOBALS['DB_NAME'],
            ),
            'features' => array(
                'auto_init' => false,
                'run_http'  => false,
            ),
        );

        if ($GLOBALS['DB_TYPE'] === 'sqlite') {
            $config['db']['sqlite_path'] = $GLOBALS['DB_SQLITE_PATH'];
        }

        $this->LS = new LS($config);
    }

    public function testUserRegister()
    {
        $info = array(
            'email'   => 'test@test.com',
            'name'    => 'ABC',
            'created' => date('Y-m-d H:i:s'),
        );
        $this->LS->register('test', 'abc', $info);

        $sth = self::$pdo->query("SELECT * FROM users WHERE id = '1'");
        $r   = $sth->fetch(\PDO::FETCH_ASSOC);

        $this->assertEquals('test', $r['username']);
        $this->assertEquals($info['email'], $r['email']);
        $this->assertEquals($info['name'], $r['name']);
        $this->assertEquals($info['created'], $r['created']);
    }

    public function testUserExists()
    {
        $this->assertEquals(true, $this->LS->userExists('test'));
        $this->assertEquals(true, $this->LS->userExists('test@test.com'));

        $this->assertEquals(true, $this->LS->userIDExists('1'));
        $this->assertEquals(true, $this->LS->userIDExists(1));
        $this->assertEquals(false, $this->LS->userIDExists('0'));
    }

    public function testUserLogin()
    {
        // Login with password
        $this->assertNotEquals(false, $this->LS->login('test', 'abc', false, false));

        // Login without password and get user ID
        $this->assertEquals('1', $this->LS->login('test', false, false, false));
    }

    public function testUserInfo()
    {
        $user = $this->LS->getUser('*', 1);

        $sth = self::$pdo->query("SELECT * FROM users WHERE id = '1'");
        $r   = $sth->fetch(\PDO::FETCH_ASSOC);

        $this->assertEquals($r['username'], $user['username']);
        $this->assertEquals($r['email'], $user['email']);
        $this->assertEquals($r['name'], $user['name']);
        $this->assertEquals($r['created'], $user['created']);
    }

    public function testUpdateUserInfo()
    {
        $email = $this->LS->getUser('email', 1);
        $this->assertEquals('test@test.com', $email);

        $this->LS->updateUser(array(
            'email' => 'test1@test.com',
        ), 1);

        $email = $this->LS->getUser('email', 1);
        $this->assertEquals('test1@test.com', $email);
    }

    public function testChangePassword()
    {
        $this->assertNotEquals(false, $this->LS->login('test', 'abc', false, false));

        $this->LS->changePassword('xyz', 1);

        $this->assertNotEquals(false, $this->LS->login('test', 'xyz', false, false));
    }
}
