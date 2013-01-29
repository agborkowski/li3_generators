<?php
/*
  Generator for migrations.
  Usage: php generate.php <migration name>
  Call with no arguments to see usage info.
*/

if(!defined('RUCKUSING_BASE')) {
    define('RUCKUSING_BASE', realpath(dirname(__FILE__)));
}

require_once RUCKUSING_BASE . '/config/config.inc.php';
require_once RUCKUSING_BASE . '/lib/classes/util/class.Ruckusing_NamingUtil.php';
require_once RUCKUSING_BASE . '/lib/classes/util/class.Ruckusing_MigratorUtil.php';

$args = parse_args($argv);

$args['by_generator'] = $by_generator;

return main($args);

//-------------------

/*
  Parse command line arguments.
*/
function parse_args($argv)
{
    $num_args = count($argv);

    if($num_args < 2) {
        print_help(true);
    }

    $migration_name = $argv[1];
    $template = isset($argv[2]) ? $argv[2] : null;

    return array('name' => $migration_name, 'template' => $template);
}

/*
  Print a usage scenario for this script.
  Optionally take a boolean on whether to immediately die or not.
*/
function print_help($exit = false)
{
    echo "\nusage: php generate.php <migration name>\n\n";
    echo "\tWhere <migration name> is a descriptive name of the migration, joined with underscores.\n";
    echo "\tExamples: add_index_to_users | create_users_table | remove_pending_users\n\n";
    if($exit) {
        exit;
    }
}

function main($args)
{
    //input sanity check
    if(!is_array($args) || (is_array($args) && !array_key_exists('name', $args))) {
        print_help(true);
    }

    $migration_name = $args['name'];
    $template = $args['template'];
    $by_generator = $args['by_generator'];

    //clear any filesystem stats cache
    clearstatcache();

    //check to make sure our migration directory exists
    if(!is_dir(RUCKUSING_MIGRATION_DIR)) {
        die_with_error("ERROR: migration directory '" . RUCKUSING_MIGRATION_DIR . "' does not exist. Specify MIGRATION_DIR in config/config.inc.php and try again.");
    }

    //generate a complete migration file
    $next_version = Ruckusing_MigratorUtil::generate_timestamp();
    $class = Ruckusing_NamingUtil::camelcase($migration_name);
    $file_name = $next_version . '_' . $class . '.php';
    $full_path = realpath(RUCKUSING_MIGRATION_DIR) . '/' . $file_name;
    $template_str = get_template($class, $template);

    //check to make sure our destination directory is writable
    if(!is_writable(RUCKUSING_MIGRATION_DIR . '/')) {
        die_with_error("ERROR: migration directory '" . RUCKUSING_MIGRATION_DIR . "' is not writable by the current user. Check permissions and try again.");
    }

    //write it out!
    $file_result = file_put_contents($full_path, $template_str);

    if($file_result === false) {
        die_with_error("Error writing to migrations directory/file. Do you have sufficient privileges?");
    } else {
        if(!$by_generator) {
            echo "\nCreated migration: {$file_name}\n\n";
            return true;
        } else {
            return \li3_actionpack\libraries\Colorizer::fg("{:green}Migration{:end} {$file_name} {:green}has been created.{:end}");
        }
    }
}

function die_with_error($str)
{
    die("\n{$str}\n");
}

function get_template($class, $template = null)
{
    if(is_null($template)) {
        $template = <<<TPL
<?php
/**
 * Automaticly generated migration
 */

class $class extends Ruckusing_BaseMigration
{

\tpublic function up()
\t{
\t\t
\t}

\tpublic function down()
\t{
\t\t
\t}

}
?>
TPL;

    } else {
        $template = "<?php\n{$template}\n\n?>";
    }

    return $template;
}

?>
