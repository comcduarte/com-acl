<?php 
namespace Acl\Service\Factory;

use Acl\Model\AclModel;
use Acl\Service\AclService;
use Interop\Container\ContainerInterface;
use Laminas\Db\ResultSet\ResultSet;
use Laminas\Db\Sql\Join;
use Laminas\Db\Sql\Select;
use Laminas\Db\Sql\Sql;
use Laminas\Permissions\Acl\Acl;
use Laminas\ServiceManager\Factory\FactoryInterface;
use User\Model\RoleModel;
use User\Model\UserModel;
use Exception;

class AclServiceFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config = $container->get('configuration');
        $aclService = new AclService(New Acl());
        
        
        if ($config['acl_config']['source'] == 'config') {
            $aclService->setupConfig($config['acl']);
            return $aclService;
        } 
        
        /******************************
         * ACL Configuration
         *
         * Retrieve Roles and Inheritance
         ******************************/
        $adapter = $container->get('user-model-adapter');
        $model = new RoleModel($adapter);
        $sql = new Sql($adapter);
        
        $select = new Select();
        $select->from($model->getTableName());
        $select->order(['PARENT']);
        
        $statement = $sql->prepareStatementForSqlObject($select);
        $resultSet = new ResultSet();
        
        try {
            $results = $statement->execute();
            $resultSet->initialize($results);
        } catch (Exception $e) {
            return FALSE;
        }
        
        $data = $resultSet->toArray();
        $aclService->setupRoles($data);
        
        /******************************
         * ACL Configuration
         *
         * Retrieve Users and Role Membership
         ******************************/
        $model = new UserModel($adapter);
        $sql = new Sql($adapter);
        
        $select = new Select();
        $select->from('user_roles');
        $select->columns(['UUID']);
        $select->join('users', 'user_roles.USER = users.UUID', ['USERNAME'], Join::JOIN_INNER);
        $select->join('roles', 'user_roles.ROLE = roles.UUID', ['ROLENAME'], Join::JOIN_INNER);
        
        $statement = $sql->prepareStatementForSqlObject($select);
        $resultSet = new ResultSet();
        
        try {
            $results = $statement->execute();
            $resultSet->initialize($results);
        } catch (Exception $e) {
            return FALSE;
        }
        
        $data = $resultSet->toArray();
        $aclService->setupUsers($data);
        
        /******************************
         * ACL Configuration
         * 
         * If acl is present, override config rules
         ******************************/
        $adapter = $container->get('acl-model-adapter');
        $model = new AclModel($adapter);
        $sql = new Sql($adapter);
        
        $select = new Select();
        $select->from($model->getTableName());
        
        $statement = $sql->prepareStatementForSqlObject($select);
        $resultSet = new ResultSet();
        
        try {
            $results = $statement->execute();
            $resultSet->initialize($results);
        } catch (Exception $e) {
            return FALSE;
        }
        
        $data = $resultSet->toArray();
        $aclService->setup($data);
        
        
        return $aclService;
    }
}