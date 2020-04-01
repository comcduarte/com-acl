<?php 
namespace Acl\Listener;

use Acl\Service\AclService;
use Laminas\Authentication\AuthenticationService;
use Laminas\EventManager\EventManagerInterface;
use Laminas\EventManager\ListenerAggregateInterface;
use Laminas\Mvc\MvcEvent;

class AclListener implements ListenerAggregateInterface
{
    private $aclService;
    private $authService;
    private $listeners;
    
    public function __construct(AclService $aclService, AuthenticationService $authService)
    {
        $this->aclService = $aclService;
        $this->authService = $authService;
    }
    
    public function checkAcl(MvcEvent $e)
    {
        $role = AclService::USER_GUEST;
        
        if ($this->getAuthService()->hasIdentity()) {
            $authService = $this->getAuthService();
            $identity = $authService->getIdentity();
            $role = 'member';
        }
        
        $routeMatch = $e->getRouteMatch();
        if (!$this->getAclService()->isAllowed($role, $routeMatch->getMatchedRouteName(), $routeMatch->getParam('action'))) {
            $e->getRouteMatch()
            ->setParam('controller', 'index')
            ->setParam('role', $role)
            ->setParam('routeMatch', $routeMatch->getMatchedRouteName())
            ->setParam('routeAction', $routeMatch->getParam('action'))
            ->setParam('action', 'denied');
        }
        return $this;
    }
    
    
    
    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $this->listeners[] = $events->attach(MvcEvent::EVENT_ROUTE, [$this, 'checkAcl'], -1000);
    }
    
    private function getAclService()
    {
        return $this->aclService;
    }
    
    private function getAuthService()
    {
        return $this->authService;
    }
    
    public function detach(EventManagerInterface $events)
    {
        foreach ($this->listeners as $index => $listener) {
            $events->detach($listener);
            unset($this->listeners[$index]);
        }
    }
}