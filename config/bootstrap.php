<?php
/**
 * li3_generators bootstrap config
 *
 * @author Mateusz Prażmowski http://li3.it
 * @package li3_generators
 * @subpackage Config
 */

/**
 * This is a definition of constant which hold path to the ruckusing migrations library
 */
defined('RUCKUSING_BASE') || define('RUCKUSING_BASE', dirname(__DIR__) . '/libraries/ruckusing-migrations');

/**
 * This is a definition of constant which hold path to the db directory wchich holds all migrations
 */
defined('RUCKUSING_DB_DIR') || define('RUCKUSING_DB_DIR', LITHIUM_APP_PATH . '/db');

/**
 * This file configures console filters and settings, specifically output behavior and coloring.
 */
if (PHP_SAPI === 'cli') {
    require __DIR__ . '/bootstrap/console.php';
}

?>
