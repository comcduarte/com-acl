<?php 
namespace Acl\Service;

use Acl\Model\AclModel;
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
    
    public function setup(array $data)
    {
        $acl = $this->getAcl();
        
        /******************************
         * Parser for Database Config
         ******************************/
        foreach ($data as $record) {
            $privilege = ('' == $record['PRIVILEGE']) ? NULL : explode(",", $record['PRIVILEGE']);
            $policy = ('' == $record['POLICY']) ? NULL : $record['POLICY'];
            
            if ('' == $record['ROLE'] || 'NULL' == $record['ROLE']) {
                $role = NULL;
            } else {
                $role = $record['ROLE'];
                if (!$acl->hasRole($role)) {
                    $acl->addRole(new GenericRole($role));
                }
            }
            
            if ('' == $record['RESOURCE'] || 'NULL' == $record['RESOURCE']) {
                $resource = NULL;
            } else {
                $resource = $record['RESOURCE'];
                if (!$acl->hasResource($resource)) {
                    $acl->addResource(new GenericResource($resource));
                } 
            }
            
            
            switch ($policy) 
            {
                case 'allow':
                    $acl->allow($role, $resource, $privilege);
                    break;
                case 'deny':
                    $acl->deny($role, $resource, $privilege);
                    break;
                default:
                    break;
            }
            
        }
    }
    
    public function setupConfig(array $data)
    {
        $acl = $this->getAcl();
        
        foreach ($data as $role => $resources) {
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
    
    public function setupRoles(array $data) 
    {
        $acl = $this->getAcl();
        /**
         * Setup Default Roles
         */
        $acl->addRole(new GenericRole(AclModel::ROLE_EVERYONE));
        
        /******************************
         * Parser for Database Config
         ******************************/
        foreach ($data as $record) {
            $role = $record['ROLENAME'];
            $parent = $record['PARENT'];
            
            if (!$acl->hasRole($role)) {
                if ($parent) {
                    $acl->addRole(new GenericRole($role), [$parent, AclModel::ROLE_EVERYONE]);
                } else {
                    $acl->addRole(new GenericRole($role), AclModel::ROLE_EVERYONE);
                }
            }
        }
    }
    
    public function setupUsers(array $data)
    {
        $acl = $this->getAcl();
        
        /******************************
         * Parser for Database Config
         ******************************/
        foreach ($data as $record) {
            $role = $record['ROLENAME'];
            $user = $record['USERNAME'];
            
            if (!$acl->hasRole($user)) {
                if ($role) { 
                    $acl->addRole(new GenericRole($user), [$role, AclModel::ROLE_EVERYONE]);
                } else {
                    $acl->addRole(new GenericRole($user), [AclModel::ROLE_EVERYONE]);
                }
            }
        }
    }
}