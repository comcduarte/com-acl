<?php 
namespace Acl\Controller;

use Laminas\Mvc\Controller\AbstractActionController;

class IndexController extends AbstractActionController
{
    public function deniedAction()
    {
        $role = $this->params()->fromRoute('role',0);
        $routeMatch = $this->params()->fromRoute('routeMatch',0);
        $routeAction = $this->params()->fromRoute('routeAction',0);
        
        return ([
            'role' => $role,
            'routeMatch' => $routeMatch,
            'routeAction' => $routeAction,
        ]);
    }
}