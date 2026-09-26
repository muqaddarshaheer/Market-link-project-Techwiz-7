<?php

/**
 * Shared symptom builders for crop-health knowledge.
 */
function cha_yes_no_pata(string $idPrefix = ''): array
{
    return [
        ['id' => 'yes', 'en' => 'Yes / Haan', 'ur' => 'Haan', 'icon' => '✅'],
        ['id' => 'no', 'en' => 'No / Nahi', 'ur' => 'Nahi', 'icon' => '⛔'],
        ['id' => 'unknown', 'en' => 'Pata nahi', 'ur' => 'Pata nahi', 'icon' => '❓'],
    ];
}

function cha_leaf_yellow_symptom(array $extraCauses = []): array
{
    return [
        'id' => 'yellow_leaf',
        'match' => ['yellow', 'peela', 'peele', 'peelay', 'pila', 'pile', 'پیلا', 'پیلے'],
        'parts' => ['leaf'],
        'label' => ['en' => 'Yellowing leaves', 'ur' => 'Patton ka peela hona'],
        'followups' => [
            [
                'id' => 'leaf_age',
                'field' => 'leaf_age',
                'en' => 'Is yellowing mostly on old leaves, new leaves, or both?',
                'ur' => 'Peela pan purane patton mein zyada hai, naye patton mein, ya dono mein?',
                'roman' => 'Peela pan purane patton mein zyada hai, naye mein, ya dono mein?',
                'options' => [
                    ['id' => 'old', 'en' => 'Old leaves', 'ur' => 'Purane patte', 'icon' => '🍂'],
                    ['id' => 'new', 'en' => 'New leaves', 'ur' => 'Naye patte', 'icon' => '🌿'],
                    ['id' => 'both', 'en' => 'Both', 'ur' => 'Dono', 'icon' => '🌱'],
                    ['id' => 'unknown', 'en' => 'Pata nahi', 'ur' => 'Pata nahi', 'icon' => '❓'],
                ],
            ],
            [
                'id' => 'soil_moisture',
                'field' => 'soil_moisture',
                'en' => 'Is the soil too wet, too dry, or normal?',
                'ur' => 'Mitti zyada geeli hai, zyada sukhi, ya normal?',
                'roman' => 'Mitti zyada geeli hai, zyada sukhi, ya normal?',
                'options' => [
                    ['id' => 'wet', 'en' => 'Too wet', 'ur' => 'Zyada geeli', 'icon' => '💧'],
                    ['id' => 'dry', 'en' => 'Too dry', 'ur' => 'Zyada sukhi', 'icon' => '☀️'],
                    ['id' => 'normal', 'en' => 'Normal', 'ur' => 'Normal', 'icon' => '👌'],
                    ['id' => 'unknown', 'en' => 'Pata nahi', 'ur' => 'Pata nahi', 'icon' => '❓'],
                ],
            ],
        ],
        'causes' => array_merge([
            ['en' => 'Possible water stress (too much or too little irrigation).', 'ur' => 'Mumkin pani ka masla (zyada ya kam pani).'],
            ['en' => 'Possible nutrient imbalance — check fertilizer history before adding more.', 'ur' => 'Mumkin ghizaiyat ka masla — zyada khad se pehle history check karein.'],
            ['en' => 'Possible root or disease pressure — look for spots, wilt, or insects.', 'ur' => 'Mumkin jarr/bimari — dagh, murjhana ya keere check karein.'],
            ['en' => 'Environmental stress (heat, sudden weather change) can also contribute.', 'ur' => 'Mausam/garami ka stress bhi wajah ho sakta hai.'],
        ], $extraCauses),
        'checklist' => [
            ['id' => 'spots', 'en' => 'Spots or patches on leaves?', 'ur' => 'Patton par dagh hain?'],
            ['id' => 'insects', 'en' => 'Insects under leaves?', 'ur' => 'Patton ke neeche keere?'],
            ['id' => 'wilt', 'en' => 'Plant also wilting?', 'ur' => 'Plant murjha bhi raha hai?'],
            ['id' => 'spread', 'en' => 'Nearby plants also affected?', 'ur' => 'Qareebi plants bhi affected?'],
        ],
        'avoid' => [
            ['en' => 'Do not apply random fertilizer or pesticide without checking.', 'ur' => 'Bina check kiye random khad/dawa na dein.'],
            ['en' => 'Do not overwater hoping it will fix yellow leaves.', 'ur' => 'Peelay patton ke liye andha-dhund pani na dein.'],
        ],
        'prevention' => [
            ['en' => 'Check irrigation and drainage first.', 'ur' => 'Pehle pani aur drainage check karein.'],
            ['en' => 'Monitor nearby plants daily.', 'ur' => 'Qareebi plants roz check karein.'],
            ['en' => 'Ask local agriculture advice if problem spreads fast.', 'ur' => 'Agar masla tez phailay to local agri mashwara lein.'],
        ],
        'spread' => true,
        'nutrition_note' => [
            'en' => 'Nutrition is only one possibility — confirm watering and leaf spots before assuming fertilizer will fix it.',
            'ur' => 'Nutrition sirf ek possibility hai — khad se pehle pani aur dagh check karein.',
        ],
    ];
}

