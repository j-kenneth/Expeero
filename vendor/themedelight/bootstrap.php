<?php
if (!function_exists('themedelightAutoloader')) {
	function themedelightAutoloader($class){
		if (strpos($class, 'Td') === 0) {
			require dirname( __FILE__ ) . '/components/' . $class . '.php';
		}
	}
	spl_autoload_register('themedelightAutoloader');
}
