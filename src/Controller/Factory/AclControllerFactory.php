<?php
namespace Acl\Controller\Factory;

use Acl\Controller\AclController;
use Acl\Form\AclForm;
use Acl\Model\AclModel;
use Psr\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class AclControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $controller = new AclController();
        
        $adapter = $container->get('acl-model-adapter');
        
        $model = new AclModel($adapter);
        $form = $container->get('FormElementManager')->get(AclForm::class);
        
        $controller->setModel($model);
        $controller->setForm($form);
        $controller->setDbAdapter($adapter);
        return $controller;
    }
}