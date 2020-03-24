<?php 
namespace Acl\Service;

use Laminas\EventManager\EventManagerAwareInterface;
use Laminas\EventManager\EventManagerAwareTrait;
use Laminas\Permissions\Acl\Acl;
use Laminas\Permissions\Acl\Resource\GenericResource;
use Laminas\Permissions\Acl\Role\GenericRole;

class AclService implements EventManagerAwareInterface
{
    use EventManagerAwareTrait;
    
    const USER_GUEST = 'guest';
    
    protected $acl;
    
    public function __construct(Acl $acl)
    {
        $this->setAcl($acl);
    }
    
    public function setAcl(Acl $acl)
    {
        $this->acl = $acl;
        return $this;
    }
    
    public function getAcl()
    {
        return $this->acl;
    }
    
    public function setup(array $config)
    {
        $acl = $this->getAcl();
        
        foreach ($config as $role => $resources) {
            if (!$acl->hasRole($role)) {
                $acl->addRole(new GenericRole($role));
            }
            foreach ($resources as $resource => $privileges) {
                if (!$acl->hasResource($resource)) {
                    $acl->addResource(new GenericResource($resource));
                }
                $acl->allow($role, $resource, array_values($privileges));
            }
        }
    }
    
    public function isAllowed($role, $resource, $privilege)
    {
        if ($this->getAcl()->hasResource($resource) && $this->getAcl()->hasRole($role)) {
            $result = $this->getAcl()->isAllowed($role, $resource, $privilege);
            return $result;
        } else {
            /**
             * Default Rule
             */
            return FALSE;
        }
    }
}