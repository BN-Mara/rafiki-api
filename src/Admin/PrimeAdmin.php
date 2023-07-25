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


final class PrimeAdmin extends AbstractAdmin{

    protected function configureFormFields(FormMapper $form): void
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

    }

    protected function configureDatagridFilters(DatagridMapper $datagrid): void
    {
        $datagrid->add('name');
        $datagrid->add('isActive');
    
    }

    protected function configureListFields(ListMapper $list): void
    {
        
        $list->addIdentifier('name');
        $list->addIdentifier('isActive');
        $list->addIdentifier('startTime');
        $list->addIdentifier('endTime');
        $list->addIdentifier('competitionId.code');
    }

    protected function configureShowFields(ShowMapper $show): void
    {
        $show->add('name');
        $show->add('isActive');
        $show->add('startTime');
        $show->add('endTime');
        $show->add('createdBy');
    }

    public function prePersist(object $object): void
    {
        //$plainPassword = $object->getPassword();
        if($object->isIsActive()){

            $em  = $this->getModelManager()->getEntityManager(Prime::class);
            $comp = $object->getCompetitionId();
            foreach($comp as $c){
                $c->setIsActive(false);
            }
            $em->flush();
            $object->setIsActive(true);
        }

    }     
     

    
    public function preUpdate(object $object): void
    {
        if($object->isIsActive()){

            $em  = $this->getModelManager()->getEntityManager(Prime::class);
            $comp = $object->getCompetitionId();
            foreach($comp as $c){
                $c->setIsActive(false);
            }
            $em->flush();
            $object->setIsActive(true);
        }
             $object->setModificationTime(new \DateTime('now',new \DateTimeZone('Africa/Kinshasa')));

    }

}