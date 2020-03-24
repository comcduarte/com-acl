<?php 
namespace Acl;

use Laminas\Mvc\MvcEvent;

class Module
{
    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }
    
    public function onBootstrap(MvcEvent $e) {
        $application = $e->getApplication();
        $eventManager = $application->getEventManager();
        $serviceManager = $application->getServiceManager();
        
        $aclListener = $serviceManager->get('acl-listener');
        $aclListener->attach($eventManager);
    }
}