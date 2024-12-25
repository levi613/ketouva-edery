<?php

namespace App\Constant;

class StatutKala
{
    public const BETOULA = [
        'hebreu' => 'בתולתא',
        'typePaiement' => 'מהר בתוליכי',
        'prix' => 'כסף זוזי מאתן',
        'statutKetouva' => 'מדאורייתא',
        'beMoitiePrix' => 'במאה',
        'moitiePrix' => 'מאה',
        'prix2' => 'מאתים'
    ];
    public const NON_BETOULA = [
        'hebreu' => 'איתתא',
        'typePaiement' => 'כסף כתובתיך',
        'prix' => 'מאה',
        'statutKetouva' => 'מדרבנן',
        'beMoitiePrix' => 'בחמשים',
        'moitiePrix' => 'חמשים',
        'prix2' => 'מאה'
    ];
    public const DIVORCEE = [
        'hebreu' => 'מתרכתא',
        'typePaiement' => 'כסף מתרכותיכי',
        'prix' => 'מאה',
        'statutKetouva' => 'מדרבנן',
        'beMoitiePrix' => 'בחמשים',
        'moitiePrix' => 'חמשים',
        'prix2' => 'מאה'
    ];
    public const VEUVE = [
        'hebreu' => 'ארמלתא',
        'typePaiement' => 'כסף ארמלותיכי',
        'prix' => 'מאה',
        'statutKetouva' => 'מדרבנן',
        'beMoitiePrix' => 'בחמשים',
        'moitiePrix' => 'חמשים',
        'prix2' => 'מאה'
    ];
    public const CONVERTIE = [
        'hebreu' => 'גיורתא',
        'typePaiement' => 'כסף כתובתיך',
        'prix' => 'מאה',
        'statutKetouva' => 'מדרבנן',
        'beMoitiePrix' => 'בחמשים',
        'moitiePrix' => 'חמשים',
        'prix2' => 'מאה'
    ];

    public const DEFAULT = [
        'hebreu' => '',
        'typePaiement' => '',
        'prix' => '',
        'statutKetouva' => '',
        'beMoitiePrix' => '',
        'moitiePrix' => '',
        'prix2' => ''
    ];
}
