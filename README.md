The library major features are:

- migrations engine with CLI, web and program interface

- "table spaces" allows having multiple databases per application, and also lets composer modules use database and have migrations

- connectors and table spaces are declared and configured separately, which helps easily override database configuration

- lightweight wrapper around PDO with automatic connection to server

# Connectors and spaces

The space is a set of tables, which are located in the same database and used together. Application may consist of several modules, each of them has it's own table space or several spaces. For example, php-auth module uses SPACE_PHPAUTH.

Each space has to be assigned to one of connectors (database connections) in the application config. All spaces may share the same connection, or be located in different databases and on different database servers.

Connector and space configuration example:

    class CommonConfig
    {
        const CONNECTOR_DBDEMO = 'CONNECTOR_DBDEMO';
        const SPACE_DBDEMO = 'SPACE_DBDEMO';
    
        static public function init(){
            DBConfig::setConnector(
                self::CONNECTOR_DBDEMO,
                new ConnectorMySQL('127.0.0.1', 'dbdemo', 'root', '1234')
            );
            
            DBConfig::setSpace(
                self::SPACE_DBDEMO,
                new Space(self::CONNECTOR_DBDEMO, __DIR__ . '/../dbdemo.sql')
            );
        }
    }

Real world applications usually has common configuration, which is applied to all instances, and a number of instance configurations - dev, test, production, etc.  

Common configuration is stored to application repository while instance configurations are stored locally for each instance and included in .gitignore file.

Instance configuration extends and overrides common configuration:
 
    class Config
    {
        static public function init(){
            CommonConfig::init();
            
            DBConfig::setConnector(self::CONNECTOR_DBDEMO, new ConnectorMySQL('production.db.host', 'dbdemo', 'demouser', 'some_strong_password'));
        }
    }

Application applies configuration inside entry points like this: 

    \Config\Config::init();
    
# Database operations

Database connection will be established automatically when performing first query for a given connector. Thus majority of pages can be generated without connecting to database at all -  all data normally comes from cache.

Basic database methods are located in DB class: 

    static public function readColumn($space_id, $query, $params_arr = array())

Reads values from single column to array.

    static public function query($space_id, $query, $params_arr = array())
    
Executes query and returns PDO statement obj. Can be used for non-select queries (insert, update, etc.).    

    static public function readObject($space_id, $query, $params_arr = array())
   
Reads single database record to stdClass object. 
    
    static public function readObjects($space_id, $query, $params_arr = array(), $field_name_for_keys = '')

Used to read multiple records from database to array of stdClass objects.

Every method receives database space id, query string with parameter placeholders and array of parameter values.     

# Migrations

Migrations are performed by vendor/bin/migrate tool.

All migrations for a space are stored in a single file, one sql query per line. Migrations file name is passed to space configuration.   

The queries are executed in the order they have in the file. You can use php-model utilites to create migrations, or write migrations manually.

# Transactions

Correct transactions are possible only within single database connection, thus you have to use transactions and foreign keys only between spaces, which share the same connector. 

For example, if your application models reference User model from php-auth - you have to assign same connector to your application table space and php-auth table space. 

# Using modules and configuring multiple spaces

Every module, which works with the database, has it's own table space id and migrations file. You have to configure the corresponding space for the module to work. 

        DBConfig::setConnector(self::CONNECTOR_DEMO, new ConnectorMySQL('127.0.0.1', 'phpstorage', 'root', '1234'));

        DBConfig::setSpace(AuthConfig::SPACE_PHPAUTH, new Space(self::CONNECTOR_DEMO, __DIR__ . '/../vendor/o-log/php-auth/db_phpauth.sql'));
        DBConfig::setSpace(StorageConstants::SPACE_PHPSTORAGE, new Space(self::CONNECTOR_DEMO, __DIR__ . '/../db_phpstorage.sql'));

You must register module spaces before the application spaces, thus migrator can first create module tables and after that create applications tables, which is important because application tables may reference the modules tables.