function cha_pest_symptom(): array
{
    return [
        'id' => 'pest',
        'match' => ['keera', 'keere', 'keerey', 'insect', 'pest', 'कीड़े', 'کیڑے', 'worms', 'aphid', 'sundi'],
        'parts' => ['leaf', 'stem', 'fruit', 'whole'],
        'label' => ['en' => 'Possible pest activity', 'ur' => 'Keeron ka mumkin masla'],
        'followups' => [
            [
                'id' => 'pest_seen',
                'field' => 'pest_seen',
                'en' => 'Can you see insects on leaves or stems?',
                'ur' => 'Kya patton ya tane par keere nazar aa rahe hain?',
                'roman' => 'Kya patton ya tane par keere nazar aa rahe hain?',
                'options' => cha_yes_no_pata(),
            ],
            [
                'id' => 'spread_others',
                'field' => 'spread',
                'en' => 'Is it spreading to nearby plants?',
                'ur' => 'Kya masla qareebi plants mein bhi phail raha hai?',
                'roman' => 'Kya masla qareebi plants mein bhi phail raha hai?',
                'options' => cha_yes_no_pata(),
            ],
        ],
        'causes' => [
            ['en' => 'Possible insect feeding damage.', 'ur' => 'Mumkin keeron ka nuksan.'],
            ['en' => 'Humid weather can increase pest pressure.', 'ur' => 'Nami wala mausam keeron ko barha sakta hai.'],
        ],
        'checklist' => [
            ['id' => 'holes', 'en' => 'Holes or chewed edges?', 'ur' => 'Suraakh ya kate hue kinare?'],
            ['id' => 'underside', 'en' => 'Check underside of leaves', 'ur' => 'Patton ka neeche wala hissa check karein'],
        ],
        'avoid' => [
            ['en' => 'Do not guess pesticide dose — follow label and local advice.', 'ur' => 'Dawa ki miqdar andaza se na dein — label + local mashwara.'],
        ],
        'prevention' => [
            ['en' => 'Inspect plants regularly, especially leaf undersides.', 'ur' => 'Regular check karein, khas kar patton ke neeche.'],
            ['en' => 'Keep field hygiene; remove heavily damaged material when appropriate.', 'ur' => 'Field saaf rakhein; bohat kharab hisson ko alag karein.'],
        ],
        'spread' => true,
        'nutrition_note' => [
            'en' => 'If insects are visible, focus on pest checks before fertilizer.',
            'ur' => 'Agar keere nazar aayein to pehle keere check karein, khad baad mein.',
        ],
    ];
}

