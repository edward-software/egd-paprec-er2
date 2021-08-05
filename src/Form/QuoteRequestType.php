<?php

namespace App\Form;

use App\Entity\QuoteRequest;
use App\Form\DataTransformer\PostalCodeToStringTransformer;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class QuoteRequestType extends AbstractType
{

    private $transformer;

    /**
     * QuoteRequestPublicType constructor.
     * @param $transformer
     */
    public function __construct(PostalCodeToStringTransformer $transformer)
    {
        $this->transformer = $transformer;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('locale', ChoiceType::class, array(
                'choices' => $options['locales']
            ))
            ->add('businessName')
            ->add('civility', ChoiceType::class, array(
                'choices' => array(
                    'M',
                    'Mme'
                ),
                "choice_label" => function ($choiceValue, $key, $value) {
                    return 'General.' . $choiceValue;
                },
                'data' => 'M',
                'expanded' => true,
                'required' => true
            ))
            ->add('access', ChoiceType::class, array(
                "choices" => $options['access'],
                "choice_label" => function ($choiceValue, $key, $value) {
                    return 'Commercial.AccessList.' . $choiceValue;
                },
            ))
            ->add('floorNumber', ChoiceType::class, array(
                "choices" => $options['floorNumber'],
                'data' => '0',
                'required' => false
            ))
            ->add('staff', ChoiceType::class, array(
                "choices" => $options['staff'],
                "choice_label" => function ($choiceValue, $key, $value) {
                    return 'Commercial.StaffList.' . $choiceValue;
                },
            ))
            ->add('lastName', TextType::class)
            ->add('firstName', TextType::class)
            ->add('email', TextType::class)
            ->add('phone', TextType::class)
            ->add('isMultisite', ChoiceType::class, array(
                "choices" => array(0, 1),
                "choice_label" => function ($choiceValue, $key, $value) {
                    return 'General.' . $choiceValue;
                },
                "expanded" => false,
            ))
            ->add('address', TextType::class)
            ->add('postalCode', TextType::class, array(
                'invalid_message' => 'Public.Contact.PostalCodeError',
                'required' => true
            ))
            ->add('city', TextType::class)
            ->add('billingAddress', TextType::class)
            ->add('billingPostalCode', TextType::class, array(
                'invalid_message' => 'Public.Contact.PostalCodeError',
                'required' => true
            ))
            ->add('billingCity', TextType::class)
            ->add('comment', TextareaType::class)
            ->add('quoteStatus', ChoiceType::class, array(
                "choices" => $options['status'],
                "choice_label" => function ($choiceValue, $key, $value) {
                    return 'Commercial.QuoteStatusList.' . $choiceValue;
                }
            ))
            ->add('overallDiscount')
            ->add('salesmanComment', TextareaType::class)
            ->add('annualBudget')
            ->add('type', ChoiceType::class, array(
                'choices' => array(
                    'Regular' => 'regular',
                    'Ponctual' => 'ponctual',
                ),
                'empty_data' => 'ponctual',
                "choice_label" => function ($choiceValue, $key, $value) {
                    return 'Commercial.QuoteRequest.Type.' . ucfirst($choiceValue);
                },
                'required' => true,
                'expanded' => true
            ))
            ->add('frequency', ChoiceType::class, array(
                'choices' => array(
                    'Regular' => 'regular',
                    'Ponctual' => 'ponctual',
                ),
                'empty_data' => 'ponctual',
                "choice_label" => function ($choiceValue, $key, $value) {
                    return 'Commercial.QuoteRequest.' . ucfirst($choiceValue);
                },
                'required' => true,
                'expanded' => true
            ))
            ->add('frequencyTimes', ChoiceType::class, array(
                'choices' => array(
                    '0' => '0',
                    '1' => '1',
                    '2' => '2',
                    '3' => '3',
                    '4' => '4',
                    '5' => '5',
                    '6' => '6',
                    '7' => '7',
                    '8' => '8',
                    '9' => '9',
                    '10' => '10',
                    '11' => '11',
                    '12' => '12'
                ),
                'expanded' => false,
                'multiple' => false
            ))
            ->add('frequencyInterval', ChoiceType::class, array(
                'choices' => array(
                    'week' => 'week',
                    'month' => 'month',
                    'bimestre' => 'bimestre',
                    'quarter' => 'quarter',
                    'quadrimestre' => 'quadrimestre'
                ),
                "choice_label" => function ($choiceValue, $key, $value) {
                    return ($choiceValue) ? 'Public.Catalog.' . ucfirst($choiceValue) : '';
                },
                'expanded' => false,
                'multiple' => false
            ))
            ->add('reference')
            ->add('customerId')
            ->add('userInCharge', EntityType::class, array(
                'class' => User::class,
                'multiple' => false,
                'expanded' => false,
                'placeholder' => '',
                'empty_data' => null,
                'choice_label' => function (User $user) {
                    return $user->getFirstName() . ' ' . $user->getLastName();
                },
                'query_builder' => function (UserRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->where('u.deleted IS NULL')
                        ->andWhere('u.enabled = 1')
                        ->andWhere('u.roles LIKE \'%ROLE_COMMERCIAL%\'')
                        ->orderBy('u.firstName');
                }
            ))
            ->add('signatoryFirstName1')
            ->add('signatoryLastName1')
            ->add('signatoryTitle1');
        $builder->get('postalCode')
            ->addModelTransformer($this->transformer);
        $builder->get('billingPostalCode')
            ->addModelTransformer($this->transformer);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => QuoteRequest::class,
            'validation_groups' => function (FormInterface $form) {
                $data = $form->getData();
                if ($data->getIsMultisite() === 1) {
                    return ['default', 'public'];
                }
                return ['default', 'public_multisite'];
            },
            'status' => null,
            'locales' => null,
            'staff' => null,
            'access' => null,
            'floorNumber' => null
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'paprec_catalogbundle_quote_request';
    }


}
