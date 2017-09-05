<?php

use Fr\LS;
use Fr\LS\TwoStepLogin;

class TestUserTwoStepLogin extends PHPUnit_Framework_TestCase
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

            'two_step_login' => array(
                'send_callback' => function () {
                },
            ),
        );

        if ($GLOBALS['DB_TYPE'] === 'sqlite') {
            $config['db']['sqlite_path'] = $GLOBALS['DB_SQLITE_PATH'];
        }

        $this->LS = new LS($config);
    }

    public function testUserLogin()
    {
        /**
         * Login with incorrect password
         */
        try {
            $this->LS->twoStepLogin('test', 'abc', true, false);
        } catch (TwoStepLogin $TSL) {
            $this->assertEquals('login_fail', $TSL->getStatus());
        }

        /**
         * Login with correct password
         */
        try {
            $this->LS->twoStepLogin('test', 'xyz', true, false);
        } catch (TwoStepLogin $TSL) {
            $this->assertEquals('enter_token_form', $TSL->getStatus());
            $this->assertEquals(true, $TSL->getOption('remember_me'));
        }
    }

    public static function tearDownAfterClass()
    {
        self::$pdo->exec('DROP TABLE users;DROP TABLE user_devices;DROP TABLE user_tokens;');
    }
}
