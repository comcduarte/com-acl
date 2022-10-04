<?php
namespace Acl\Controller;

use Components\Controller\AbstractBaseController;
use Laminas\Db\ResultSet\ResultSet;
use Laminas\Db\Sql\Select;
use Laminas\Db\Sql\Sql;
use Laminas\Db\Sql\Where;
use Laminas\View\Model\ViewModel;
use Exception;

class AclController extends AbstractBaseController
{
    public function indexAction()
    {
        $view = new ViewModel();
        $view = parent::indexAction();
        $view->setTemplate('base/subtable');
        
        /**
         * Override fetchall
         */
        $sql = new Sql($this->adapter);
        
        $select = new Select();
        $select->from($this->model->getTableName());
        $select->columns(['UUID','ROLE','RESOURCE','POLICY', 'PRIVILEGE']);
        $select->where(new Where());
        $select->order('ROLE DESC','RESOURCE');
        
        $statement = $sql->prepareStatementForSqlObject($select);
        $resultSet = new ResultSet();
        try {
            $results = $statement->execute();
            $resultSet->initialize($results);
        } catch (Exception $e) {
            return FALSE;
        }
        
        $records = $resultSet->toArray();
        $header = [];
        
        if (!empty($records)) {
            $header = array_keys($records[0]);
        }
        
        $params = [
            [
                'route' => 'acl/default',
                'action' => 'update',
                'key' => 'UUID',
                'label' => 'Update',
            ],
            [
                'route' => 'acl/default',
                'action' => 'delete',
                'key' => 'UUID',
                'label' => 'Delete',
            ],
        ];
        
        $view->setVariables([
            'data' => $records,
            'header' => $header,
            'title' => 'Access Control Lists',
            'params' => $params,
            'search' => true,
        ]);
        return $view;
    }
}