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


final class UserReportAdmin extends AbstractAdmin{

    protected function configureFormFields(FormMapper $form): void
    {
       
        $form->add('category', TextType::class);
        $form->add('message', TextType::class);
        $form->add('userId', EmailType::class);
        $form->add('videoId', TextType::class);

    }

    protected function configureDatagridFilters(DatagridMapper $datagrid): void
    {
        $datagrid->add('category');
        $datagrid->add('message');
        $datagrid->add('userId');
        $datagrid->add('videoId');
        $datagrid->add('createdAt');
    
    }

    protected function configureListFields(ListMapper $list): void
    {
        
        $list->addIdentifier('category');
        $list->addIdentifier('message');
        $list->addIdentifier('userId');
        $list->addIdentifier('videoId');
        $list->addIdentifier('createdAt');
        
    }

    protected function configureShowFields(ShowMapper $show): void
    {
        $show->add('category');
        $show->add('message');
        $show->add('userId');
        $show->add('videoId');
        $show->add('createdAt');
    }
    

}