<?php
$tstart = microtime(true);
set_time_limit(0); /* makes sure our script doesnt timeout */

/* version info */
define('PKG_NAME','filetranslit');
define('PKG_NAME_LOWER','filetranslit');
define('PKG_VERSION','0.1.1');
define('PKG_RELEASE','pl');

$root = dirname(dirname(__FILE__)).'/';
$sources= array (
	'build' => $root .'_build/',
	'source_plugins'=>$root.'core/components/filetranslit/elements/plugins/',
	'docs' => $root.'core/components/filetranslit/docs/',
);

require_once $sources['build'].'build.config.php';

require_once MODX_CORE_PATH . 'model/modx/modx.class.php';

$modx= new modX();
$modx->initialize('mgr');
$modx->setLogLevel(modX::LOG_LEVEL_INFO);
$modx->setLogTarget(XPDO_CLI_MODE ? 'ECHO' : 'HTML');

$modx->loadClass('transport.modPackageBuilder','',false, true);
$builder = new modPackageBuilder($modx);
$builder->createPackage(PKG_NAME,PKG_VERSION,PKG_RELEASE);
$builder->registerNamespace(PKG_NAME_LOWER,false,true,'{core_path}components/'.PKG_NAME_LOWER.'/');



$plugin = $modx->newObject('modPlugin');
$plugin->set('id',1);
$plugin->set('name','fileTranslit');
$plugin->set('description','Плагин автоматически транслитерирует имена файлов при загрузке.');
$plugin->set('plugincode', file_get_contents($sources['source_plugins'] . '/filetranslit.php'));
$event = $modx->newObject('modPluginEvent');
$event->fromArray(array(
	'pluginid' => 1,
    'event' => 'OnFileManagerUpload',
    'priority' => 0,
    'propertyset' => 0,
),'',true,true);
$plugin->addMany($event);

$attr = array(
	xPDOTransport::PRESERVE_KEYS => false,
	xPDOTransport::UPDATE_OBJECT => true,
	xPDOTransport::UNIQUE_KEY => 'name',
	xPDOTransport::RELATED_OBJECTS => true,
	xPDOTransport::RELATED_OBJECT_ATTRIBUTES => array (
		'PluginEvents' => array(
			xPDOTransport::PRESERVE_KEYS => true,
			xPDOTransport::UPDATE_OBJECT => true,
			xPDOTransport::UNIQUE_KEY => array('pluginid','event'),
		),
	),
);
$vehicle = $builder->createVehicle($plugin,$attr);
$builder->putVehicle($vehicle);


$builder->setPackageAttributes(array(
	'license' => file_get_contents($sources['docs'] . 'gpl-2.0.txt'),
	'readme' => "This plugin transliterates filenames on upload via MODX filemanager.\nIt should be bent to the OnFileManagerUpload event.",
	'changelog' => "v0.1.1 - First release",
));

$builder->pack();

$tend= microtime(true);
$totalTime= ($tend - $tstart);
$totalTime= sprintf("%2.4f s", $totalTime);

$modx->log(modX::LOG_LEVEL_INFO,"\nPackage Built.\nExecution time: {$totalTime}\n");
exit();