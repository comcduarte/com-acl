<?php
namespace Acl\Model;

use Components\Model\AbstractBaseModel;

class AclModel extends AbstractBaseModel
{
    public $ROLE;
    public $RESOURCE;
    public $PRIVILEGE;
    
    public function __construct($adapter = NULL)
    {
        parent::__construct($adapter);
        $this->setTableName('acl');
    }
}