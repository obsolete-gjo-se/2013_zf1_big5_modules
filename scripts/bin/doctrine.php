<?php
// X:/Dropbox/Projekte/workspace/DefaultWorkspace/com_application/scripts/bin

define('APPLICATION_ENV', 'development');

require_once '../../application/configs/application.php';

// Bootstrapping resources
$application = new Zend_Application(APPLICATION_ENV, APPLICATION_PATH . '/configs/application.ini');
$bootstrap = $application->bootstrap()->getBootstrap();

// Retrieve Doctrine Container resource
$container = $bootstrap->getResource('doctrine');

// Console
$cli = new \Symfony\Component\Console\application(
    'Doctrine Command Line Interface',
    \Doctrine\Common\Version::VERSION
);

try {
    // Bootstrapping Console HelperSet
    $helperSet = array();

    if (($dbal = $container->getConnection(getenv('CONN') ?: $container->defaultConnection)) !== null) {
        $helperSet['db'] = new \Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper($dbal);
    }

    if (($em = $container->getEntityManager(getenv('EM') ?: $container->defaultEntityManager)) !== null) {
        $helperSet['em'] = new \Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper($em);
    }
} catch (\Exception $e) {
    $cli->renderException($e, new \Symfony\Component\Console\Output\ConsoleOutput());
}

$cli->setCatchExceptions(true);
$cli->setHelperSet(new \Symfony\Component\Console\Helper\HelperSet($helperSet));

$cli->addCommands(array(
    // DBAL Commands
    new \Doctrine\DBAL\Tools\Console\Command\RunSqlCommand(),
    new \Doctrine\DBAL\Tools\Console\Command\ImportCommand(),

    // ORM Commands
    new \Doctrine\ORM\Tools\Console\Command\ClearCache\MetadataCommand(),
    new \Doctrine\ORM\Tools\Console\Command\ClearCache\ResultCommand(),
    new \Doctrine\ORM\Tools\Console\Command\ClearCache\QueryCommand(),
    new \Doctrine\ORM\Tools\Console\Command\SchemaTool\CreateCommand(),
    new \Doctrine\ORM\Tools\Console\Command\SchemaTool\UpdateCommand(),
    new \Doctrine\ORM\Tools\Console\Command\SchemaTool\DropCommand(),
    new \Doctrine\ORM\Tools\Console\Command\EnsureProductionSettingsCommand(),
    new \Doctrine\ORM\Tools\Console\Command\ConvertDoctrine1SchemaCommand(),
    new \Doctrine\ORM\Tools\Console\Command\GenerateRepositoriesCommand(),
    new \Doctrine\ORM\Tools\Console\Command\GenerateEntitiesCommand(),
    new \Doctrine\ORM\Tools\Console\Command\GenerateProxiesCommand(),
    new \Doctrine\ORM\Tools\Console\Command\ConvertMappingCommand(),
    new \Doctrine\ORM\Tools\Console\Command\RunDqlCommand(),
    new \Doctrine\ORM\Tools\Console\Command\ValidateSchemaCommand(),

));

$cli->run();
