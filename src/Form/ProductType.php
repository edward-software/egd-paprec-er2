<?php

namespace App\Form;

use App\Entity\BillingUnit;
use App\Entity\Product;
use App\Entity\Range;
use App\Repository\BillingUnitRepository;
use App\Repository\RangeRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class ProductType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('capacity')
            ->add('capacityUnit')
            ->add('dimensions', TextareaType::class)
            ->add('isEnabled', ChoiceType::class, array(
                "choices" => array(
                    0,
                    1
                ),
                "choice_label" => function ($choiceValue, $key, $value) {
                    return 'General.' . $choiceValue;
                },
                "expanded" => true,
            ))
            ->add('rentalUnitPrice', TextType::class, [
                "required" => true
            ])
            ->add('transportUnitPrice', TextType::class, [
                "required" => true
            ])
            ->add('treatmentUnitPrice', TextType::class, [
                "required" => true
            ])
            ->add('traceabilityUnitPrice', TextType::class, [
                "required" => true
            ])
            ->add('position')
            ->add('transportType', ChoiceType::class, array(
                "choices" => $options['transportTypes'],
                "choice_label" => function ($choiceValue, $key, $value) {
                    return 'General.TransportType.' . ucfirst($choiceValue);
                },
                "required" => true,
                "invalid_message" => 'Cannot be null',
                "expanded" => false,
                "multiple" => false,
                'constraints' => new NotBlank()
            ))
            ->add('catalog', ChoiceType::class, array(
                'choices' => array(
                    'Regular' => 'regular',
                    'Ponctual' => 'ponctual',
                ),
                'empty_data' => 'ponctual',
                "choice_label" => function ($choiceValue, $key, $value) {
                    return 'Catalog.Product.Catalog.' . ucfirst($choiceValue);
                },
                'required' => true,
                'expanded' => true
            ))
            ->add('frequency', ChoiceType::class, array(
                'choices' => array(
                    'Regular' => 'regular',
                    'Ponctual' => 'ponctual',
                    'Unknown' => 'unknown'
                ),
                'empty_data' => 'unknown',
                "choice_label" => function ($choiceValue, $key, $value) {
                    return 'General.Frequency.' . ucfirst($choiceValue);
                },
                'required' => true,
                'expanded' => true
            ))
            ->add('frequencyTimes', ChoiceType::class, array(
                'choices' => array(
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
                'multiple' => false,
                'required' => true
            ))
            ->add('frequencyInterval', ChoiceType::class, array(
                'choices' => array(
                    'week' => 'week',
                    'month' => 'month',
                    'bimestre' => 'bimestre',
                    'quarter' => 'quarter'
                ),
                "choice_label" => function ($choiceValue, $key, $value) {
                    return ($choiceValue) ? 'General.Frequency.' . ucfirst($choiceValue) : '';
                },
                'expanded' => false,
                'multiple' => false,
                'required' => true
            ))
            ->add('range', EntityType::class, array(
                'class' => Range::class,
                'query_builder' => function (RangeRepository $er) {
                    return $er->createQueryBuilder('r')
                        ->select(array('r', 'rL'))
                        ->leftJoin('r.rangeLabels', 'rL')
                        ->where('r.deleted IS NULL')
                        ->andWhere('rL.language = :language')
                        ->orderBy('r.position', 'ASC')
                        ->setParameter('language', 'FR')
                        ->andWhere('r.catalog != :catalog')
                        ->setParameter('catalog', 'MATERIAL');
                },
                'choice_label' => 'rangeLabels[0].name',
                'placeholder' => '',
                'empty_data' => null,
            ))
            ->add('billingUnit', EntityType::class, array(
                'class' => BillingUnit::class,
                'query_builder' => function (BillingUnitRepository $er) {
                    return $er->createQueryBuilder('bU')
                        ->select(array('bU'))
                        ->where('bU.deleted IS NULL');
                },
                'choice_label' => function ($billingUnit) {
                    return $billingUnit->getCode() . ' - ' .$billingUnit->getName();
                },
                'placeholder' => '',
                'empty_data' => null,
            ))
            ->add('wasteClassification', TextType::class)
            ->add('code', TextType::class)
            ->add('materialUnitPrice', TextType::class)
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Product::class,
            'transportTypes' => null,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'paprec_catalogbundle_product';
    }


}
