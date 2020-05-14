<?php
namespace Acl\Listener;

use Laminas\Mvc\MvcEvent;

class NavigationListener
{
    public function addAcl(MvcEvent $event)
    {
        $sm = $event->getApplication()->getServiceManager();
        $pm = $sm->get('ViewHelperManager');
        $plugin = $pm->get('navigation');
        
        $aclService = $sm->get('acl-service');
        $acl = $aclService->getAcl();
        $plugin->setAcl($acl);
    }
}