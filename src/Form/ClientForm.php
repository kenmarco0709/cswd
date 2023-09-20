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
use Symfony\Bridge\Doctrine\Form\Type\EntityType;


use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;

use App\Form\DataTransformer\DataTransformer;
use App\Form\DataTransformer\DatetimeTransformer;

use App\Entity\ClientEntity;
use App\Entity\ProvinceEntity;
use App\Entity\BarangayEntity;
use App\Entity\BranchEntity;
use App\Entity\DeseaseEntity;
use App\Entity\UserEntity;


class ClientForm extends AbstractType
{
   
    private $manager;
    private $userData;
    private $formAction;
    private $encoderId;

    public function __construct(ObjectManager $manager) {
        $this->manager = $manager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $this->userData = $options['userData'];
        $this->formAction = $options['action'];

        $builder
            ->add('action', HiddenType::class, array(
                'data' => $options['action'],
                'mapped' => false,
                'attr' => array(
                    'class' => 'form-action'
                )
            ))
            ->add('id', HiddenType::class)
            ->add('case_no', TextType::class, array(
                'label' => 'Case No.',
                'label_attr' => array(
                    'class' => 'middle'
                ),
                'attr' => [ 'class' => 'form-control'],
                'required' => false
            ))
            ->add('first_name', TextType::class, array(
                'label' => 'First Name',
                'label_attr' => array(
                    'class' => 'middle'
                ),
                'attr' => [ 'class' => 'form-control'],
                'required' => false
            ))
            ->add('middle_name', TextType::class, array(
                'label' => 'Middle Name',
                'label_attr' => array(
                    'class' => 'middle'
                ),
                'attr' => [ 'class' => 'form-control'],
                'required' => false
            ))
            ->add('last_name', TextType::class, array(
                'label' => 'Last Name',
                'label_attr' => array(
                    'class' => 'middle'
                ),
                'attr' => [ 'class' => 'form-control'],
                'required' => false
            ))
            ->add('worker', TextType::class, array(
                'label' => 'Worker',
                'label_attr' => array(
                    'class' => 'middle'
                ),
                'attr' => [ 'class' => 'form-control'],
                'required' => false
            ))
            ->add('patient_first_name', TextType::class, array(
                'label' => 'First Name',
                'label_attr' => array(
                    'class' => 'middle'
                ),
                'attr' => [ 'class' => 'form-control'],
                'required' => false
            ))
            ->add('patient_middle_name', TextType::class, array(
                'label' => 'Middle Name',
                'label_attr' => array(
                    'class' => 'middle'
                ),
                'attr' => [ 'class' => 'form-control'],
                'required' => false
            ))
            ->add('patient_last_name', TextType::class, array(
                'label' => 'Last Name',
                'label_attr' => array(
                    'class' => 'middle'
                ),
                'attr' => [ 'class' => 'form-control'],
                'required' => false
            ))
            ->add('occupation', TextType::class, array(
                'label' => 'Occupation',
                'label_attr' => array(
                    'class' => 'middle'
                ),
                'attr' => [ 'class' => 'form-control'],
                'required' => false
            ))
            ->add('religion', TextType::class, array(
                'label' => 'Religion',
                'label_attr' => array(
                    'class' => 'middle'
                ),
                'attr' => [ 'class' => 'form-control'],
                'required' => false
            ))
            ->add('contact_no', TextType::class, array(
                'label' => 'Contact No.',
                'label_attr' => array(
                    'class' => 'middle'
                ),
                'attr' => [ 'class' => 'form-control'],
                'required' => false
            ))
            ->add('approve_amount', TextType::class, array(
                'label' => 'Approved Amount.',
                'label_attr' => array(
                    'class' => 'middle'
                ),
                'attr' => [ 'class' => 'form-control amt'],
                'required' => false
            ))
            ->add('income', TextType::class, array(
                'label' => 'Monthly Income',
                'label_attr' => array(
                    'class' => 'middle'
                ),
                'attr' => [ 'class' => 'form-control amt'],
                'required' => false
            ))
            ->add('years_from_current_city', TextType::class, array(
                'label' => 'Number of years in Current City',
                'label_attr' => array(
                    'class' => 'middle'
                ),
                'attr' => [ 'class' => 'form-control'],
                'required' => false
            ))
            ->add('problem_presented', TextareaType::class, array(
                'label' => '',
                'label_attr' => array(
                    'class' => 'middle'
                ),
                'attr' => [ 'class' => 'form-control'],
                'required' => false
            ))
            ->add('birth_place', TextType::class, array(
                'label' => 'Birth Place',
                'label_attr' => array(
                    'class' => 'middle'
                ),
                'attr' => [ 'class' => 'form-control'],
                'required' => false
            ))
            ->add('branch', HiddenType::class, array('data' => $options['branchId']))
            ->add('caseType', ChoiceType::class, [
                'choices'  => $options['caseTypes'],
                'choice_attr' => [ 'class' => 'form-control', 'data-childelem' => 'client_form_specified_case_type'],
                'expanded' => true,
                'multiple' => true,
                'mapped' => false
            ])
            ->add('gender', ChoiceType::class, [
                'choices'  => $options['genders'],
                'expanded' => true
            ])
            ->add('treatment', ChoiceType::class, [
                'choices'  => $options['treatments'],
                'expanded' => true
            ])
            ->add('housing', ChoiceType::class, [
                'choices'  => [
                    'Owned' => 'Owned',
                    'Rented' => 'Rented',
                    'Amortized' => 'Amortized',
                    'Shared' => 'Shared',
                    'Caretaker' => 'Caretaker'
                ],
                'expanded' => true
            ])
            ->add('housing_structure', ChoiceType::class, [
                'choices'  => [
                    'Makeshift/Dilapidated' => 'Makeshift/Dilapidated',
                    'Light Materials' => 'Light Materials',
                    'Concrete' => 'Concrete',
                    'Combined heavy and light materials' => 'Combined heavy and light materials'
                ],
                'expanded' => true
            ])
            ->add('lot', ChoiceType::class, [
                'choices'  => [
                    'Owned' => 'Owned',
                    'Amortized' => 'Amortized',
                    'Squatter' => 'Squatter',
                    'Sharer' => 'Sharer',
                    'Rented' => 'Rented'
                ],
                'expanded' => true
            ])
            ->add('lightning', ChoiceType::class, [
                'choices'  => [
                    'Kerosene Lamp' => 'Kerosene Lamp',
                    'Electricity' => 'Electricity',
                    'Candle' => 'Candle',
                    'Shared' => 'Shared',
                    'Owned' => 'Owned'
                ],
                'expanded' => true,
                'multiple' => true,
                'mapped' => false
            ])
            ->add('assistantType', ChoiceType::class, [
                'choices'  => $options['assistantTypes'],               
                'choice_attr' => [ 'class' => 'form-control', 'data-childelem' => 'client_form_desease'],
                'expanded' => true,
                'multiple' => true,
                'mapped' => false
            ])
            ->add('desease', ChoiceType::class, array(
                'label' => 'Desease ',
                'choices'  => $options['deseases'],               
                'attr' => [ 'class' => 'form-control '],
                'required' => false


            ))
            ->add('civil_status', ChoiceType::class, [
                'choices'  => $options['civilStatuses'],               
                'attr' => [ 'class' => 'form-control ', 'data-childelem' => 'client_form_specified_civil_status' ],

            ])
            ->add('specified_civil_status', TextType::class, array(
                'label' => 'Specify ',
                'label_attr' => array(
                    'class' => 'middle'
                ),
                'attr' => [ 'class' => 'form-control'],
                'required' => false
            ))
            ->add('specified_case_type', TextType::class, array(
                'label' => 'Specify ',
                'label_attr' => array(
                    'class' => 'middle'
                ),
                'attr' => [ 'class' => 'form-control'],
                'required' => false
            ))
            ->add('relation_to_patient', ChoiceType::class, [
                'label' => 'Relationship to patient',
                'choices'  => $options['patientRelationships'],               
                'attr' => [ 'class' => 'form-control ']

            ])
            ->add('educational_attainment', ChoiceType::class, [                
                'choices'  => $options['educationalAttainments'],
                'label_attr' => array(
                    'class' => 'middle required  col-form-label'
                ),
                'attr' => [ 'class' => 'form-control ']
            ])
            ->add('barangay', HiddenType::class)
            ->add('barangay_address', HiddenType::class,[
                'required' => false
            ])
            ->add('intake_date', TextType::class, array(
                'label' => 'Intake Date',
                'label_attr' => array(
                    'class' => 'middle required  col-form-label'
                ),
                'attr' => [ 'class' => 'form-control datepicker '],
                'required' => false
            ))
            ->add('release_date', TextType::class, array(
                'label' => 'Release Date',
                'label_attr' => array(
                    'class' => 'middle required  col-form-label'
                ),
                'attr' => [ 'class' => 'form-control datepicker '],
                'required' => false
            ))
            ->add('birth_date', TextType::class, array(
                'label' => 'Birth Date',
                'label_attr' => array(
                    'class' => 'middle required  col-form-label'
                ),
                'attr' => [ 'class' => 'form-control datepicker '],
                'required' => false
            ))
            ->add('desease', HiddenType::class,[
                'required' => false
            ])
           
            ->add('encoder', HiddenType::class,[
                'required' => false,
                'mapped' => true,
                'data' => $this->userData['type'] == 'Encoder' && $this->formAction == 'n'  ? $this->userData['id'] : $options['encoderId'] 

            ])            
            ->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $event, $options) use ($builder){
               
                $form = $event->getForm();
                $data = $event->getData();
                $barangay = $data->getBarangay();
                $barangayAddress = $data->getBarangayAddress();
                $desease = $data->getDesease();
                $encoder = $data->getEncoder();


                $form
                    ->add('barangay_desc', TextType::class, array(
                        'label' => 'Barangay',
                        'label_attr' => array(
                            'class' => 'middle required  col-form-label'
                        ),
                        'required' => false,
                        'attr' => array(                            
                            'class' => 'form-control'

                        ),
                        'mapped' => false,
                        'data' => $barangay ?  $barangay->getDescription() .' ' . $barangay->getCity()->getDescription() . ', ' . $barangay->getCity()->getProvince()->getDescription() : ''
                    ))
                    ->add('barangay_address_desc', TextType::class, array(
                        'label' => 'Address',
                        'label_attr' => array(
                            'class' => 'middle required  col-form-label'
                        ),
                        'required' => false,
                        'attr' => array(                            
                            'class' => 'form-control'

                        ),
                        'mapped' => false,
                        'data' => $barangayAddress ?  $barangayAddress->getDescription() .' ' . $barangayAddress->getCity()->getDescription() . ', ' . $barangayAddress->getCity()->getProvince()->getDescription() : ''
                    ))
                    ->add('desease_desc', TextType::class, array(
                        'label' => 'Desease',
                        'label_attr' => array(
                            'class' => 'middle required  col-form-label'
                        ),
                        'required' => false,
                        'attr' => array(                            
                            'class' => 'form-control'

                        ),
                        'mapped' => false,
                        'data' => $desease ?  $desease->getDescription()  : ''
                    ))
                    ->add('encoder_fullname', TextType::class, array(
                        'label' => 'Encoder',
                        'label_attr' => array(
                            'class' => 'middle required  col-form-label'
                        ),
                        'required' => false,
                        'attr' => array(                            
                            'class' => 'form-control',
                            'readonly' => $this->userData['type'] == 'Encoder' && $this->formAction == 'n' ? true : false

                        ),
                        'mapped' => false,
                        'data' => $this->userData['type'] == 'Encoder' && $this->formAction == 'n' ? $this->userData['fullName'] : ($encoder ? $encoder->getFullName() : '')
                    ));


            });

            $builder->get('branch')->addModelTransformer(new DataTransformer($this->manager, BranchEntity::class, false, $options['branchId']));
            $builder->get('intake_date')->addModelTransformer(new DatetimeTransformer());
            $builder->get('release_date')->addModelTransformer(new DatetimeTransformer());
            $builder->get('birth_date')->addModelTransformer(new DatetimeTransformer());
            $builder->get('barangay')->addModelTransformer(new DataTransformer($this->manager, BarangayEntity::class, false));
            $builder->get('barangay_address')->addModelTransformer(new DataTransformer($this->manager, BarangayEntity::class, false));
            $builder->get('desease')->addModelTransformer(new DataTransformer($this->manager, DeseaseEntity::class, false));
            $builder->get('encoder')->addModelTransformer(new DataTransformer($this->manager, UserEntity::class, false, ($this->userData['type'] == 'Encoder' && $this->formAction == 'n' ?  $this->userData['id'] :  $options['encoderId'])));








    }

    public function getName()
    {
        return 'client';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' 	  => 'App\Entity\ClientEntity',
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            // a unique key to help generate the secret token
            'intention'       => 'clientEntity_intention',
            'action'          => 'n',
            'branchId'        => null,
            'caseTypes'       => [],
            'assistantTypes'  => [],
            'genders'       => [],
            'treatments'       => [],
            'civilStatuses'       => [],
            'educationalAttainments'       => [],
            'patientRelationships'       => [],  
            'deseases'       => [],
            'userData' => [],
            'encoderId' => null,
            'allow_extra_fields' => true,

        ));
    }
}