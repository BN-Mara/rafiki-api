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
use Symfony\Component\Form\Extension\Core\Type\TextType;


final class VoteAdmin extends AbstractAdmin{

    /*protected function configureFormFields(FormMapper $form): void
    {
        $form->add('competitionId', EntityType::class, [
            // looks for choices from this entity
            'class' => Competition::class,
        
            // uses the User.username property as the visible option string
            'choice_label' => 'name',
        
            // used to render a select box, check boxes or radios
            // 'multiple' => true,
            // 'expanded' => true,
        ]);
        $form->add('name', TextType::class);
        $form->add('isActive', CheckboxType::class);
        $form->add('startTime', DateTimeType::class);
        $form->add('endTime', DateTimeType::class);
        $form->add('createdBy', TextType::class);

    }*/

    protected function configureDatagridFilters(DatagridMapper $datagrid): void
    {
        $datagrid->add('artist.numero');
        $datagrid->add('artist.firstName');
        $datagrid->add('artist.lastName');
        $datagrid->add('isPayed');
        $datagrid->add('numberOfVote');
        $datagrid->add('prime.name');
        $datagrid->add('createdAt');
        $datagrid->add('payment.reference');
    
    }

    protected function configureListFields(ListMapper $list): void
    {
        
        $list->addIdentifier('artist.firstName');
        $list->addIdentifier('artist.numero');
        $list->addIdentifier('numberOfVote');
        $list->addIdentifier('isPayed');
        $list->addIdentifier('prime.name');
        $list->addIdentifier('createdAt');
        $list->addIdentifier('payment.reference');


    }

    protected function configureShowFields(ShowMapper $show): void
    {
        $show->add('artist.numero');
        $show->add('artist.firstName');
        $show->add('artist.lastName');
        $show->add('isPayed');
        $show->add('numberOfVote');
        $show->add('prime.name');
        $show->add('payment.reference');
        $show->add('createdAt');
    }

}