<?php
/**
 * li3_generators create command
 *
 * @package li3_generators
 * @subpackage Command
 */

namespace li3_generators\extensions\command;

use lithium\core\Libraries;
use lithium\core\ClassNotFoundException;
use lithium\util\String;
use lithium\util\Inflector;

/**
 * {:heading}{:end}
 * The {:command}create{:end} command allows you to rapidly develop your models, views,
 * controllers, tests, mocks, assets and database migrations by generating
 * the minimum code necessary to test and run your application
 *
 * {:heading}EXAMPLE USAGE:{:end}
 *
 *      {:command}li3 create Posts{:end}                - generates full Posts scaffolding
 *      {:command}li3 create controller Posts{:end}     - generates only Posts controller
 *
 * {:heading}AVAILABLE COMMAND ACTIONS:{:end}
 *
 *      {:command}model{:end}                           - generates model file
 *      {:command}controller{:end}                      - generates controller file
 *      {:command}view{:end}                            - generates view files
 *      {:command}assets{:end}                          - generates assets files
 *      {:command}test{:end}                            - generates test files
 *      {:command}mock{:end}                            - generates mock files
 *      {:command}migration{:end}                       - generates empty migration
 *
 * {:heading}ALIASES FOR {:command}create{:end} {:heading}COMMAND:{:end}
 *
 *      {:command}li3 [create] [generate] [c] [g] [<command>]{:end}
 * {:heading}{:end}
 */
class Create extends \li3_generators\console\Command
{
    /**
     * Name of library to use
     *
     * @var string
     */
    public $library = null;

    /**
     * This is the name of the template from which generator gets the source files
     * Place own templates in `<library>\extensions\command\create\template\template_name`
     *
     * @var string
     */
    public $template = null;

    /**
     * The name of the template source directory use to generate the file
     *
     * @var string
     */
    protected $source = null;

    /**
     * This parameter allows to skipp execution of given command.
     *
     * @var string
     */
    public $skip = null;

    /**
     * Holds library data from `lithium\core\Libraries::get()`.
     *
     * @var array
     */
    protected $_library = array();

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
     * Run the create command. Takes `$command` and delegates to
     * `$command::$method`
     *
     * @param string $command
     * @return boolean
     */
    public function run($command = null)
    {
        if(!is_null($this->template)) {
            $this->source = $this->template;
        }

        if($command && !$this->request->args()) {
            return $this->_default($command);
        }

        $this->request->shift();

        $this->template = $command;

        if(!$command) {
            return false;
        }

        if($this->_execute($command)) {
            return true;
        }

        $this->error("{$command} could not be created.");

        return false;
    }

    /**
     * Execute the given sub-command for the current request.
     *
     * @param string $command The sub-command name. example: Model, Controller,
     * Test
     * @return boolean
     */
    protected function _execute($command)
    {
        try {
            if(!$class = $this->_instance($command)) {
                return false;
            }
        } catch(ClassNotFoundException $e) {
            return false;
        }

        $data = array();

        $params = $class->invokeMethod('_params');

        foreach($params as $i => $param) {
            $data[$param] = $class->invokeMethod("_{$param}", array($this->request));
        }

        if($message = $class->invokeMethod('_save', array($data))) {
            if(is_string($message)) {
                $this->out($message);
            }

            return true;
        }

        return false;
    }

