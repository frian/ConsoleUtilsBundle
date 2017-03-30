# ConsoleUtilsBundle

some utilities for the Symfony command line

## Commands

### utils:doctrine:recreate

The utils:doctrine:recreate recreates the database and loads fixtures:


    php bin/console utils:doctrine:recreate


For compatibility reasons you have to specifiy the --force option:

    php bin/console utils:doctrine:recreate --force

You can use the --fixtures option from doctrine:fixtures:load


### utils:tests:run WIP

The utils:tests:run executes phpunit tests:

    php bin/console utils:tests:run
