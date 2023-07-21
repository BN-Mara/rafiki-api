<?php

namespace App\Admin;

use App\Entity\Competition;
use App\Entity\UserData;
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
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

final class VideoDataAdmin extends AbstractAdmin{

    protected function configureFormFields(FormMapper $form): void
    {
       
        $form->add('userId', EntityType::class, [
            // looks for choices from this entity
            'class' => UserData::class,
        
            // uses the User.username property as the visible option string
            'choice_label' => 'name',
        
            // used to render a select box, check boxes or radios
            // 'multiple' => true,
            // 'expanded' => true,
        ]);
        $form->add('videoUrl', TextType::class);
        $form->add('status',CollectionType::class,[
            'entry_type'   => ChoiceType::class,
            'entry_options'  => [
                'choices'  => [
                    'IN' => 'IN',
                    'RED'     => 'RED',
                    'ORANGE'    => 'ORAGE',
                    'GREEN'    => 'GREEN',
                ],
            ]
        ]);
        $form->add('primeId', TextType::class);

    }

    protected function configureDatagridFilters(DatagridMapper $datagrid): void
    {
        $datagrid->add('userId.name');
        $datagrid->add('userId.email');
        $datagrid->add('primeId');
        $datagrid->add('userId.phone');
    
    }

    protected function configureListFields(ListMapper $list): void
    {
        
        $list->addIdentifier('userId.name');
        $list->addIdentifier('videoUrl');
        $list->addIdentifier('status');
        $list->addIdentifier('primeId');
        $list->addIdentifier('createdAt');
    }

    protected function configureShowFields(ShowMapper $show): void
    {
        $show->add('userId.name');
        $show->add('videoUrl');
        $show->add('primeId');
        $show->add('createdAt');
        $show->add('status');
    }

}