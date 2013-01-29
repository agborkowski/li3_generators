<?php
/**
 * li3_generators destroy command
 *
 * @package li3_generators
 * @subpackage Command
 */

namespace li3_generators\extensions\command;

use lithium\core\Libraries;

/**
 * {:heading}{:end}
 * The {:command}destroy{:end} command allows you to rapidly destroy your models, views,
 * controllers, tests, mocks and assets
 *
 * {:heading}EXAMPLE USAGE:{:end}
 *
 *      {:command}li3 destroy Posts{:end}               - destroys full Posts scaffolding
 *      {:command}li3 destroy controller Posts{:end}    - destroys only Posts controller
 *
 * {:heading}AVAILABLE COMMAND ACTIONS:{:end}
 *
 *      {:command}model{:end}                           - destroys model file
 *      {:command}controller{:end}                      - destroys controller file
 *      {:command}view{:end}                            - destroys view files and directory
 *      {:command}assets{:end}                          - destroys assets files
 *      {:command}test{:end}                            - destroys test files
 *      {:command}mock{:end}                            - destroys mock files
 *
 * {:heading}ALIASES FOR {:command}destroy{:end} {:heading}COMMAND:{:end}
 *
 *      {:command}li3 [destroy] [delete] [remove] [rm] [d] [<command>]{:end}
 * {:heading}{:end}
 */
class Destroy extends \li3_generators\console\Command
{
    /**
     * Array holds the paths to the file to delete
     *
     * @var array
     */
    protected static $_paths = array(
        'model' => 'models/%s.php',
        'controller' => 'controllers/%sController.php',
        'test' => array('tests/cases/models/%sTest.php', 'tests/cases/controllers/%sControllerTest.php'),
        'mock' => array('tests/mocks/models/Mock%s.php', 'tests/mocks/controllers/Mock%sController.php'),
        'view' => 'views/%s',
        'assets' => array('webroot/css/application/%s.css', 'webroot/js/application/%s.js')
    );

    /**
     * Array holds special methods which are must be called on file name variable
     *
     * @var array
     */
    protected static $_filename_rules = array(
        'model' => array('strtolower', 'ucfirst'),
        'controller' => array('strtolower', 'ucfirst'),
        'test' => array('strtolower', 'ucfirst'),
        'mock' => array('strtolower', 'ucfirst'),
        'view' => array('strtolower'),
        'assets' => array('strtolower'),
    );

    /**
     * Name of library to use
     *
     * @var string
     */
    public $library = null;

    /**
     * Holds library data from `lithium\core\Libraries::get()`.
     *
     * @var array
     */
    protected $_library = array();

    /**
     * This parameter allows to skipp execution of given command.
     *
     * @var string
     */
    public $skip = null;

    /**
     * Class initializer. Parses template and sets up params that need to be
     * filled.
     *
     * @return void
     */
    protected function _init()
    {
        parent::_init();

        $this->library = $this->library ? : true;

        $defaults = array('prefix' => null, 'path' => null);

        $this->_library = (array)Libraries::get($this->library) + $defaults;
    }

    /**
     * Run the destroy command. Takes `$command` and delegates to
     * `$command::$method`
     *
     * @param string $command
     *
     * @return boolean
     */
    public function run($command = null)
    {
        $prompt = "{:yellow}Are you sure you want to do this?{:end}";
        $choices = array("y", "n");

        if($this->in($prompt, compact('choices')) != "y") {
            $this->out("{:yellow}The action was stopped.{:end}");

            return false;
        }

        if($command && !$this->request->args()) {
            return $this->_default($command);
        }

        $this->request->shift();

        if(!$command) {
            return false;
        }

        if(isset(static::$_paths[$command]) && !empty(static::$_paths[$command])) {
            $paths = static::$_paths[$command];

            if(!is_array($paths)) {
                $paths = array($paths);
            }

            $prepared_name = $this->request->action;

            if(isset(static::$_filename_rules[$command]) && !empty(static::$_filename_rules[$command])) {
                foreach(static::$_filename_rules[$command] as $function) {
                    if(function_exists($function)) {
                        $prepared_name = call_user_func($function, $prepared_name);
                    }
                }
            }

            foreach($paths as $path) {
                if(!$this->_execute($prepared_name, $path)) {
                    return false;
                }
            }

            return true;
        }

        $this->error("{:bold_red}{$command} could not be deleted.{:end}");

        return false;
    }

