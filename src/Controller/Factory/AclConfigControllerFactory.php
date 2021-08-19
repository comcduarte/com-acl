<?php
namespace Acl\Controller\Factory;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Acl\Controller\AclConfigController;

class AclConfigControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $controller = new AclConfigController();
        $adapter = $container->get('acl-model-adapter');
        
        $controller->setDbAdapter($adapter);
        $controller->setConfig($container->get('config')['acl']);
        return $controller;
    }
}