<?php

namespace App\Form;

use Doctrine\Persistence\ObjectManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;


use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;

use App\Form\DataTransformer\DataTransformer;
use App\Entity\BarangayEntity;
use App\Entity\CityEntity;




class BarangayForm extends AbstractType
{
   
    private $manager;

    public function __construct(ObjectManager $manager) {
        $this->manager = $manager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('action', HiddenType::class, array(
                'data' => $options['action'],
                'mapped' => false,
                'attr' => array(
                    'class' => 'form-action'
                )
            ))
            ->add('id', HiddenType::class)
            ->add('code', TextType::class, array(
                'label' => 'Code',
                'label_attr' => array(
                    'class' => 'middle'
                ),
                'attr' => [ 'class' => 'form-control'],
                'required' => false
            ))
            ->add('description', TextType::class, array(
                'label' => 'Description',
                'label_attr' => array(
                    'class' => 'middle required'
                ),
                'attr' => [ 'class' => 'form-control required'],
                'required' => true
            ))    
            ->add('city', HiddenType::class)
            ->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $event) {

                $form = $event->getForm();
                $data = $event->getData();
                $city = $data->getCity();
                $form
       
                    ->add('city_desc', TextType::class, array(
                        'label' => 'City',
                        'label_attr' => array(
                            'class' => 'middle required'
                        ),
                        'required' => true,
                        'attr' => array(                            
                            'class' => 'form-control',
                        ),
                        'mapped' => false,
                        'data' => $city ? $city->getDescription() : ''
                    ));
            });

           $builder->get('city')->addModelTransformer(new DataTransformer($this->manager, CityEntity::class, false));

    }

    public function getName()
    {
        return 'barangay';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' 	  => 'App\Entity\BarangayEntity',
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            // a unique key to help generate the secret token
            'intention'       => 'clientEntity_intention',
            'action'          => 'n',
        ));
    }
}