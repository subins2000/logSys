<?php
require_once __DIR__ . '/../src/autoload.php';

if ( $GLOBALS['DB_TYPE'] === 'sqlite' ) {
	$GLOBALS['DB_SQLITE_PATH'] = tempnam( sys_get_temp_dir(), 'logSysSQLiteDB' );
	$GLOBALS['DB_DSN']         = 'sqlite:' . $GLOBALS['DB_SQLITE_PATH'];
} else if ( $GLOBALS['DB_TYPE'] === 'postgresql' ) {
	$GLOBALS['DB_DSN'] = 'pgsql:dbname=' . $GLOBALS['DB_NAME'] . ';host=' . $GLOBALS['DB_HOST'] . ';port=' . $GLOBALS['DB_PORT'];
} else {
	$GLOBALS['DB_DSN'] = 'mysql:dbname=' . $GLOBALS['DB_NAME'] . ';host=' . $GLOBALS['DB_HOST'] . ';port=' . $GLOBALS['DB_PORT'];
}

function setServerArray() {
	$_SERVER['SERVER_NAME'] = 'localhost';
	$_SERVER['SERVER_PORT'] = '80';
	$_SERVER['REQUEST_URI'] = '/index.php';
}
