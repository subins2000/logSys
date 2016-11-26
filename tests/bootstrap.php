<?php
require_once __DIR__ . "/../src/LS.php";

if($GLOBALS['DB_TYPE'] === "sqlite"){
  $GLOBALS['DB_SQLITE_PATH'] = tempnam(sys_get_temp_dir(), 'logSysSQLiteDB');
  $GLOBALS['DB_DSN'] = "sqlite:" . $GLOBALS['DB_SQLITE_PATH'];
}else{
  $GLOBALS['DB_DSN'] = "mysql:dbname=". $GLOBALS["DB_NAME"] .";host=" . $GLOBALS["DB_HOST"];
}