    /**
     * Destroy all scaffold files from default
     *
     * @param string $name class name to destroy
     *
     * @return boolean
     */
    protected function _default($name)
    {
        $skip_commands = array();

        if(isset($this->skip) && !is_null($this->skip)) {
            $skip_commands = preg_split('/[[:punct:]]|\s+/', $this->skip);
        }

        foreach(static::$_paths as $type => $paths) {
            if(in_array($type, $skip_commands)) {
                $this->out("{:yellow}Destroying the {$type} has been skipped.{:end}");

                continue;
            }

            if(!is_array($paths)) {
                $paths = array($paths);
            }

            $prepared_name = $name;

            if(isset(static::$_filename_rules[$type]) && !empty(static::$_filename_rules[$type])) {
                foreach(static::$_filename_rules[$type] as $function) {
                    if(function_exists($function)) {
                        $prepared_name = call_user_func($function, $prepared_name);
                    }
                }
            }

            foreach($paths as $path) {
                if(!$this->_execute($prepared_name, $path)) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Execute the given sub-command for the current request.
     *
     * @param string $name The name of file/directory to delete
     * @param string $path Path to file/directory to delete
     *
     * @return boolean
     */
    protected function _execute($name, $path)
    {
        $path = "{$this->_library['path']}/{$path}";

        $file_path = sprintf($path, $name);
        $file_path = preg_replace('/\/|\\\/', DIRECTORY_SEPARATOR, $file_path);

        $file_name = $this->file_name($file_path);

        if(file_exists($file_path)) {
            if(is_dir($file_path)) {
                $this->_rmdir($file_path);
            } else if(is_file($file_path)) {
                if(unlink($file_path)) {
                    $message = "{:red}File{:end} {$file_name} {:red}has been deleted.{:end}";
                } else {
                    $message = "{:red}File{:end} {$file_name} {:red}hasn't been deleted!{:end}";
                }
            }
        } else {
            if(false !== strpos($file_name, '.')) {
                $message = "{:yellow}File{:end} {$file_name} {:yellow}doesn't' exists. Skipped.{:end}";
            } else {
                $message = "{:yellow}Directory{:end} {$file_name} {:yellow}doesn't' exists. Skipped.{:end}";
            }
        }

        if(isset($message) && !empty($message)) {
            $this->out($message);
        }

        return true;
    }

    /**
     * Method returns short path to file by exclude library path
     *
     * @param $path Full path to file
     *
     * @return mixed
     */
    protected function file_name($path)
    {
        $file_name = str_replace("{$this->_library['path']}/", '', $path);

        return $file_name;
    }

    /**
     * Recursive remove directories, and files from path
     *
     * @param string $path Path to directory to delete
     *
     * @return boolean
     */
    protected function _rmdir($dir)
    {
        if (is_dir($dir)) {
            $files = scandir($dir, SCANDIR_SORT_DESCENDING);

            array_pop($files);
            array_pop($files);

            foreach($files as $file) {
                $file_path = $dir . DIRECTORY_SEPARATOR . $file;

                $file_name = $this->file_name($file_path);

                if(is_file($file_path)) {
                    if(unlink($file_path)) {
                        $message = "{:red}File{:end} {$file_name} {:red}has been deleted.{:end}";
                    } else {
                        $message = "{:red}File{:end} {$file_name} {:red}hasn't been deleted!{:end}";
                    }
                } else if(is_dir($file_path)) {
                    $this->_rmdir($file_path);
                }

                if(isset($message) && !empty($message)) {
                    $this->out($message);
                    unset($message);
                }
            }

            $dir_name = $this->file_name($dir);

            if(rmdir($dir)) {
                $message = "{:red}Directory{:end} {$dir_name} {:red}has been deleted.{:end}";
            } else {
                $message = "{:red}Directory{:end} {$dir_name} {:red}hasn't been deleted!{:end}";
            }

            if(isset($message) && !empty($message)) {
                $this->out($message);
                unset($message);
            }

        }

        return true;
    }

}

?>