function cha_dry_wilt_symptom(): array
{
    return [
        'id' => 'drying',
        'match' => ['sukh', 'sukha', 'sukh rahi', 'dry', 'drying', 'murjha', 'wilt', 'wilted', 'سوکھ', 'مرجھ'],
        'parts' => ['leaf', 'whole'],
        'label' => ['en' => 'Drying / wilting leaves', 'ur' => 'Pattiyan sukh / murjha rahi hain'],
        'followups' => [
            [
                'id' => 'dry_pattern',
                'field' => 'dry_pattern',
                'en' => 'Are tips drying first, or the whole leaf?',
                'ur' => 'Tips se sukh rahi hain ya poori leaf?',
                'roman' => 'Tips se sukh rahi hain ya poori leaf?',
                'options' => [
                    ['id' => 'tips', 'en' => 'Tips first', 'ur' => 'Tips se', 'icon' => '🍃'],
                    ['id' => 'whole', 'en' => 'Whole leaf', 'ur' => 'Poori leaf', 'icon' => '🌿'],
                    ['id' => 'unknown', 'en' => 'Pata nahi', 'ur' => 'Pata nahi', 'icon' => '❓'],
                ],
            ],
            [
                'id' => 'soil_moisture',
                'field' => 'soil_moisture',
                'en' => 'Is soil too wet or too dry?',
                'ur' => 'Mitti zyada geeli hai ya zyada sukhi?',
                'roman' => 'Mitti zyada geeli hai ya zyada sukhi?',
                'options' => [
                    ['id' => 'wet', 'en' => 'Too wet', 'ur' => 'Zyada geeli', 'icon' => '💧'],
                    ['id' => 'dry', 'en' => 'Too dry', 'ur' => 'Zyada sukhi', 'icon' => '☀️'],
                    ['id' => 'unknown', 'en' => 'Pata nahi', 'ur' => 'Pata nahi', 'icon' => '❓'],
                ],
            ],
        ],
        'causes' => [
            ['en' => 'Possible water stress or heat stress.', 'ur' => 'Mumkin pani ya garmi ka stress.'],
            ['en' => 'Possible root issue or disease — check plant base and nearby plants.', 'ur' => 'Mumkin jarr/bimari — plant base aur qareebi plants check karein.'],
            ['en' => 'Pest damage can also cause drying tips.', 'ur' => 'Keere bhi tips sukha sakte hain.'],
        ],
        'checklist' => [
            ['id' => 'wilt', 'en' => 'Whole plant wilting?', 'ur' => 'Poora plant murjha raha hai?'],
            ['id' => 'insects', 'en' => 'Any insects?', 'ur' => 'Keere nazar aa rahe hain?'],
            ['id' => 'spread', 'en' => 'How many plants affected?', 'ur' => 'Kitne plants affected hain?'],
        ],
        'avoid' => [
            ['en' => 'Do not flood the field suddenly.', 'ur' => 'Achanak zyada pani na bhar dein.'],
        ],
        'prevention' => [
            ['en' => 'Stabilize irrigation; check drainage.', 'ur' => 'Pani regular rakhein; drainage check karein.'],
            ['en' => 'Monitor how fast symptoms move day by day.', 'ur' => 'Roz dekhein masla kitni tez chal raha hai.'],
        ],
        'spread' => true,
        'nutrition_note' => [
            'en' => 'Drying is not automatically a fertilizer issue — check water and roots first.',
            'ur' => 'Sukhna khud-ba-khud khad ka masla nahi — pehle pani aur jarr check karein.',
        ],
    ];
}

