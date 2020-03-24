<?php 
namespace Acl\Service\Factory;

use Acl\Service\AclService;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\Permissions\Acl\Acl;

class AclServiceFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config = $container->get('configuration');
        $aclService = new AclService(New Acl());
        $aclService->setup($config['acl']);
        
        return $aclService;
    }
}