<?php
/**
 * li3_generators mocks generator
 *
 * @package li3_generators
 * @subpackage Command
 */

namespace li3_generators\extensions\command\create;

use lithium\util\Inflector;

/**
 * Generate a Mock that extends the name of the given class in the `--library` namespace.
 *
 * `li3 create mock model Posts`
 * `li3 create --library=li3_plugin mock model Posts`
 *
 */
class Mock extends \li3_generators\extensions\command\Create
{

    /**
     * Get the namespace for the mock.
     *
     * @param string $request
     * @param array|string $options
     * @return string
     */
    protected function _namespace($request, $options = array())
    {
        $request->params['command'] = $request->action;

        $namespace = parent::_namespace($request, array('prepend' => 'tests.mocks.'));

        return $namespace;
    }

    /**
     * Get the parent for the mock.
     *
     * @param string $request
     * @return string
     */
    protected function _parent($request)
    {
        $namespace = parent::_namespace($request);

        $class = $request->action;

        $parent = "\\{$namespace}\\{$class}";

        return $parent;
    }

    /**
     * Get the class name for the mock.
     *
     * @param string $request
     * @return string
     */
    protected function _class($request)
    {
        $type = $request->action;
        $name = $request->args();

        if($command = $this->_instance($type)) {
            $request->params['action'] = $name;
            $name = $command->invokeMethod('_class', array($request));
        }

        return "Mock{$name}";
    }

    /**
     * Get the methods for the mock to override
     *
     * @param string $request
     * @return string
     */
    protected function _methods($request)
    {
        return null;
    }
}

?>