function cha_spots_symptom(): array
{
    return [
        'id' => 'spots',
        'match' => ['daag', 'dagh', 'spot', 'spots', 'patch', 'داغ', 'دھب'],
        'parts' => ['leaf', 'fruit', 'stem'],
        'label' => ['en' => 'Spots / patches on plant', 'ur' => 'Patton/phal par dagh'],
        'followups' => [
            [
                'id' => 'spot_color',
                'field' => 'spot_color',
                'en' => 'What color are the spots mostly?',
                'ur' => 'Dagh kis rang ke zyada hain?',
                'roman' => 'Dagh kis rang ke zyada hain?',
                'options' => [
                    ['id' => 'brown', 'en' => 'Brown', 'ur' => 'Bhure', 'icon' => '🟤'],
                    ['id' => 'black', 'en' => 'Black', 'ur' => 'Kale', 'icon' => '⚫'],
                    ['id' => 'yellow', 'en' => 'Yellow', 'ur' => 'Peele', 'icon' => '🟡'],
                    ['id' => 'white', 'en' => 'White', 'ur' => 'Safed', 'icon' => '⚪'],
                    ['id' => 'unknown', 'en' => 'Pata nahi', 'ur' => 'Pata nahi', 'icon' => '❓'],
                ],
            ],
            [
                'id' => 'spread_others',
                'field' => 'spread',
                'en' => 'Are nearby plants getting spots too?',
                'ur' => 'Kya qareebi plants mein bhi dagh aa rahe hain?',
                'roman' => 'Kya qareebi plants mein bhi dagh aa rahe hain?',
                'options' => cha_yes_no_pata(),
            ],
        ],
        'causes' => [
            ['en' => 'Possible fungal or bacterial leaf spot — needs field checking.', 'ur' => 'Mumkin fungal/bacterial dagh — field check zaroori.'],
            ['en' => 'Physical damage or spray burn can also look like spots.', 'ur' => 'Chot ya spray burn bhi dagh jaisa dikh sakta hai.'],
            ['en' => 'Weather (rain + humidity) can increase spot problems.', 'ur' => 'Barish + nami dagh wale maslay barha sakti hai.'],
        ],
        'checklist' => [
            ['id' => 'rain', 'en' => 'Recent rain / high humidity?', 'ur' => 'Recent barish / nami?'],
            ['id' => 'fruit', 'en' => 'Fruit also spotted?', 'ur' => 'Phal par bhi dagh?'],
        ],
        'avoid' => [
            ['en' => 'Do not spray unknown chemicals without guidance.', 'ur' => 'Bina mashware unknown chemical spray na karein.'],
        ],
        'prevention' => [
            ['en' => 'Improve airflow; avoid long leaf wetness when possible.', 'ur' => 'Hawa ka guzar behtar rakhein; patte zyada der geele na rahen.'],
            ['en' => 'Remove badly affected leaves if local advice supports it.', 'ur' => 'Bohat kharab patte local mashware se hataein.'],
        ],
        'spread' => true,
        'nutrition_note' => [
            'en' => 'Spots are usually not fixed by fertilizer alone.',
            'ur' => 'Dagh aksar sirf khad se theek nahi hote.',
        ],
    ];
}

/**
 * Generic black/dark change — used by most crops (potato has its own richer version).
 */
