<?php

require_once __DIR__.'/crops/_helpers.php';
require_once __DIR__.'/crops/potato.php';
// potato.php returns array when required - wait, it returns at end. So we can't require potato then also use registry.

/**
 * Expandable crop-health knowledge.
 * Causes are POSSIBLE only — never confirmed diagnoses.
 */
$potato = require __DIR__.'/crops/potato.php';
$tomato = require __DIR__.'/crops/tomato.php';
$onion = require __DIR__.'/crops/onion.php';

$simple = [
    'wheat' => ['Wheat', 'Gandum', '🌾', ['wheat', 'gandum', 'گندم'], 'Monitor spots, lodging, moisture after rain.', 'Dagh, girna, barish ke baad nami dekhein.'],
    'corn' => ['Corn / Maize', 'Makai', '🌽', ['corn', 'maize', 'makai', 'مکئی'], 'Watch yellowing, pests, heat.', 'Peela, keere, garmi dekhein.'],
    'rice' => ['Rice', 'Chawal', '🍚', ['rice', 'chawal', 'چاول'], 'Watch water level, spots, pests.', 'Pani, dagh, keere dekhein.'],
    'carrot' => ['Carrot', 'Gajar', '🥕', ['carrot', 'gajar', 'گاجر'], 'Watch leaf drying and roots.', 'Patte sukhna aur jarr dekhein.'],
    'cucumber' => ['Cucumber', 'Kheera', '🥒', ['cucumber', 'kheera', 'کھیرا'], 'Watch spots, wilt, pests.', 'Dagh, murjhana, keere dekhein.'],
    'chili' => ['Chili', 'Mirch', '🌶️', ['chili', 'chilli', 'mirch', 'مرچ'], 'Watch curl/yellowing and pests.', 'Murorna/peela, keere dekhein.'],
    'eggplant' => ['Eggplant', 'Baingan', '🍆', ['eggplant', 'baingan', 'brinjal', 'بینگن'], 'Watch wilt, spots, fruit pests.', 'Murjhana, dagh, keere dekhein.'],
    'spinach' => ['Spinach', 'Palak', '🥬', ['spinach', 'palak', 'پالک'], 'Watch yellowing and drying.', 'Peela aur sukhna dekhein.'],
    'okra' => ['Okra', 'Bhindi', '🪴', ['okra', 'bhindi', 'بھنڈی'], 'Watch yellowing and pests.', 'Peela aur keere dekhein.'],
    'cauliflower' => ['Cauliflower', 'Phool gobhi', '🥦', ['cauliflower', 'phool gobhi', 'gobhi'], 'Watch leaf spots and head quality.', 'Dagh aur head quality dekhein.'],
    'cabbage' => ['Cabbage', 'Band gobhi', '🥬', ['cabbage', 'band gobhi'], 'Watch pests and leaf damage.', 'Keere aur nuksan dekhein.'],
    'garlic' => ['Garlic', 'Lehsan', '🧄', ['garlic', 'lehsan', 'لہسن'], 'Watch tip drying.', 'Tips sukhna dekhein.'],
    'mango' => ['Mango', 'Aam', '🥭', ['mango', 'aam', 'آم'], 'Watch leaf drop and spots.', 'Patte girna aur dagh dekhein.'],
    'apple' => ['Apple', 'Seb', '🍎', ['apple', 'seb', 'سیب'], 'Watch spots and fruit quality.', 'Dagh aur phal quality dekhein.'],
    'kinnow' => ['Kinnow / Citrus', 'Kinnow', '🍊', ['kinnow', 'orange', 'citrus', 'malta', 'کنو'], 'Watch yellowing and fruit drop.', 'Peela aur phal girna dekhein.'],
    'guava' => ['Guava', 'Amrood', '🟢', ['guava', 'amrood', 'امرود'], 'Watch spots and fruit pests.', 'Dagh aur keere dekhein.'],
    'banana' => ['Banana', 'Kela', '🍌', ['banana', 'kela', 'کیلا'], 'Watch yellowing and wilt.', 'Peela aur murjhana dekhein.'],
    'watermelon' => ['Watermelon', 'Tarbooz', '🍉', ['watermelon', 'tarbooz', 'تربوز'], 'Watch wilt and spots.', 'Murjhana aur dagh dekhein.'],
    'grapes' => ['Grapes', 'Angoor', '🍇', ['grapes', 'angoor', 'انگور'], 'Watch leaf spots and berries.', 'Dagh aur dane dekhein.'],
    'tulsi' => ['Holy Basil / Tulsi', 'Tulsi', '🌿', ['tulsi', 'holy basil', 'basil', 'تلسی'], 'Watch yellowing, watering, and leaf drop.', 'Peela, pani, patte girna dekhein.'],
    'mint' => ['Mint', 'Pudina', '🌿', ['mint', 'pudina', 'podina', 'پودینہ'], 'Watch yellowing and soil moisture.', 'Peela aur mitti nami dekhein.'],
    'coriander' => ['Coriander', 'Dhania', '🌿', ['coriander', 'dhania', 'cilantro', 'دھنیا'], 'Watch bolting and yellow leaves.', 'Peela aur jaldi phool dekhein.'],
];

