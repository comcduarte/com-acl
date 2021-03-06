<?php 
namespace Acl\Form;

use Components\Form\AbstractBaseForm;
use Laminas\Form\Element\Text;

class AclForm extends AbstractBaseForm
{
    public function init()
    {
        parent::init();
        
        $this->add([
            'name' => 'ROLE',
            'type' => Text::class,
            'attributes' => [
                'class' => 'form-control',
                'id' => 'ROLE',
                'required' => 'true',
                'placeholder' => '',
            ],
            'options' => [
                'label' => 'Role',
            ],
        ],['priority' => 100]);
        
        $this->add([
            'name' => 'RESOURCE',
            'type' => Text::class,
            'attributes' => [
                'class' => 'form-control',
                'id' => 'RESOURCE',
                'required' => 'true',
                'placeholder' => '',
            ],
            'options' => [
                'label' => 'Resource',
            ],
        ],['priority' => 100]);
        
        $this->add([
            'name' => 'PRIVILEGE',
            'type' => Text::class,
            'attributes' => [
                'class' => 'form-control',
                'id' => 'PRIVILEGE',
                'placeholder' => '',
            ],
            'options' => [
                'label' => 'Privilege',
            ],
        ],['priority' => 100]);
        
        $this->add([
            'name' => 'POLICY',
            'type' => Text::class,
            'attributes' => [
                'class' => 'form-control',
                'id' => 'POLICY',
                'placeholder' => '',
            ],
            'options' => [
                'label' => 'Policy',
            ],
        ],['priority' => 100]);
    }
}