function cha_black_change_symptom(array $extraCauses = [], ?array $followups = null): array
{
    return [
        'id' => 'black_change',
        'match' => ['black', 'kala', 'kale', 'kaala', 'kaale', 'dark', 'siyah', 'کالا', 'کالے', 'سیاہ'],
        'parts' => ['leaf', 'stem', 'fruit', 'whole'],
        'label' => ['en' => 'Black / dark change on crop', 'ur' => 'Fasal par kala / dark pan'],
        'followups' => $followups ?? [
            [
                'id' => 'part',
                'field' => 'part',
                'en' => 'Where is the black/dark change?',
                'ur' => 'Kala pan kahan nazar aa raha hai?',
                'roman' => 'Kala pan kahan nazar aa raha hai?',
                'options' => [
                    ['id' => 'leaf', 'en' => 'Leaves', 'ur' => 'Patton par', 'icon' => '🌿'],
                    ['id' => 'stem', 'en' => 'Stem', 'ur' => 'Tana / stem', 'icon' => '🌱'],
                    ['id' => 'fruit', 'en' => 'Fruit / bulb / produce', 'ur' => 'Phal / bulb', 'icon' => '🧅'],
                    ['id' => 'whole', 'en' => 'Whole plant', 'ur' => 'Poora plant', 'icon' => '🪴'],
                    ['id' => 'unknown', 'en' => 'Pata nahi', 'ur' => 'Pata nahi', 'icon' => '❓'],
                ],
            ],
            [
                'id' => 'soil_moisture',
                'field' => 'soil_moisture',
                'en' => 'Is the soil too wet or too dry?',
                'ur' => 'Mitti zyada geeli hai ya zyada sukhi?',
                'roman' => 'Mitti zyada geeli hai ya zyada sukhi?',
                'options' => [
                    ['id' => 'wet', 'en' => 'Too wet', 'ur' => 'Zyada geeli', 'icon' => '💧'],
                    ['id' => 'dry', 'en' => 'Too dry', 'ur' => 'Zyada sukhi', 'icon' => '☀️'],
                    ['id' => 'normal', 'en' => 'Normal', 'ur' => 'Normal', 'icon' => '👌'],
                    ['id' => 'unknown', 'en' => 'Pata nahi', 'ur' => 'Pata nahi', 'icon' => '❓'],
                ],
            ],
        ],
        'causes' => array_merge([
            ['en' => 'Possible fungal mold or leaf/stem disease — needs field checking, not a confirmed diagnosis.', 'ur' => 'Mumkin fungal mold ya patta/tana masla — field check zaroori, confirmed bimari nahi.'],
            ['en' => 'Wet soil / poor drainage can encourage dark rotting tissues.', 'ur' => 'Geeli mitti / kharab drainage dark / sarrne wale maslay barha sakti hai.'],
            ['en' => 'Physical damage, heat, or spray burn can also turn tissue dark.', 'ur' => 'Chot, garmi, ya spray burn bhi dark dikha sakte hain.'],
            ['en' => 'Some crop varieties are naturally darker — color alone is not always a disease.', 'ur' => 'Kuch varieties naturally dark hoti hain — sirf color bimari nahi.'],
        ], $extraCauses),
        'checklist' => [
            ['id' => 'smell', 'en' => 'Any foul smell on plant/produce?', 'ur' => 'Koi badbu hai?'],
            ['id' => 'soft', 'en' => 'Is the dark part soft or firm?', 'ur' => 'Dark hissa soft hai ya sakht?'],
            ['id' => 'spread', 'en' => 'Nearby plants also darkening?', 'ur' => 'Qareebi plants bhi kale ho rahe?'],
            ['id' => 'wet', 'en' => 'Standing water near roots?', 'ur' => 'Jarro ke paas pani khara hai?'],
        ],
        'avoid' => [
            ['en' => 'Do not spray random chemicals just because color looks dark.', 'ur' => 'Sirf color dekh kar random dawa na spray karein.'],
            ['en' => 'Do not mix diseased and healthy produce in storage.', 'ur' => 'Kharab aur sehatmand produce ek storage mein na milayein.'],
        ],
        'prevention' => [
            ['en' => 'Improve drainage and avoid waterlogging.', 'ur' => 'Drainage behtar rakhein; pani khara na hone dein.'],
            ['en' => 'Separate suspicious plants/produce and watch for 2–3 days.', 'ur' => 'Mashkook plants alag karke 2–3 din dekhein.'],
            ['en' => 'Ask local agriculture officer if it spreads fast.', 'ur' => 'Agar tez phailay to local agri officer se mashwara lein.'],
        ],
        'spread' => true,
        'nutrition_note' => [
            'en' => 'Black/dark change is usually not fixed by fertilizer alone — check moisture, rot, and pests first.',
            'ur' => 'Kala pan aksar sirf khad se theek nahi hota — pehle nami, sarrna aur keere check karein.',
        ],
    ];
}

