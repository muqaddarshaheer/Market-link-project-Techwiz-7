<?php

require_once __DIR__.'/_helpers.php';

$black = [
    'id' => 'black_change',
    'match' => ['black', 'kala', 'kale', 'kaala', 'kaale', 'dark', 'کالا', 'کالے'],
    'parts' => ['tuber_outside', 'tuber_inside', 'leaf'],
    'label' => ['en' => 'Black / dark change on potato', 'ur' => 'Aloo par kala / dark pan'],
    'followups' => [
        [
            'id' => 'part',
            'field' => 'part',
            'en' => 'Where do you see the black/dark change?',
            'ur' => 'Kala pan kahan nazar aa raha hai?',
            'roman' => 'Kala pan kahan nazar aa raha hai?',
            'options' => [
                ['id' => 'tuber_outside', 'en' => 'Outside / skin', 'ur' => 'Bahar / skin', 'icon' => '🥔'],
                ['id' => 'tuber_inside', 'en' => 'Inside', 'ur' => 'Andar', 'icon' => '🥔'],
                ['id' => 'leaf', 'en' => 'On leaves', 'ur' => 'Patton par', 'icon' => '🌿'],
                ['id' => 'stem', 'en' => 'Stem', 'ur' => 'Tana / stem', 'icon' => '🌱'],
                ['id' => 'unknown', 'en' => 'Pata nahi', 'ur' => 'Pata nahi', 'icon' => '❓'],
            ],
        ],
        [
            'id' => 'light_exposure',
            'field' => 'light_exposure',
            'when' => ['part' => 'tuber_outside'],
            'en' => 'Was the potato kept in light / sun for long?',
            'ur' => 'Kya aloo dhoop ya roshni mein raha tha?',
            'roman' => 'Kya aloo dhoop ya roshni mein raha tha?',
            'options' => cha_yes_no_pata(),
        ],
        [
            'id' => 'texture',
            'field' => 'texture',
            'when' => ['part' => 'tuber_inside'],
            'en' => 'Is the potato soft inside or still hard?',
            'ur' => 'Andar soft hai ya hard?',
            'roman' => 'Andar soft hai ya hard?',
            'options' => [
                ['id' => 'soft', 'en' => 'Soft', 'ur' => 'Soft / naram', 'icon' => '🟤'],
                ['id' => 'hard', 'en' => 'Hard', 'ur' => 'Hard / sakht', 'icon' => '⚪'],
                ['id' => 'unknown', 'en' => 'Pata nahi', 'ur' => 'Pata nahi', 'icon' => '❓'],
            ],
        ],
    ],
    'causes' => [
        ['en' => 'Outside darkening can relate to light exposure, bruising, or storage conditions — not always a disease.', 'ur' => 'Bahar ka dark pan light, chot, ya storage se ho sakta hai — hamesha bimari nahi.'],
        ['en' => 'Inside darkening with soft tissue could relate to soft rot / breakdown — needs careful checking.', 'ur' => 'Andar soft + dark soft rot/breakdown se related ho sakta hai — carefully check karein.'],
        ['en' => 'Wet soil / poor drainage around tubers can increase storage and rot risks.', 'ur' => 'Geeli mitti / kharab drainage risk barha sakti hai.'],
        ['en' => 'Physical damage can turn dark over time.', 'ur' => 'Chot waqt ke sath dark dikh sakti hai.'],
    ],
    'checklist' => [
        ['id' => 'smell', 'en' => 'Any foul smell?', 'ur' => 'Badbu hai?'],
        ['id' => 'others', 'en' => 'Nearby potatoes also affected?', 'ur' => 'Qareebi aloo bhi?'],
        ['id' => 'storage', 'en' => 'Storage wet/warm/closed?', 'ur' => 'Storage geeli/garam/band?'],
    ],
    'avoid' => [
        ['en' => 'Do not eat soft, foul-smelling potatoes; separate them.', 'ur' => 'Soft/badbudar aloo alag rakhein; use careful food-safety practice.'],
        ['en' => 'Do not claim a disease name without checking.', 'ur' => 'Bina check ke bimari ka naam na rakhein.'],
    ],
    'prevention' => [
        ['en' => 'Keep questionable tubers separate and monitor.', 'ur' => 'Mashkook aloo alag rakhein aur dekhein.'],
        ['en' => 'Keep tools/storage clean and dry.', 'ur' => 'Tools/storage saaf aur dry rakhein.'],
        ['en' => 'Avoid waterlogging in the field.', 'ur' => 'Field mein pani khara na hone dein.'],
    ],
    'spread' => true,
    'nutrition_note' => [
        'en' => 'For black tuber issues, nutrition is usually not the first assumption.',
        'ur' => 'Kale aloo ke liye nutrition pehla andaza nahi hona chahiye.',
    ],
];

return cha_base_crop(
    'potato',
    'Potato',
    'Aloo',
    '🥔',
    ['potato', 'potatoes', 'aloo', 'alu', 'alloo', 'aalu', 'alo', 'آلو'],
    [$black, cha_leaf_yellow_symptom(), cha_pest_symptom(), cha_dry_wilt_symptom(), cha_spots_symptom()],
    'Cool-season tuber crop — watch moisture, pests, and storage.',
    'Thande mausam ki fasal — pani, keere aur storage dekhein.'
);
