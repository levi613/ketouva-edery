<?php

namespace App\Constant;

class OptionPissoul
{
    public const SAFEK = 'ספק';
    public const PISSOUL = 'פיסול';


    public const BEKIDOUCHIN = 'בקידושין';
    public const BEEDEI_HAKIDOUCHIN = 'בעדי הקידושין';


    public const CINQUANTE_LITRIN = [
        'base' => 'חמישין לטרין',
        'ajout' => 'עוד חמישין לטרין סך הכל מאה לטרין'
    ];

    public const VINGT_CINQ_LITRIN = [
        'base' => 'חמשה ועשרין לטרין',
        'ajout' => 'עוד חמשה ועשרין לטרין סך הכל חמשין לטרין'
    ];

    public const CENT_KESSEF = [
        'base' => 'מאה זקוקים כסף צרוף',
        'ajout' => 'עוד מאה זקוקים כסף צרוף סך הכל מאתים זקוקים כסף צרוף'
    ];

    public const CINQUANTE_KESSEF = [
        'base' => 'חמישים זקוקים כסף צרוף',
        'ajout' => 'עוד חמישים כסף צרוףסך הכל מאה זקוקים כסף צרוף'
    ];

    public const CENT_ZOUZ = 'כסף זוזי מאה';
    public const DEUX_CENT_ZOUZ = 'כסף זוזי מאתן';

    public const DEFAULT = [
        'base' => '',
        'ajout' => ''
    ];
}
