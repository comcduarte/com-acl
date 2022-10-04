<?php
namespace Acl\View\Helper\Factory;

use Acl\View\Helper\MultiRoleNavigation;
use Psr\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class MultiRoleNavigationFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $plugin = new MultiRoleNavigation();
        $plugin->setServiceLocator($container);
        $plugin->setDefaultRole('EVERYONE');
        return $plugin;
    }
}