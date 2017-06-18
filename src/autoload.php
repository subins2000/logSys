<?php
/**
 * Francium classes autoloader
 */

spl_autoload_register(
	function ( $class ) {
		$file = __DIR__ . '/' . str_replace( '\\', '/', $class ) . '.php';

		if ( file_exists( $file ) ) {
			require $file;
		}
	}
);
