<?php
/**
 * li3_generators console dispatch configuration
 *
 * @author Mateusz Prażmowski http://li3.it
 * @package li3_generators
 */

use lithium\console\Dispatcher;
use lithium\core\Environment;

Dispatcher::applyFilter('run', function ($self, $params, $chain) {

    /**
     * Set command by aliases
     */
    switch($params['request']->argv[0]) {
        case 'd':
        case 'delete':
        case 'rm':
        case 'remove':
            $params['request']->argv[0] = 'destroy';
            break;
        case 'c':
        case 'g':
        case 'generate':
            $params['request']->argv[0] = 'create';
            break;
        case 'migrate':
        case 'migrations':
            $params['request']->argv[0] = 'migration';
            break;
    }

    return $chain->next($self, $params, $chain);
});

?>