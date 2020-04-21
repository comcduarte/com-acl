<?php 
namespace Acl\Controller;

use Acl\Model\AclModel;
use Components\Controller\AbstractConfigController;
use Laminas\Db\Sql\Sql;
use Laminas\Db\Sql\Ddl\CreateTable;
use Laminas\Db\Sql\Ddl\DropTable;
use Laminas\Db\Sql\Ddl\Column\Datetime;
use Laminas\Db\Sql\Ddl\Column\Integer;
use Laminas\Db\Sql\Ddl\Column\Varchar;
use Laminas\Db\Sql\Ddl\Constraint\PrimaryKey;
use Settings\Model\SettingsModel;

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
        
        $ddl->addColumn(new Varchar('ROLE', 255, TRUE));
        $ddl->addColumn(new Varchar('RESOURCE', 255, TRUE));
        $ddl->addColumn(new Varchar('POLICY', 255, TRUE));
        $ddl->addColumn(new Varchar('PRIVILEGE', 255, TRUE));
        
        $ddl->addConstraint(new PrimaryKey('UUID'));
        
        $this->adapter->query($sql->buildSqlString($ddl), $this->adapter::QUERY_MODE_EXECUTE);
        unset($ddl);
        
        /******************************
         * Default ACL Rules
         ******************************/
        $acl = new AclModel($this->adapter);
        $acl->ROLE = $acl::ROLE_EVERYONE;
        $acl->RESOURCE = 'home';
        $acl->PRIVILEGE = 'index';
        $acl->POLICY = $acl::POLICY_ALLOW;
        $acl->create();
        
        $acl = new AclModel($this->adapter);
        $acl->ROLE = $acl::ROLE_EVERYONE;
        $acl->RESOURCE = 'user/login';
        $acl->PRIVILEGE = 'login';
        $acl->POLICY = $acl::POLICY_ALLOW;
        $acl->create();
        
        $acl = new AclModel($this->adapter);
        $acl->ROLE = $acl::ROLE_EVERYONE;
        $acl->RESOURCE = 'user/logout';
        $acl->PRIVILEGE = 'logout';
        $acl->POLICY = $acl::POLICY_ALLOW;
        $acl->create();
        
        $acl = new AclModel($this->adapter);
        $acl->ROLE = $acl::ROLE_EVERYONE;
        $acl->RESOURCE = 'denied';
        $acl->PRIVILEGE = 'view';
        $acl->POLICY = $acl::POLICY_ALLOW;
        $acl->create();
        
        $acl = new AclModel($this->adapter);
        $acl->ROLE = $acl::ROLE_ADMIN;
        $acl->RESOURCE = 'acl/config';
        $acl->PRIVILEGE = '';
        $acl->POLICY = $acl::POLICY_ALLOW;
        $acl->create();
        
        $acl = new AclModel($this->adapter);
        $acl->ROLE = $acl::ROLE_ADMIN;
        $acl->RESOURCE = 'user/config';
        $acl->PRIVILEGE = '';
        $acl->POLICY = $acl::POLICY_ALLOW;
        $acl->create();
        
        $acl = new AclModel($this->adapter);
        $acl->ROLE = $acl::ROLE_ADMIN;
        $acl->RESOURCE = 'user/default';
        $acl->PRIVILEGE = '';
        $acl->POLICY = $acl::POLICY_ALLOW;
        $acl->create();
        
        $acl = new AclModel($this->adapter);
        $acl->ROLE = $acl::ROLE_ADMIN;
        $acl->RESOURCE = 'role/default';
        $acl->PRIVILEGE = '';
        $acl->POLICY = $acl::POLICY_ALLOW;
        $acl->create();
        
        $acl = new AclModel($this->adapter);
        $acl->ROLE = $acl::ROLE_ADMIN;
        $acl->RESOURCE = 'acl/default';
        $acl->PRIVILEGE = '';
        $acl->POLICY = $acl::POLICY_ALLOW;
        $acl->create();
        
        $acl = new AclModel($this->adapter);
        $acl->ROLE = $acl::ROLE_ADMIN;
        $acl->RESOURCE = 'settings/default';
        $acl->PRIVILEGE = '';
        $acl->POLICY = $acl::POLICY_ALLOW;
        $acl->create();
        
        $this->createSettings('ACL');
        $this->flashMessenger()->addSuccessMessage('Acl Settings created.');
    }

    public function createSettings($module)
    {
        parent::createSettings($module);
        $setting = new SettingsModel($this->adapter);
        $setting->MODULE = $module;
        $setting->SETTING = 'CONFIG_SOURCE';
        $setting->VALUE = 'CONFIG';
        $setting->create();
    }
}