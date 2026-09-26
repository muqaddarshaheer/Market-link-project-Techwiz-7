<?php

require_once __DIR__.'/_helpers.php';

return cha_base_crop(
    'onion',
    'Onion',
    'Piyaz',
    '🧅',
    ['onion', 'onions', 'piyaz', 'pyaz', 'pyaaz', 'پیاز'],
    [
        cha_onion_black_symptom(),
        cha_dry_wilt_symptom(),
        cha_leaf_yellow_symptom(),
        cha_pest_symptom(),
        cha_spots_symptom(),
    ],
    'Watch tip burn, leaf drying, bulb mold risk in humid storage, and wet-field stress.',
    'Tips sukhna, pattiyan, bulb mold (nami storage), aur geeli mitti ka khayal rakhein.'
);
