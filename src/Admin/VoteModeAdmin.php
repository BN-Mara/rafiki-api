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
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;


final class VoteModeAdmin extends AbstractAdmin{

    protected function configureFormFields(FormMapper $form): void
    {
        $form->add('competition', EntityType::class, [
            // looks for choices from this entity
            'class' => Competition::class,
        
            // uses the User.username property as the visible option string
            'choice_label' => 'name',
        
            // used to render a select box, check boxes or radios
            // 'multiple' => true,
            // 'expanded' => true,
        ]);
        $form->add('description', TextType::class);
        $form->add('price', NumberType::class);
        $form->add('numberOfVote', IntegerType::class);

    }

    protected function configureDatagridFilters(DatagridMapper $datagrid): void
    {
        $datagrid->add('description');
        $datagrid->add('price');
        $datagrid->add('numberOfVote');
    
    }

    protected function configureListFields(ListMapper $list): void
    {
        
        $list->addIdentifier('description');
        $list->addIdentifier('price');
        $list->addIdentifier('numberOfVote');
        $list->addIdentifier('createdAt');
        $list->addIdentifier('competition.name');
    }

    protected function configureShowFields(ShowMapper $show): void
    {
        $show->add('description');
        $show->add('price');
        $show->add('numberOfVote');
        $show->add('createdAt');
        $show->add('competition.name');
    }

}