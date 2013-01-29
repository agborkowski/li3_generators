<?php
/**
 * li3_generators migration command
 *
 * @package li3_generators
 * @subpackage Command
 */

namespace li3_generators\extensions\command;

use \lithium\data\Connections;
use \lithium\core\Environment;

require RUCKUSING_BASE . '/lib/classes/util/class.Ruckusing_Logger.php';
require RUCKUSING_BASE . '/lib/classes/class.Ruckusing_FrameworkRunner.php';

/**
 * {:heading}{:end}
 * The {:command}migration{:end} command  allows you to create database migration files
 * Created migrations are executed using the ruckusing-migrations engine
 *
 * {:heading}EXAMPLE USAGE:{:end}
 *
 *      {:command}li3 migration generate <name>{:end}                       - generates <name> migration. It's not recommended.
 *                                                            (Better to use li3 create migration <name>)
 *      {:command}li3 migration db:setup{:end}                              - initializing the database for migration support
 *      {:command}li3 migration db:migrate{:end}                            - run database migration
 *      {:command}li3 migration db:migrate VERSION=20101006114707{:end}     - run database migration with specified version.
 *                                                            (Version can go up or down)
 *      {:command}li3 migration db:migrate VERSION=-2{:end}                 - run the database migration with a specific version of two previously.
 *      {:command}li3 migration db:version{:end}                            - determing the current DB version
 *      {:command}li3 migration db:status{:end}                             - getting the current state of migrations
 *      {:command}li3 migration db:schema{:end}                             - dumping the current schema to text file
 *
 * {:heading}ALIASES FOR {:command}migration{:end} {:heading}COMMAND:{:end}
 *
 *      {:command}li3 [migration] [migrate] [migrations] [<command>]{:end}
 * {:heading}{:end}
 */
class Migration extends \li3_generators\console\Command
{
    private $ruckusing_db_config;

    protected $connection = 'default';

    public function _init()
    {
        parent::_init();

        if($connection = Connections::get($this->connection)) {
            $this->ruckusing_db_config = array(
                $this->connection => array(
                    'type'      => strtolower($connection->_config['adapter']),
                    'host'      => $connection->_config['host'],
                    'port'      => 3306,
                    'database'  => $connection->_config['database'],
                    'user'      => $connection->_config['login'],
                    'password'  => $connection->_config['password'],
                )
            );
        }
    }

    protected function run($command = null)
    {
        $env = Environment::get();
        $this->out("Using Environment: '$env' and Connection: '{$this->connection}'.");

        if(!$this->ruckusing_db_config) {
            $this->error('Error: cannot cannot connect to database');

            return;
        }

        if(is_null($command)) {
            $this->help();

            return;
        }

        $args = $this->_config['request']->args;

        $this->execute($command, $args);
    }

    protected function help()
    {
        $message = "Usage: li3 migration [command] [args]
Commands:
  help: Displays this text.
  generate: Generates migration file. Requires a migration description argument
            either as a an underscore seperated words or quoted words.
  other migration commands: All ruckusing-migrations' tasks. Listed in
                            https://github.com/ruckus/ruckusing-migrations/wiki/Available-Tasks
";
        $this->out($message);

        return true;
    }

    protected function generate($description = null)
    {
        $this->create($description);
    }

    public function create($description = null, $tempalte = null, $by_generator = false)
    {
        if(is_null($description)) {
            $this->error("Please Specify a migration description.\nThe description can either be a set of underscore_seperated_words or \"quoted words\".");

            return false;
        }

        $argv = array('', $description, $tempalte);

        $result = include RUCKUSING_BASE . '/generate.php';

        return $result;
    }

    protected function execute($command, $args)
    {
        $argv = array_merge(array(''), array($command), $args, array("ENV=$this->connection"));

        $main = new \Ruckusing_FrameworkRunner($this->ruckusing_db_config, $argv);
        $main->execute();
    }

}
?>
