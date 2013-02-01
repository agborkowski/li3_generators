# Code generator for lithium PHP framework [http://lithify.me](http://lithify.me)

With this module you can quickly build scaffold of your application, make a database migrations,
or if you need to - delete them. Everything quickly and easy using few commands.

##Installation
###1st way - standard instalation
1. Clone/Download the plugin into your ``libraries`` directory.
2. Tell your app to load the plugin by adding the following to your app's ``config/bootstrap/libraries.php``:

    Libraries::add('li3_generators');

###2nd way - using composer
1. Add following line to your composer file in require section

        "prazmok666/li3_generators": "dev-master".

2. Run composer install/update command.
3. Tell your app to load the plugin by adding the following to your app's ``config/bootstrap/libraries.php``:

        Libraries::add('li3_generators', array('path' => 'path_to_composer_vendor_directory/prazmok666/li3_generators'));

##Usage

> For easy use of the generator, I recommend to add an alias to the li3 command
> [Here at bottom of the page you can see how to do this](http://lithify.me/docs/manual/getting-started/installation.wiki).

There are three basic commands:

    $ li3 create
    $ li3 destroy
    $ li3 migration

There are a few more aliases for these commands:

    1. For create command    - c, g, generate (li3 g Posts)
    2. For destroy command   - d, delete, rm, remove (li3 d Posts)
    3. For migration command - migrate, migrations

For more information type
    $ li3
or
    $ li3 help <command>

For example:
    $ li3 g Posts
    $ li3 d Posts

## Available command actions

### Create command actions:

    $ li3 create <name>                  - creates full scaffolding and migration file

    $ li3 create model <name>            - creates model file
    $ li3 create controller <name>       - creates controller file
    $ li3 create view <name>             - creates view directory and files
    $ li3 create test controller <name>  - creates test file for controller
    $ li3 create test model <name>       - creates test file for model
    $ li3 create mock controller <name>  - creates mock file for controller
    $ li3 create mock model <name>       - creates mock file for model
    $ li3 create assets <name>           - creates assets js and css files
    $ li3 create migration <name>        - creates database migration empty file

### Destroy command actions:

    $ li3 destroy <name>                  - destroys full scaffolding

    $ li3 destroy model <name>            - destroys model file
    $ li3 destroy controller <name>       - destroys controller file
    $ li3 destroy view <name>             - destroys view directory and files
    $ li3 destroy test <name>             - destroys test file for model
    $ li3 destroy mock <name>             - destroys mock file for controller
    $ li3 destroy assets <name>           - destroys assets js and css files

###Migration command actions

Migrations ar based on `ruckusing-migrations` plugin

    $ li3 migration generate <name>                     - generates migration file. (It's not recommended - better to use
                                                          li3 create migration <name>).
    $ li3 migration db:setup                            - initializing the database for migration support
    $ li3 migration db:migrate                          - run database migration
    $ li3 migration db:migrate VERSION=20101006114707   - run database migration with specified version.
    $ li3 migration db:migrate VERSION=-2               - run the database migration with a specific version of two
                                                          previously.
    $ li3 migration db:version                          - determing the current DB version
    $ li3 migration db:status                           - getting the current state of migrations
    $ li3 migration db:schema                           - dumping the current schema to text file

> More informations about ruckusing-migrations in [https://github.com/ruckus/ruckusing-migrations](https://github.com/ruckus/ruckusing-migrations).

##Special options

There is few special options for create and destroy commands:

    $ --library=name_of_library  - Name of library to use
    $ --template=name_of_library - This is the name of the template from which generator gets
                                   the source files. You can add your own templates in
                                   <library>\extensions\command\create\template\template_name\*
    $ --skip=action              - Used for skip generator actions. You can skip few actions
                                   by typing --skip=<action1>,<action2>,<action*>

For example `li3 create Posts --template=own_template --library=own_library --skip=test,mock,migration`

##Examples of usage

    $ li3 create Posts                      - creates full Posts scaffolding
    $ li3 destroy Posts                     - destroys full Posts scaffolding
    $ li3 create migration AddUsersTable    - creates new migration named AddUsersTable
    $ li3 crate controller Products         - creates only ProductsController file
    $ li3 crete test mode Categories        - creates test file for Categories model
    $ li3 migration db:migrate              - creates database migration