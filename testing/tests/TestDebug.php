<?php
class TestDebug extends PHPUnit_Framework_TestCase
{
    private static $pdo = null;
    private static $log_file;

    public function setUp()
    {
        self::$pdo = new PDO($GLOBALS['DB_DSN'], $GLOBALS['DB_USERNAME'], $GLOBALS['DB_PASSWORD'], array(
            \PDO::ATTR_PERSISTENT => true,
            \PDO::ATTR_ERRMODE    => \PDO::ERRMODE_EXCEPTION,
        ));

        self::$log_file = __DIR__ . '/logSys.log';
    }

    public function testLogFileExists()
    {
        $this->assertEquals(file_exists(self::$log_file), false);

        $config = array(
            'db'       => array(
                'type'        => $GLOBALS['DB_TYPE'],
                'host'        => null,
                'port'        => 0,
                'sqlite_path' => '/file_do_not_exist',
            ),
            'features' => array(
                'auto_init' => false,
                'run_http'  => false,
            ),
            'debug'    => array(
                'enable'   => true,
                'log_file' => self::$log_file,
            ),
        );

        new \Fr\LS($config);

        $this->assertEquals(true, file_exists(self::$log_file));
        $this->assertContains('Could not connect to database', file_get_contents(self::$log_file));
    }

    public static function tearDownAfterClass()
    {
        if (file_exists(self::$log_file)) {
            unlink(self::$log_file);
        }
    }
}
