<?php 
namespace Acl\Listener;

use Acl\Service\AclService;
use Laminas\Authentication\AuthenticationService;
use Laminas\EventManager\EventManagerInterface;
use Laminas\EventManager\ListenerAggregateInterface;
use Laminas\Mvc\MvcEvent;
use Laminas\Db\Adapter\AdapterAwareTrait;
use User\Model\UserModel;
use Acl\Model\AclModel;

class AclListener implements ListenerAggregateInterface
{
    use AdapterAwareTrait;
    
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
        $allowed = FALSE;
        $role = AclModel::ROLE_EVERYONE;
        $routeMatch = $e->getRouteMatch();
        
        if ($this->getAuthService()->hasIdentity()) {
            $authService = $this->getAuthService();
            $identity = $authService->getIdentity();
//             $role = $identity;
            
            $user = new UserModel($this->adapter);
            $user->read(['USERNAME' => $identity]);
            $groups = $user->memberOf();
            
            $groups[]['ROLENAME'] = AclModel::ROLE_EVERYONE;
            
            foreach ($groups as $group) {
                if ($this->getAclService()->isAllowed($group['ROLENAME'], $routeMatch->getMatchedRouteName(), $routeMatch->getParam('action'))) {
                    $allowed = TRUE;
                    $role = $group;
                    break;
                }
            }
        } else {
            if ($this->getAclService()->isAllowed($role, $routeMatch->getMatchedRouteName(), $routeMatch->getParam('action'))) {
                $allowed = TRUE;
            }
        }
        
        
        if (!$allowed) {
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