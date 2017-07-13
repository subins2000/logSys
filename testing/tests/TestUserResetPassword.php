<?php

use Fr\LS;

class TestUserResetPassword extends PHPUnit_Framework_TestCase
{
    private static $pdo = null;

    /**
     * @var LS
     */
    private $LS;

    /**
     * @var array Emails sent
     */
    private $emails = array();

    public function setUp()
    {
        self::$pdo = new PDO($GLOBALS['DB_DSN'], $GLOBALS['DB_USERNAME'], $GLOBALS['DB_PASSWORD'], array(
            \PDO::ATTR_PERSISTENT => true,
            \PDO::ATTR_ERRMODE    => \PDO::ERRMODE_EXCEPTION,
        ));

        $that = $this;

        $config = array(
            'db' => array(
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

            'basic' => array(
                'email_callback' => function ($LS, $email, $subject, $body) use (&$that) {
                    $that->emails[] = array(
                        'to'      => $email,
                        'subject' => $subject,
                        'body'    => $body,
                    );
                },
            ),
        );

        if ($GLOBALS['DB_TYPE'] === 'sqlite') {
            $config['db']['sqlite_path'] = $GLOBALS['DB_SQLITE_PATH'];
        }

        $this->LS = new LS($config);
    }

    public function sendToken()
    {
        $that        = $this;
        $pdo         = self::$pdo;
        $token_in_db = null;

        setServerArray();

        $this->LS->sendResetPasswordToken(1, function ($token) use (&$that, &$pdo, &$token_in_db) {
            $sth = $pdo->prepare('SELECT token FROM user_tokens WHERE uid = ?');
            $sth->execute(array(1));

            while ($r = $sth->fetch()) {
                if ($r['token'] === $token) {
                    $foundToken = true;
                }
            }

            $that->assertEquals(true, isset($foundToken));

            $token_in_db = $token;

            // email body should only be the token
            return $token;
        });

        return $token_in_db;
    }

    public function testSendToken()
    {
        $token = $this->sendToken();

        $this->assertEquals('test1@test.com', $this->emails[0]['to']);
        $this->assertEquals($token, $this->emails[0]['body']);

        $this->assertEquals(true, $this->LS->verifyResetPasswordToken($token));
    }

    public function testRemoveToken()
    {
        $token = $this->sendToken();

        $this->assertEquals(true, $this->LS->removeToken($this->emails[0]['body']));

        $sth = self::$pdo->query('SELECT COUNT(1) FROM user_tokens');

        $this->assertEquals(1, $sth->fetchColumn());
    }
}
