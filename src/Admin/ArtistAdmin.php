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
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;


final class ArtistAdmin extends AbstractAdmin{

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
        $form->add('firstName', TextType::class);
        $form->add('lastName', TextType::class);
        $form->add('numero', IntegerType::class);
        $form->add('gender', ChoiceType::class,[

            'choices'  => [
                'Male' => 'M',
                'Female' => 'F',
                
            ],
        ]);
        $form->add('birthDate', DateType::class,[
            'years'=>range(2005,2020)
        ]);
        $form->add('isActive', CheckboxType::class);

        $form->add('file', FileType::class,[
            'required' => false
        ]);
        $form->add('bio', TextareaType::class);
        

    }

    protected function configureDatagridFilters(DatagridMapper $datagrid): void
    {
        $datagrid->add('firstName');
        $datagrid->add('lastName');
        $datagrid->add('gender');
        $datagrid->add('competition.code');
        $datagrid->add('numero');
        $datagrid->add('isActive');
        $datagrid->add('birthDate');

    
    }

    protected function configureListFields(ListMapper $list): void
    {
        
        $list->addIdentifier('firstName');
        $list->addIdentifier('lastName');
        $list->addIdentifier('gender');
        $list->addIdentifier('numero');
        $list->addIdentifier('coverImage');
        $list->addIdentifier('competition.code');
        $list->add('isActive');
        $list->add('birthDate');
    }

    protected function configureShowFields(ShowMapper $show): void
    {
        $show->add('firstName');
        $show->add('lastName');
        $show->add('gender');
        $show->add('numero');
        $show->add('createdAt');
        $show->add('coverImage');
        $show->add('competition.code');
        $show->add('isActive');
        $show->add('birthDate');
    }
    public function prePersist(object $image): void
    {
        $image->setCoverImage("/");
        $this->manageFileUpload($image);
    }

    public function preUpdate(object $image): void
    {
        $image->setCoverImage("/");
        $this->manageFileUpload($image);
    }

    private function manageFileUpload(object $image): void
    {
        if ($image->getFile()) {
            $image->refreshUpdated();
        }
    }

}