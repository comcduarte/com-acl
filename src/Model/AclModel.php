<?php
namespace Acl\Model;

use Components\Model\AbstractBaseModel;

class AclModel extends AbstractBaseModel
{
    const ROLE_GUEST = 'guest';
    const ROLE_EVERYONE = 'EVERYONE';
    const ROLE_ADMIN = 'admin';
    const POLICY_ALLOW = 'allow';
    const POLICY_DENY = 'deny';
    
    public $ROLE;
    public $RESOURCE;
    public $POLICY;
    public $PRIVILEGE;
    
    public function __construct($adapter = NULL)
    {
        parent::__construct($adapter);
        $this->setTableName('acl');
    }
}