function cha_onion_black_symptom(): array
{
    $s = cha_black_change_symptom(
        [
            ['en' => 'Onion leaf tip blackening can relate to tip burn, thrips, or fungal leaf blight — check tips vs whole leaf.', 'ur' => 'Piyaz tip black tip-burn, thrips, ya leaf blight se related ho sakta hai — tips vs poori leaf dekhein.'],
            ['en' => 'Bulb surface darkening may relate to storage mold / black mold risk in humid storage — not automatic disease name.', 'ur' => 'Bulb surface dark storage mold risk se related ho sakta hai — automatic bimari naam nahi.'],
            ['en' => 'Red/dark onion varieties look purple-black naturally when growing — confirm it is a change vs normal color.', 'ur' => 'Laal/dark piyaz varieties naturally dark dikhti hain — pehle confirm karein ye change hai ya normal color.'],
        ],
        [
            [
                'id' => 'part',
                'field' => 'part',
                'en' => 'Where is the black color on onion?',
                'ur' => 'Piyaz mein kala pan kahan hai?',
                'roman' => 'Piyaz mein kala pan kahan hai — patti, bulb, ya tips?',
                'options' => [
                    ['id' => 'leaf', 'en' => 'Leaves / tops', 'ur' => 'Pattiyan / tops', 'icon' => '🌿'],
                    ['id' => 'fruit', 'en' => 'Bulb / skin', 'ur' => 'Bulb / skin', 'icon' => '🧅'],
                    ['id' => 'stem', 'en' => 'Neck / stem', 'ur' => 'Gardan / stem', 'icon' => '🌱'],
                    ['id' => 'whole', 'en' => 'Whole plant looks dark', 'ur' => 'Poora plant dark', 'icon' => '🪴'],
                    ['id' => 'unknown', 'en' => 'Pata nahi', 'ur' => 'Pata nahi', 'icon' => '❓'],
                ],
            ],
            [
                'id' => 'soil_moisture',
                'field' => 'soil_moisture',
                'en' => 'Is onion field soil too wet?',
                'ur' => 'Piyaz ki mitti zyada geeli hai?',
                'roman' => 'Piyaz ki mitti zyada geeli hai?',
                'options' => [
                    ['id' => 'wet', 'en' => 'Too wet', 'ur' => 'Zyada geeli', 'icon' => '💧'],
                    ['id' => 'dry', 'en' => 'Too dry', 'ur' => 'Zyada sukhi', 'icon' => '☀️'],
                    ['id' => 'normal', 'en' => 'Normal', 'ur' => 'Normal', 'icon' => '👌'],
                    ['id' => 'unknown', 'en' => 'Pata nahi', 'ur' => 'Pata nahi', 'icon' => '❓'],
                ],
            ],
        ]
    );
    $s['label'] = ['en' => 'Black / dark change on onion', 'ur' => 'Piyaz par kala / dark pan'];

    return $s;
}

function cha_common_symptoms(): array
{
    return [
        cha_leaf_yellow_symptom(),
        cha_black_change_symptom(),
        cha_pest_symptom(),
        cha_dry_wilt_symptom(),
        cha_spots_symptom(),
    ];
}

function cha_base_crop(string $id, string $en, string $ur, string $icon, array $aliases, array $symptoms, string $seasonEn, string $seasonUr): array
{
    return [
        'id' => $id,
        'name_en' => $en,
        'name_ur' => $ur,
        'icon' => $icon,
        'aliases' => $aliases,
        'season' => ['en' => $seasonEn, 'ur' => $seasonUr],
        'symptoms' => $symptoms,
        'sources' => [
            'Pakistan Agricultural Research Council (PARC) extension themes',
            'Provincial agriculture department farmer guidance patterns',
            'FAO crop production / plant health extension principles',
        ],
    ];
}
