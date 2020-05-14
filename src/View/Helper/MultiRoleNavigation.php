<?php
namespace Acl\View\Helper;

use Laminas\Navigation\Page\AbstractPage;
use Laminas\Permissions\Acl\Role\RoleInterface;
use Laminas\View\Exception\InvalidArgumentException;
use Laminas\View\Helper\Navigation;

class MultiRoleNavigation extends Navigation
{
    protected $roles;
    
    public function accept(AbstractPage $page, $recursive = TRUE)
    {
        $accept = TRUE;
        
        if (! $page->isVisible(false) && ! $this->getRenderInvisible()) {
            $accept = false;
        } elseif ($this->getUseAcl()) {
            $accept = false;
            $acl = $this->getAcl();
            $roles = $this->getRoles();
            
            foreach ($roles as $role) {
                $params = ['acl' => $acl, 'page' => $page, 'role' => $role];
                if ($this->isAllowed($params)) {
                    $accept = true;
                    return $accept;
                }
            }
        }
        
        if ($accept && $recursive) {
            $parent = $page->getParent();
            
            if ($parent instanceof AbstractPage) {
                $accept = $this->accept($parent, true);
            }
        }
        
        return $accept;
    }
    
    public function setRole($role = null)
    {
        if (null === $role || is_string($role) ||
            $role instanceof RoleInterface
            ) {
                $this->role = $role;
            } else {
                throw new InvalidArgumentException(sprintf(
                    '$role must be a string, null, or an instance of '
                    . 'Laminas\Permissions\Role\RoleInterface; %s given',
                    (is_object($role) ? get_class($role) : gettype($role))
                    ));
            }
            
            return $this;
    }
    
    public function setRoles($roles = NULL)
    {
        if (NULL === $roles || is_array($roles)) {
            $this->roles = $roles;
        } else {
            throw new InvalidArgumentException(sprintf(
                '$roles must be an array, or null, ; %s given',
                (is_object($roles) ? get_class($roles) : gettype($roles))
                ));
        }
    }
    
    public function getRole()
    {
        if ($this->role === null && static::$defaultRole !== null) {
            return static::$defaultRole;
        }
        
        return $this->role;
    }
    
    public function getRoles()
    {
        if ($this->roles === NULL && $this->role === NULL && static::$defaultRole !== NULL) {
            return [static::$defaultRole];
        }
        
        /**
         * If Role exists, but Roles are NULL, return individual Role
         */
        if (!$this->hasRoles() && $this->hasRole()) {
            return [$this->role];
        }
        
        if ($this->hasRoles()) {
            return $this->roles;
        }
        
        return [static::$defaultRole];
    }
    
    public function hasRole()
    {
        if ($this->role instanceof RoleInterface
            || is_string($this->role)
            || static::$defaultRole instanceof RoleInterface
            || is_string(static::$defaultRole)
            ) {
                return true;
            }
            
            return false;
    }
    
    public function hasRoles()
    {
        if (is_array($this->roles)) {
            return true;
        }
        
        return false;
    }
}