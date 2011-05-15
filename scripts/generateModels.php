<?php

// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(APPLICATION_PATH . '/../library'),
    get_include_path(),
)));

// Zend_Console_GetOpt is needed to define current environment
require_once 'Zend/Console/Getopt.php';

try {
    $console = new Zend_Console_Getopt(array(
        'environment|e=w' => 'Application environment to use (default development)',
        'models-path|p=s' => 'Path to put the generated models (default ../application/models/generated)'
    ));

    $env = $console->getOption('e');
} catch (Zend_Console_Getopt_Exception $e) {
    echo $console->getUsageMessage();
    exit;
}

if (null === $env) {
    $env = 'development';
}

define('APPLICATION_ENV', $env);

/** Zend_Application **/
require_once 'Zend/Application.php';

// Create application and bootstrap
$application = new Zend_Application(
    APPLICATION_ENV,
    APPLICATION_PATH . '/configs/application.ini'
);

$application->bootstrap();
$bootstrap = $application->getBootstrap();

$path = $console->getOption('p');

if (null === $path) {
    $path = APPLICATION_PATH . '/models/generated/';
}

if (!is_dir($path) && !mkdir($path, 0644, true)) {
    die("Unable to create dir $path");
}

$dbTablePath = $path . '/DbTable';

if (!is_dir($dbTablePath) && !mkdir($dbTablePath, 0644)) {
    die("Unable to create dir $dbTablePath");
}

$mapperPath = $path . '/mappers';

if (!is_dir($mapperPath) && !mkdir($mapperPath, 0644)) {
    die("Unable to create dir $mapperPath");
}

if (!is_writable($path)) {
    die("Directory $path is not writable");
}

if (!is_writable($mapperPath)) {
    die("Directory $mapperPath is not writable");
}

$innerStrategy = new ModelGenerator_NamingStrategy_Default();
$namingStrategy =
    new ModelGenerator_NamingStrategy_RemoveClassName($innerStrategy);
    
$db = $bootstrap->getResource('db');
$appNamespace = $bootstrap->getOption('appNamespace');

if (null === $appNamespace) {
    $appNamespace = 'Application';
}

if (!$db instanceof Zend_Db_Adapter_Abstract) {
    die('Db resource is not properly configured');
}

$tables = $db->listTables();

foreach ($tables as $table) {
    $dbTableGenerator = new ModelGenerator_DbTable_Generator(array(
        'tableName' => $table,
        'appNamespace' => $appNamespace,
        'namingStrategy' => $namingStrategy
    ));
    
    $fileName = $namingStrategy->getFileName($table);
    
    file_put_contents(
        $dbTablePath . '/' . $fileName,
        "<?php\n" . $dbTableGenerator->generate()
    );
    
    require_once $dbTablePath . '/' . $fileName;
    $dbTableClassName = $dbTableGenerator->getName();
    $dbTable = new $dbTableClassName();
    
    $modelGenerator = new ModelGenerator_DomainModel_Generator(array(
        'appNamespace' => $appNamespace,
        'dbTable' => $dbTable,
        'namingStrategy' => $namingStrategy
    ));
    
    file_put_contents(
        $path . '/' . $fileName, "<?php\n" . $modelGenerator->generate()
    );
        
    $mapperGenerator = new ModelGenerator_DataMapper_Generator(array(
    	'appNamespace' => $appNamespace,
        'dbTable' => $dbTable,
        'namingStrategy' => $namingStrategy
    ));
    
    file_put_contents(
        $mapperPath . '/' . $fileName,
        "<?php\n" . $mapperGenerator->generate()
    );
}