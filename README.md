# ConsoleUtilsBundle

some utilities for the Symfony command line

## Commands

### utils:doctrine:recreate

The utils:doctrine:recreate recreates the database and loads fixtures:

    php bin/console utils:doctrine:recreate

For compatibility reasons you have to specifiy the --force option:

    php bin/console utils:doctrine:recreate --force

You can use the --fixtures option from doctrine:fixtures:load

You can use the --em option from doctrine:fixtures:load and doctrine:schema:create

You can use the --connection option from doctrine:database:create and doctrine:database:drop


### utils:doctrine:test WIP

The utils:doctrine:test executes phpunit tests on a recreated database:

    php bin/console utils:doctrine:test

For compatibility reasons you have to specifiy the --force option:

    php bin/console utils:doctrine:test --force

You can use the --fixtures option from doctrine:fixtures:load

You can use the --testsuite option from phpunit