    /**
     * Run through the default set. model, controller, test model, test
     * controller
     *
     * @param string $name class name to create
     * @return boolean
     */
    protected function _default($name)
    {
        $skip_commands = array();

        $commands = array(
            array('model', $name),
            array('controller', $name),
            array('view', $name),
            array('assets', $name),
            array('test', 'model', $name),
            array('test', 'controller', $name),
            array('mock', 'model', $name),
            array('mock', 'controller', $name),
            array('migration', $name)
        );

        if(isset($this->skip) && !is_null($this->skip)) {
            $skip_commands = preg_split('/[[:punct:]]|\s+/', $this->skip);
        }

        foreach($commands as $args) {
            $command = $this->request->params['command'] = array_shift($args);

            if(in_array($command, $skip_commands)) {
                $this->out("{:blue}Generating the {$command} has been skipped.{:end}");

                continue;
            }

            switch($command) {
                case 'migration': $this->template = 'advanced_migration'; break;
                default: $this->template = $command;
            }

            $this->request->params['action'] = array_shift($args);
            $this->request->params['args'] = $args;

            if(!$this->_execute($command)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get the namespace.
     *
     * @param string $request
     * @param array $options
     * @return string
     */
    protected function _namespace($request, $options = array())
    {
        $name = $request->command;
        $defaults = array('prefix' => $this->_library['prefix'], 'prepend' => null, 'spaces' => array('model' => 'models', 'view' => 'views', 'controller' => 'controllers', 'command' => 'extensions.command', 'adapter' => 'extensions.adapter', 'helper' => 'extensions.helper'));
        $options += $defaults;

        if(isset($options['spaces'][$name])) {
            $name = $options['spaces'][$name];
        }

        $namespace = str_replace('.', '\\', $options['prefix'] . $options['prepend'] . $name);

        return $namespace;
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
        $contents = $this->_template();

        if(empty($contents)) {
            return array();
        }

        preg_match_all('/(?:\{:(?P<params>[^}]+)\})/', $contents, $keys);

        if(!empty($keys['params'])) {
            $params = array_values(array_unique($keys['params']));

            return $params;
        }

        return array();
    }

    /**
     * Returns the contents of the template.
     *
     * @return string
     */
    protected function _template($source = 'command.create.template')
    {
        $source .= $this->source ? ".{$this->source}" : ".default";

        $file = Libraries::locate($source, $this->template, array('filter' => false, 'type' => 'file', 'suffix' => '.txt'));

        if(!$file || is_array($file)) {
            return false;
        }

        return file_get_contents($file);
    }

    /**
     * Get an instance of a sub-command
     *
     * @param string $name the name of the sub-command to instantiate
     * @param array $config
     * @return object;
     */
    protected function _instance($name, array $config = array())
    {
        if($class = Libraries::locate('command.create', Inflector::camelize($name))) {
            $this->request->params['template'] = $this->template;

            return new $class(array('request' => $this->request, 'classes' => $this->_classes));
        }

        return parent::_instance($name, $config);
    }

    /**
     * Save a template with the current params. Writes file to `Create::$path`.
     *
     * @param array $params
     * @return string A result string on success of writing the file. If any
     * errors occur along
     * the way such as missing information boolean false is returned.
     */
    protected function _save(array $params = array())
    {
        $defaults = array('namespace' => null, 'class' => null);
        $params += $defaults;

        if(empty($params['class']) || empty($this->_library['path'])) {
            return false;
        }

        $contents = $this->_template();

        $result = String::insert($contents, $params);
        $namespace = str_replace($this->_library['prefix'], '\\', $params['namespace']);
        $path = str_replace('\\', '/', "{$namespace}\\{$params['class']}");
        $path = $this->_library['path'] . stristr($path, '/');
        $file = str_replace('//', '/', "{$path}.php");
        $directory = dirname($file);
        $relative = str_replace($this->_library['path'] . '/', "", $file);

        if((!is_dir($directory)) && !mkdir($directory, 0755, true)) {
            return false;
        }

        if(file_exists($file)) {
            $prompt = "{:yellow}File{:end} {$relative} {:yellow}already exists. Overwrite?{:end}";
            $choices = array('y', 'n');
            
            if($this->in($prompt, compact('choices')) != 'y') {
                return "{:yellow}File{:end} {$relative} {:yellow}has been skipped.{:end}";
            }
        }

        if(file_put_contents($file, "<?php\n{$result}\n\n?>")) {
            return "{:green}File{:end} {$relative} {:green}has been created.{:end}";
        }

        return false;
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
    }

?>