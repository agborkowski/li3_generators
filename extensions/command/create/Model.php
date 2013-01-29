<?php
/**
 * Lithium: the most rad php framework
 *
 * @copyright     Copyright 2012, Union of RAD (http://union-of-rad.org)
 * @license       http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace li3_generators\extensions\command\create;

use lithium\util\Inflector;

/**
 * Generate a Model class in the `--library` namespace
 *
 * `li3 create model Posts`
 * `li3 create --library=li3_plugin model Posts`
 *
 */
class Model extends \li3_generators\extensions\command\Create
{

    /**
     * Get the class name for the model.
     *
     * @param string $request
     * @return string
     */
    protected function _class($request)
    {
        // return Inflector::camelize(Inflector::pluralize($request->action));
        return Inflector::camelize($request->action);
    }

}
?>