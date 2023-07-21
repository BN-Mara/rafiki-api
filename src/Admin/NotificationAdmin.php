<?php

namespace App\Admin;

use App\Entity\Competition;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;


final class NotificationAdmin extends AbstractAdmin{

    protected function configureFormFields(FormMapper $form): void
    {
       
        $form->add('title', TextType::class);
        $form->add('body', TextType::class);
        $form->add('type', TextType::class);
        $form->add('isSent', TextType::class);
        $form->add('users', TextType::class);

    }

    protected function configureDatagridFilters(DatagridMapper $datagrid): void
    {
        $datagrid->add('title');
        $datagrid->add('body');
        $datagrid->add('type');
    
    }

    protected function configureListFields(ListMapper $list): void
    {
        
        $list->addIdentifier('title');
        $list->addIdentifier('body');
        $list->addIdentifier('type');
        $list->addIdentifier('isSent');
        $list->addIdentifier('users');
        $list->addIdentifier('createdAt');
        
    }

    protected function configureShowFields(ShowMapper $show): void
    {
        $show->add('title');
        $show->add('body');
        $show->add('type');
        $show->add('isSent');
        $show->add('createdAt');
        $show->add('sentTime');
    }

}