<?php
namespace Acl\Form\Factory;

use Acl\Form\AclForm;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class AclFormFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $form = new AclForm();
        return $form;
    }
}