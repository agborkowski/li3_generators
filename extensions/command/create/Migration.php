<?php
/**
 * li3_generators migrations generator alias
 *
 * @package li3_generators
 * @subpackage Command
 */

namespace li3_generators\extensions\command\create;

use li3_generators\extensions\command\Migration as MigrationCore;
use lithium\util\String;
use lithium\util\Inflector;

class Migration extends \li3_generators\extensions\command\Create
{
    /**
     * Get the migration advanced class name.
     *
     * @param string $request
     * @return string
     */
    protected function _advanced_class($request)
    {
        $class = 'Create' . Inflector::camelize($request->action) . 'Table';

        return $class;
    }

    /**
     * Get the migration class name.
     *
     * @param string $request
     * @return string
     */
    protected function _class($request)
    {
        $class = Inflector::camelize($request->action);

        return $class;
    }

    /**
     * Get the migration table name.
     *
     * @param string $request
     * @return string
     */
    protected function _table($request)
    {
        $table = strtolower($request->action);

        return $table;
    }

    /**
     * Save a migration.
     *
     * @param array $params
     * @return string A result string on success of writing the file. If any
     * errors occur along
     * the way such as missing information boolean false is returned.
     */
    protected function _save(array $params = array())
    {
        $contents = $this->_template();
        $template = String::insert($contents, $params);

        $Migration = new MigrationCore();

        $name = isset($params['advanced_class']) ? $params['advanced_class'] : $params['class'];

        return $Migration->create($name, $template, true);
    }
}

?>
