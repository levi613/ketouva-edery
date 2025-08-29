<?php

namespace App\Form;;

use App\Constant\Dechirer;
use App\Constant\OptionPissoul;
use App\Constant\ProvenanceKala;
use App\Constant\StatutKala as ConstantStatutKala;
use App\Constant\StatutKetouva;
use App\Constant\TitrePersonne as ConstantTitrePersonne;
use App\Constant\TypeKetouva;
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
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
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
        $titres = [];
        foreach ((new \ReflectionClass(ConstantTitrePersonne::class))->getConstants() as $value) {
            if ($value == '') {
                $titres['Rien'] = $value;
            } else {
                $titres[$value] = $value;
            }
        }

        $statutKetouva = [];
        foreach ((new \ReflectionClass(StatutKetouva::class))->getConstants() as $value) {
            if ($value == '') {
                $statutKetouva['Rien'] = $value;
            } else {
                $statutKetouva[$value] = $value;
            }
        }

        $statutsKala = [];
        if ($options['type'] != TypeKetouva::CINQUANTE) {
            $statutsKala[] = [ConstantStatutKala::BETOULA['hebreu'] => ConstantStatutKala::BETOULA['hebreu']];
        }

        foreach ((new \ReflectionClass(ConstantStatutKala::class))->getConstants() as $value) {
            if ($value['hebreu'] != 'בתולתא' && $value['hebreu'] != '') {
                $statutsKala[$value['hebreu']] = $value['hebreu'];
            }
        }

        $provenancesKala = [];
        foreach ((new \ReflectionClass(ProvenanceKala::class))->getConstants() as $value) {
            if ($value == '') {
                $provenancesKala['Rien'] = $value;
            } else {
                $provenancesKala[$value] = $value;
            }
        }

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
                'label' => $options['type'] == TypeKetouva::TAOUTA || $options['type'] == TypeKetouva::IRKESSA || $options['type'] == TypeKetouva::NIKREA || $options['type'] == TypeKetouva::PISSOUL ? 'Ville de la remise de la ketouva' : null,
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
            ->add('nomFamilleHatan', TextType::class, [
                'label' => 'Nom de famille (écrit juste après le prénom du hatan, avant "ben...")',
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
            ->add('nomFamilleKala', TextType::class, [
                'label' => 'Nom de famille (écrit juste après le prénom de la kala, avant "bat...")',
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
            ->add('statutKetouva', ChoiceType::class, [
                'choices' => $statutKetouva,
                'label' => 'Cocher une case :',
                'expanded' => true,
                'multiple' => false,
                'label_attr' => [
                    'class' => 'radio-inline'
                ],
                'attr' => [
                    // 'dir' => 'rtl'
                ]
            ])
            ->add('ajustFontSizeInPdf', NumberType::class, [
                'label' => 'Ajuster la taille de la police dans le PDF',
                'required' => false,
                'help' => 'par exemple : -0.1 pour réduire la taille de 0.1 ou 0.1 pour augmenter la taille de 0.1',
                'attr' => [
                    'step' => 0.1,
                ],
            ])
            ->add('ecartLigne', NumberType::class, [
                'label' => 'Taille de l\'écart entre les lignes dans le PDF',
                'required' => false,
                'attr' => [
                    'step' => 0.1,
                ],
            ])
            ->add('nomFichier', TextType::class, [
                'label' => 'Nom du fichier',
                'required' => false
            ])
            // ->add('salleFrancais', TextType::class, [
            //     'label' => 'Salle du mariage (français)',
            //     'required' => false
            // ])
            ->add('villeFrancais', TextType::class, [
                'label' => 'Ville en français',
                'required' => false
            ])
            ->add('codePostalFrancais', TextType::class, [
                'label' => 'Code postal',
                'required' => false
            ])
            ->add('dateFrancais', TextType::class, [
                'label' => 'Date en français',
                'required' => false
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Enregistrer'
            ])
            ->add('reset', ResetType::class, [
                'label' => 'Réinitialiser'
            ]);


        if ($options['type'] != TypeKetouva::PISSOUL) {
            $builder->add('provenanceKala', ChoiceType::class, [
                'choices' => $provenancesKala,
                'label' => 'Cocher une case :',
                'required' => true,
                'expanded' => true,
                'multiple' => false,
                'label_attr' => [
                    'class' => 'radio-inline'
                ]
            ]);
        }

        if ($options['type'] != TypeKetouva::BETOULA) {
            $builder->add('statutKala', ChoiceType::class, [
                'choices' => $statutsKala,
                'label' => 'Cocher une case :',
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

        // if ($options['type'] != TypeKetouva::CINQUANTE) {
        //     $builder->add('orpheline', CheckboxType::class, [
        //         'required' => false
        //     ]);
        // }

        if ($options['type'] == TypeKetouva::TAOUTA || $options['type'] == TypeKetouva::IRKESSA || $options['type'] == TypeKetouva::NIKREA || $options['type'] == TypeKetouva::PISSOUL) {
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

            if ($options['type'] == TypeKetouva::IRKESSA) {
                $builder->add('dateMariageConnue', CheckboxType::class, [
                    'required' => false,
                    'label' => 'Je connais la date précise du mariage.',
                    'attr' => ['onclick' => 'afficheDateMariage()']

                ]);
            }

            if ($options['type'] == TypeKetouva::NIKREA) {
                $dechirer = [];
                foreach ((new \ReflectionClass(Dechirer::class))->getConstants() as $value) {
                    $dechirer[$value] = $value;
                }

                $builder->add('dechirer', ChoiceType::class, [
                    'choices' => $dechirer,
                    'expanded' => true,
                    'multiple' => false,
                    'label_attr' => [
                        'class' => ''
                    ],
                    'attr' => [
                        // 'dir' => 'rtl'
                    ]
                ]);
            }

            if ($options['type'] == TypeKetouva::PISSOUL) {
                $optionPissoul1 = [OptionPissoul::SAFEK => OptionPissoul::SAFEK, OptionPissoul::PISSOUL => OptionPissoul::PISSOUL];
                $optionPissoul2 = [
                    OptionPissoul::BEKIDOUCHIN => OptionPissoul::BEKIDOUCHIN,
                    OptionPissoul::BEEDEI_HAKIDOUCHIN => OptionPissoul::BEEDEI_HAKIDOUCHIN
                ];
                $optionPissoul3 = [OptionPissoul::CENT_ZOUZ => OptionPissoul::CENT_ZOUZ, OptionPissoul::DEUX_CENT_ZOUZ => OptionPissoul::DEUX_CENT_ZOUZ];
                $prixPissoul = [
                    OptionPissoul::CINQUANTE_LITRIN['base'] => OptionPissoul::CINQUANTE_LITRIN['base'],
                    OptionPissoul::VINGT_CINQ_LITRIN['base'] => OptionPissoul::VINGT_CINQ_LITRIN['base'],
                    OptionPissoul::CENT_KESSEF['base'] => OptionPissoul::CENT_KESSEF['base'],
                    OptionPissoul::CINQUANTE_KESSEF['base'] => OptionPissoul::CINQUANTE_KESSEF['base']
                ];

                $builder
                    ->add('optionPissoul1', ChoiceType::class, [
                        'choices' => $optionPissoul1,
                        'expanded' => true,
                        'multiple' => false,
                        'label_attr' => [
                            'class' => ''
                        ],
                        'required' => true,
                        'attr' => [
                            // 'dir' => 'rtl'
                        ],
                        'label' => 'Option 1 :'
                    ])
                    ->add('optionPissoul2', ChoiceType::class, [
                        'choices' => $optionPissoul2,
                        'expanded' => true,
                        'multiple' => false,
                        'label_attr' => [
                            'class' => ''
                        ],
                        'required' => true,
                        'attr' => [
                            // 'dir' => 'rtl'
                        ],
                        'label' => 'Option 2 :'
                    ])
                    ->add('optionPissoul3', ChoiceType::class, [
                        'choices' => $optionPissoul3,
                        'expanded' => true,
                        'multiple' => false,
                        'label_attr' => [
                            'class' => ''
                        ],
                        'required' => true,
                        'attr' => [
                            // 'dir' => 'rtl'
                        ],
                        'label' => 'Option 3 :'
                    ])
                    ->add('prixPissoul', ChoiceType::class, [
                        'choices' => $prixPissoul,
                        'expanded' => true,
                        'multiple' => false,
                        'label_attr' => [
                            'class' => ''
                        ],
                        'required' => true,
                        'attr' => [
                            // 'dir' => 'rtl'
                        ],
                        'label' => 'Option 4 :'
                    ]);
            }
        } else {
            $builder->add('hatanBahour', CheckboxType::class, [
                'label' => 'בחור',
                'required' => false,
                'label_attr' => [
                    'class' => 'checkbox-inline'
                ],
                'attr' => [
                    // 'dir' => 'rtl'
                ]
            ]);
        }

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $form = $event->getForm();

            // reccupérer l'année en hébreu en cours
            $jd = gregoriantojd(date("m"), date("d"), date("Y"));
            $tabDateHe = cal_from_jd($jd, CAL_JEWISH);
            $anneeDefault = $this->anneeRepository->findOneBy(['num' => $tabDateHe['year']]);


            /** @var Ketouva */
            $ketouva = $event->getData();

            if (!$ketouva->getId()) {
                $form
                    ->add('annee', EntityType::class, [
                        'placeholder' => 'Choisir une année',
                        'label' => 'Année',
                        'class' => Annee::class,
                        'data' => $anneeDefault,
                        'choice_label' => 'num'
                    ]);

                if ($ketouva->getTypeKetouva() == TypeKetouva::TAOUTA || $ketouva->getTypeKetouva() == TypeKetouva::IRKESSA || $ketouva->getTypeKetouva() == TypeKetouva::NIKREA || $ketouva->getTypeKetouva() == TypeKetouva::PISSOUL) {
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

                if ($ketouva->getTypeKetouva() == TypeKetouva::TAOUTA || $ketouva->getTypeKetouva() == TypeKetouva::IRKESSA || $ketouva->getTypeKetouva() == TypeKetouva::NIKREA || $ketouva->getTypeKetouva() == TypeKetouva::PISSOUL) {
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