$crops = [
    'potato' => $potato,
    'tomato' => $tomato,
    'onion' => $onion,
];

foreach ($simple as $id => $d) {
    [$en, $ur, $icon, $aliases, $sEn, $sUr] = $d;
    $crops[$id] = cha_base_crop(
        $id,
        $en,
        $ur,
        $icon,
        $aliases,
        cha_common_symptoms(),
        $sEn,
        $sUr
    );
}

$crops['generic'] = cha_base_crop(
    'generic',
    'Crop',
    'Fasal',
    '🌱',
    ['fasal', 'crop', 'plant', 'pauday', 'پاودے', 'فصل'],
    cha_common_symptoms(),
    'General monitoring — moisture, pests, leaves, weather.',
    'General monitoring — pani, keere, patte, mausam.'
);

$crops['other'] = $crops['generic'];

return [
    'meta' => [
        'version' => 2,
        'languages' => ['ur', 'en', 'roman_ur'],
        'sources' => [
            'Pakistan Agricultural Research Council (PARC) extension themes',
            'Provincial agriculture department farmer guidance patterns',
            'FAO crop production / plant health extension principles',
        ],
    ],
    'crop_picker' => [
        'id' => 'crop',
        'en' => 'Which crop has this problem?',
        'ur' => 'Kaunsi fasal mein ye masla ho raha hai?',
        'roman' => 'Kaunsi fasal mein ye masla ho raha hai?',
        'options' => [
            ['id' => 'potato', 'en' => 'Potato / Aloo', 'ur' => 'Aloo', 'icon' => '🥔'],
            ['id' => 'tomato', 'en' => 'Tomato', 'ur' => 'Tamatar', 'icon' => '🍅'],
            ['id' => 'onion', 'en' => 'Onion / Piyaz', 'ur' => 'Piyaz', 'icon' => '🧅'],
            ['id' => 'wheat', 'en' => 'Wheat / Gandum', 'ur' => 'Gandum', 'icon' => '🌾'],
            ['id' => 'corn', 'en' => 'Corn / Makai', 'ur' => 'Makai', 'icon' => '🌽'],
            ['id' => 'rice', 'en' => 'Rice / Chawal', 'ur' => 'Chawal', 'icon' => '🍚'],
            ['id' => 'carrot', 'en' => 'Carrot / Gajar', 'ur' => 'Gajar', 'icon' => '🥕'],
            ['id' => 'cucumber', 'en' => 'Cucumber / Kheera', 'ur' => 'Kheera', 'icon' => '🥒'],
            ['id' => 'chili', 'en' => 'Chili / Mirch', 'ur' => 'Mirch', 'icon' => '🌶️'],
            ['id' => 'eggplant', 'en' => 'Eggplant / Baingan', 'ur' => 'Baingan', 'icon' => '🍆'],
            ['id' => 'spinach', 'en' => 'Spinach / Palak', 'ur' => 'Palak', 'icon' => '🥬'],
            ['id' => 'okra', 'en' => 'Okra / Bhindi', 'ur' => 'Bhindi', 'icon' => '🪴'],
            ['id' => 'cauliflower', 'en' => 'Cauliflower', 'ur' => 'Phool gobhi', 'icon' => '🥦'],
            ['id' => 'cabbage', 'en' => 'Cabbage', 'ur' => 'Band gobhi', 'icon' => '🥬'],
            ['id' => 'garlic', 'en' => 'Garlic / Lehsan', 'ur' => 'Lehsan', 'icon' => '🧄'],
            ['id' => 'mango', 'en' => 'Mango / Aam', 'ur' => 'Aam', 'icon' => '🥭'],
            ['id' => 'apple', 'en' => 'Apple / Seb', 'ur' => 'Seb', 'icon' => '🍎'],
            ['id' => 'kinnow', 'en' => 'Kinnow / Orange', 'ur' => 'Kinnow', 'icon' => '🍊'],
            ['id' => 'guava', 'en' => 'Guava / Amrood', 'ur' => 'Amrood', 'icon' => '🟢'],
            ['id' => 'banana', 'en' => 'Banana / Kela', 'ur' => 'Kela', 'icon' => '🍌'],
            ['id' => 'watermelon', 'en' => 'Watermelon', 'ur' => 'Tarbooz', 'icon' => '🍉'],
            ['id' => 'grapes', 'en' => 'Grapes / Angoor', 'ur' => 'Angoor', 'icon' => '🍇'],
            ['id' => 'tulsi', 'en' => 'Tulsi / Basil', 'ur' => 'Tulsi', 'icon' => '🌿'],
            ['id' => 'mint', 'en' => 'Mint / Pudina', 'ur' => 'Pudina', 'icon' => '🌿'],
            ['id' => 'coriander', 'en' => 'Coriander / Dhania', 'ur' => 'Dhania', 'icon' => '🌿'],
            ['id' => 'other', 'en' => 'Other plant', 'ur' => 'Doosra plant', 'icon' => '🌱'],
            ['id' => 'unknown', 'en' => 'Pata nahi', 'ur' => 'Pata nahi', 'icon' => '❓'],
        ],
    ],
    'crops' => $crops,
];
