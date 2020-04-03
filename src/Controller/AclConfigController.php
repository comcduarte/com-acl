<?php 
namespace Acl\Controller;

use Components\Controller\AbstractConfigController;
use Laminas\Db\Sql\Sql;
use Laminas\Db\Sql\Ddl\CreateTable;
use Laminas\Db\Sql\Ddl\DropTable;
use Laminas\Db\Sql\Ddl\Column\Datetime;
use Laminas\Db\Sql\Ddl\Column\Integer;
use Laminas\Db\Sql\Ddl\Column\Varchar;
use Laminas\Db\Sql\Ddl\Constraint\PrimaryKey;
use Acl\Model\AclModel;

class AclConfigController extends AbstractConfigController
{
    public function clearDatabase()
    {
        $sql = new Sql($this->adapter);
        $ddl = [];
        
        $ddl[] = new DropTable('acl');
        
        foreach ($ddl as $obj) {
            $this->adapter->query($sql->buildSqlString($obj), $this->adapter::QUERY_MODE_EXECUTE);
        }
    }

    public function createDatabase()
    {
        $sql = new Sql($this->adapter);
        
        /******************************
         * ACL
         ******************************/
        $ddl = new CreateTable('acl');
        
        $ddl->addColumn(new Varchar('UUID', 36));
        $ddl->addColumn(new Integer('STATUS', TRUE));
        $ddl->addColumn(new Datetime('DATE_CREATED', TRUE));
        $ddl->addColumn(new Datetime('DATE_MODIFIED', TRUE));
        
        $ddl->addColumn(new Varchar('ROLE', 255));
        $ddl->addColumn(new Varchar('RESOURCE', 255));
        $ddl->addColumn(new Varchar('POLICY', 255));
        $ddl->addColumn(new Varchar('PRIVILEGE', 255));
        
        $ddl->addConstraint(new PrimaryKey('UUID'));
        
        $this->adapter->query($sql->buildSqlString($ddl), $this->adapter::QUERY_MODE_EXECUTE);
        unset($ddl);
        
        /******************************
         * Default ACL Rules
         ******************************/
        $acl = new AclModel($this->adapter);
        $acl->ROLE = $acl::ROLE_GUEST;
        $acl->RESOURCE = 'home';
        $acl->PRIVILEGE = 'index';
        $acl->POLICY = $acl::POLICY_ALLOW;
        $acl->create();
        
        $acl = new AclModel($this->adapter);
        $acl->ROLE = $acl::ROLE_GUEST;
        $acl->RESOURCE = 'user/login';
        $acl->PRIVILEGE = 'login';
        $acl->POLICY = $acl::POLICY_ALLOW;
        $acl->create();
        
        $acl = new AclModel($this->adapter);
        $acl->ROLE = $acl::ROLE_GUEST;
        $acl->RESOURCE = 'user/logout';
        $acl->PRIVILEGE = 'logout';
        $acl->POLICY = $acl::POLICY_ALLOW;
        $acl->create();
        
        $acl = new AclModel($this->adapter);
        $acl->ROLE = $acl::ROLE_GUEST;
        $acl->RESOURCE = 'denied';
        $acl->PRIVILEGE = 'view';
        $acl->POLICY = $acl::POLICY_ALLOW;
        $acl->create();
        
        $acl = new AclModel($this->adapter);
        $acl->ROLE = $acl::ROLE_ADMIN;
        $acl->RESOURCE = 'acl/config';
        $acl->PRIVILEGE = 'index';
        $acl->POLICY = $acl::POLICY_ALLOW;
        $acl->create();
        
        $acl = new AclModel($this->adapter);
        $acl->ROLE = $acl::ROLE_ADMIN;
        $acl->RESOURCE = 'acl/config';
        $acl->PRIVILEGE = 'create';
        $acl->POLICY = $acl::POLICY_ALLOW;
        $acl->create();
        
        $acl = new AclModel($this->adapter);
        $acl->ROLE = $acl::ROLE_ADMIN;
        $acl->RESOURCE = 'acl/config';
        $acl->PRIVILEGE = 'clear';
        $acl->POLICY = $acl::POLICY_ALLOW;
        $acl->create();
    }
}