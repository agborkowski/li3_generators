<?php
/**
 * li3_generators views generator
 *
 * @package li3_generators
 * @subpackage Command
 */

namespace li3_generators\extensions\command\create;

use lithium\core\Libraries;
use lithium\util\Inflector;
use lithium\util\String;

/**
 * Generate a View file in the `--library` namespace
 *
 * `li3 create view Posts index`
 * `li3 create --library=li3_plugin view Posts index`
 *
 */
class View extends \li3_generators\extensions\command\Create
{
    /**
     * Types of assets
     *
     * @var array
     */
    protected static $view_files = array('index', 'add', 'edit', 'show', '_form');

    /**
     * Extension of view files
     *
     * @var string
     */
    protected $extension = "html.php";

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
     * Get the singular data variable that is sent down from controller methods.
     *
     * @param string $request
     * @return string
     */
    protected function _singular($request)
    {
        return Inflector::singularize(Inflector::camelize($request->action, false));
    }

    /**
     * Parse a template to find available variables specified in `{:name}`
     * format. Each variable
     * corresponds to a method in the sub command. For example, a `{:namespace}`
     * variable will
     * call the namespace method in the model command when `li3 create model
     * Post` is called.
     *
     * @return array
     */
    protected function _params()
    {
        $params = array();

        foreach(static::$view_files as $file) {
            $this->template = $file;

            $contents = $this->_template();

            if(empty($contents)) {
                return array();
            }

            preg_match_all('/(?:\{:(?P<params>[^}]+)\})/', $contents, $keys);

            if(!empty($keys['params'])) {
                $params = array_merge(array_values(array_unique($keys['params'])), $params);
            }
        }

        $params = array_unique($params);

        return $params;
    }

    /**
     * Returns the contents of the template.
     *
     * @return string
     */
    protected function _template($source = 'command.create.template')
    {
        $source .= $this->source ? ".{$this->source}" : ".default";
        $source .= '.views';

        $file = Libraries::locate($source, $this->template, array('filter' => false, 'type' => 'file', 'suffix' => '.txt'));

        if(!$file || is_array($file)) {
            return false;
        }

        return file_get_contents($file);
    }

    /**
     * Override the save method to handle view specific params.
     *
     * @param array $params
     * @return mixed
     */
    protected function _save(array $params = array())
    {
        $params['path'] = Inflector::underscore($this->request->action);
        $params['files'] = array('index', 'add', 'edit', 'show', '_form');

        if(isset(static::$_namespace_sufix) && !empty(static::$_namespace_sufix)) {
            $params['path'] = substr(static::$_namespace_sufix, 1) . DIRECTORY_SEPARATOR . $params['path'];
        }
        
        $dest_path = "{$this->_library['path']}/views/{$params['path']}";
        $directory = preg_replace('/\/|\\\/', DIRECTORY_SEPARATOR, $dest_path);

        if(!is_dir($directory)) {
            if(!mkdir($directory, 0755, true)) {
                return false;
            }
        }

        if(isset(static::$view_files) && !empty(static::$view_files)) {
            foreach(static::$view_files as $file) {
                $this->template = $file;

                $contents = $this->_template();
                $result = String::insert($contents, $params);

                $file = "{$file}.{$this->extension}";
                $file_path = $directory . DIRECTORY_SEPARATOR . $file;

                $file_name = $this->file_name($file_path);

                if(file_exists($file_path)) {
                    $prompt = "{:yellow}File{:end} {$file_name} {:yellow}already exists. Overwrite?{:end}";
                    $choices = array('y', 'n');

                    if($this->in($prompt, compact('choices')) != 'y') {
                        $this->out("{:yellow}File{:end} {$file_name} {:yellow}has been skipped.{:end}");

                        continue;
                    }
                }

                if(is_int(file_put_contents($file_path, $result))) {
                    $this->out("{:green}File{:end} {$file_name} {:green}has been created.{:end}");
                }
            }

            return true;
        }

        return false;
    }

}
?>