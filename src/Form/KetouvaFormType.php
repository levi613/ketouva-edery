<?php

namespace App\Form;;

use App\Constant\StatutKala as ConstantStatutKala;
use App\Constant\TitrePersonne as ConstantTitrePersonne;
use App\Entity\Annee;
use App\Entity\JourMois;
use App\Entity\JourSemaine;
use App\Entity\Ketouva;
use App\Entity\Mois;
use App\Repository\AnneeRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class KetouvaFormType extends AbstractType
{
    protected $anneeRepository;

    public function __construct(AnneeRepository $anneeRepository)
    {
        $this->anneeRepository = $anneeRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $titres = [
            ConstantTitrePersonne::REB => ConstantTitrePersonne::REB,
            ConstantTitrePersonne::HARAV => ConstantTitrePersonne::HARAV,
            'Rien' => ConstantTitrePersonne::RIEN
        ];

        $statutsKala = [];
        if ($options['type'] != '5050') {
            $statutsKala[] = [ConstantStatutKala::BETOULA['hebreu'] => ConstantStatutKala::BETOULA['hebreu']];
        }

        $statutsKala[] = [
            ConstantStatutKala::NON_BETOULA['hebreu'] => ConstantStatutKala::NON_BETOULA['hebreu'],
            ConstantStatutKala::DIVORCEE['hebreu'] => ConstantStatutKala::DIVORCEE['hebreu'],
            ConstantStatutKala::VEUVE['hebreu'] => ConstantStatutKala::VEUVE['hebreu'],
            ConstantStatutKala::CONVERTIE['hebreu'] => ConstantStatutKala::CONVERTIE['hebreu'],
        ];

        $builder
            ->add('jourSemaine', EntityType::class, [
                'placeholder' => 'Choisir un jour',
                'label' => 'Jour de la semaine',
                'class' => JourSemaine::class,
                'choice_label' => function (JourSemaine $jourSemaine) {
                    return strtoupper($jourSemaine->getFrancais());
                }
            ])
            ->add('jourMois', EntityType::class, [
                'placeholder' => 'Choisir un jour',
                'label' => 'Jour du mois',
                'class' => JourMois::class,
                'choice_label' => 'num'
            ])
            ->add('mois', EntityType::class, [
                'placeholder' => 'Choisir un mois',
                'label' => 'Mois',
                'class' => Mois::class,
                'choice_label' => function (Mois $mois) {
                    return strtoupper($mois->getFrancais());
                }
            ])
            ->add('ville', TextType::class, [
                'attr' => [
                    'dir' => 'rtl'
                ],
                'label' => $options['type'] == 'taouta' || $options['type'] == 'irseka' ? 'Ville de la remise de la ketouva' : null,
                'required' => false
            ])
            ->add('titreHatan', ChoiceType::class, [
                'choices' => $titres,
                'expanded' => true,
                'multiple' => false,
                'label_attr' => [
                    'class' => 'radio-inline'
                ],
                'attr' => [
                    // 'dir' => 'rtl'
                ]
            ])
            ->add('nomHatan', TextType::class, [
                'label' => 'Prénom',
                'attr' => [
                    'dir' => 'rtl'
                ],
                'required' => false
            ])
            ->add('titrePereHatan', ChoiceType::class, [
                'choices' => $titres,
                'expanded' => true,
                'multiple' => false,
                'label_attr' => [
                    'class' => 'radio-inline'
                ],
                'attr' => [
                    // 'dir' => 'rtl'
                ]
            ])
            ->add('nomPereHatan', TextType::class, [
                'label' => 'Prénom du père',
                'attr' => [
                    'dir' => 'rtl'
                ],
                'required' => false
            ])
            ->add('nomKala', TextType::class, [
                'label' => 'Prénom',
                'attr' => [
                    'dir' => 'rtl'
                ],
                'required' => false
            ])
            ->add('titrePereKala', ChoiceType::class, [
                'choices' => $titres,
                'expanded' => true,
                'multiple' => false,
                'label_attr' => [
                    'class' => 'radio-inline'
                ],
                'attr' => [
                    // 'dir' => 'rtl'
                ]
            ])
            ->add('nomPereKala', TextType::class, [
                'label' => 'Prénom du père',
                'attr' => [
                    'dir' => 'rtl'
                ],
                'required' => false
            ])
            ->add('nomFichier', TextType::class, [
                'label' => 'Nom du fichier',
                'required' => false
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Enregistrer'
            ])
            ->add('reset', ResetType::class, [
                'label' => 'Réinitialiser'
            ]);

        if ($options['type'] != 'habad' && $options['type'] != 'sefarad') {
            $builder->add('statutKala', ChoiceType::class, [
                'choices' => $statutsKala,
                'required' => true,
                'expanded' => true,
                'multiple' => false,
                'label_attr' => [
                    'class' => 'radio-inline'
                ],
                'choice_attr' => function ($choice, $key, $value) {
                    return ['onclick' => 'autoRempli()'];
                }
            ]);
        }

        if ($options['type'] != '5050') {
            $builder->add('orpheline', CheckboxType::class, [
                'required' => false
            ]);
        }

        if ($options['type'] == 'taouta' || $options['type'] == 'irseka') {
            $builder
                ->add('jourSemaineMariage', EntityType::class, [
                    'placeholder' => 'Choisir un jour',
                    'label' => 'Jour de la semaine',
                    'required' => false,
                    'class' => JourSemaine::class,
                    'choice_label' => function (JourSemaine $jourSemaine) {
                        return strtoupper($jourSemaine->getFrancais());
                    }
                ])
                ->add('jourMoisMariage', EntityType::class, [
                    'placeholder' => 'Choisir un jour',
                    'label' => 'Jour du mois',
                    'required' => false,
                    'class' => JourMois::class,
                    'choice_label' => 'num'
                ])
                ->add('moisMariage', EntityType::class, [
                    'placeholder' => 'Choisir un mois',
                    'label' => 'Mois',
                    'required' => false,
                    'class' => Mois::class,
                    'choice_label' => function (Mois $mois) {
                        return strtoupper($mois->getFrancais());
                    }
                ])
                ->add('villeMariage', TextType::class, [
                    'attr' => [
                        'dir' => 'rtl'
                    ],
                    'label' => 'Ville du mariage',
                    'required' => false
                ]);

            if ($options['type'] == 'irseka') {
                $builder->add('dateMariageConnue', CheckboxType::class, [
                    'required' => false,
                    'label' => 'Je connais la date précise du mariage.',
                    'attr' => ['onclick' => 'afficheDateMariage()']

                ]);
            }
        }

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $form = $event->getForm();

            // reccupérer l'année en hébreu en cours
            $jd = gregoriantojd(date("m"), date("d"), date("Y"));
            $tabDateHe = cal_from_jd($jd, CAL_JEWISH);
            $anneeDefault = $this->anneeRepository->findOneBy(['num' => $tabDateHe['year']]);


            /** @var Ketouva */
            $ketouva = $event->getData();

            if ($ketouva->getId() == null) {
                $form
                    ->add('annee', EntityType::class, [
                        'placeholder' => 'Choisir une année',
                        'label' => 'Année',
                        'class' => Annee::class,
                        'data' => $anneeDefault,
                        'choice_label' => 'num'
                    ]);

                if ($ketouva->getTypeKetouva() == 'taouta' || $ketouva->getTypeKetouva() == 'irseka') {
                    $form->add('anneeMariage', EntityType::class, [
                        'placeholder' => 'Choisir une année',
                        'label' => 'Année',
                        'required' => false,
                        'class' => Annee::class,
                        'data' => $anneeDefault,
                        'choice_label' => 'num'
                    ]);
                }
            } else {
                $form
                    ->add('annee', EntityType::class, [
                        'placeholder' => 'Choisir une année',
                        'label' => 'Année',
                        'class' => Annee::class,
                        'choice_label' => 'num'
                    ]);

                if ($ketouva->getTypeKetouva() == 'taouta' || $ketouva->getTypeKetouva() == 'irseka') {
                    $form->add('anneeMariage', EntityType::class, [
                        'placeholder' => 'Choisir une année',
                        'label' => 'Année',
                        'required' => false,
                        'class' => Annee::class,
                        'choice_label' => 'num'
                    ]);
                }
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Ketouva::class,
            'type' => ''
        ]);
    }
}
