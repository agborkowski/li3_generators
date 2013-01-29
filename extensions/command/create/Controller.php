<?php
/**
 * li3_generators controllers generator
 *
 * @package li3_generators
 * @subpackage Command
 */

namespace li3_generators\extensions\command\create;

use lithium\util\Inflector;

/**
 * Generate a Controller class in the `--library` namespace
 *
 * `li3 create controller Posts`
 * `li3 create --library=li3_plugin controller Posts`
 *
 */
class Controller extends \li3_generators\extensions\command\Create
{

    /**
     * Get the fully-qualified model class that is used by the controller.
     *
     * @param string $request
     * @return string
     */
    protected function _use($request)
    {
        $request->params['command'] = 'model';

        $namespace = $this->_namespace($request) . $request->namespace_sufix . '\\' . $this->_model($request);

        return $namespace;
    }

    /**
     * Get the controller class name.
     *
     * @param string $request
     * @return string
     */
    protected function _class($request)
    {
        return $this->_name($request) . 'Controller';
    }

    /**
     * Returns the name of the controller class, minus `'Controller'`.
     *
     * @param string $request
     * @return string
     */
    protected function _name($request)
    {
        return Inflector::camelize($request->action);
    }

    /**
     * Get the plural variable used for data in controller methods.
     *
     * @param string $request
     * @return string
     */
    protected function _plural($request)
    {
        return Inflector::pluralize(Inflector::camelize($request->action, false));
    }

    /**
     * Get the model class used in controller methods.
     *
     * @param string $request
     * @return string
     */
    protected function _model($request)
    {
        return Inflector::camelize($request->action);
    }

    /**
     * Get the singular variable to use for data in controller methods.
     *
     * @param string $request
     * @return string
     */
    protected function _singular($request)
    {
        return Inflector::singularize(Inflector::camelize($request->action, false));
    }
}

?>