<?php

return [
    'currency' => 'Rs.',
    'currency_code' => 'PKR',

    'crops' => [
        'potato' => ['en' => 'Potato', 'ur' => 'آلو', 'icon' => '🥔'],
        'wheat' => ['en' => 'Wheat', 'ur' => 'گندم', 'icon' => '🌾'],
        'rice' => ['en' => 'Rice', 'ur' => 'چاول', 'icon' => '🍚'],
        'corn' => ['en' => 'Corn', 'ur' => 'مکئی', 'icon' => '🌽'],
        'tomato' => ['en' => 'Tomato', 'ur' => 'ٹماٹر', 'icon' => '🍅'],
        'onion' => ['en' => 'Onion', 'ur' => 'پیاز', 'icon' => '🧅'],
        'carrot' => ['en' => 'Carrot', 'ur' => 'گاجر', 'icon' => '🥕'],
        'cucumber' => ['en' => 'Cucumber', 'ur' => 'کھیرا', 'icon' => '🥒'],
        'chili' => ['en' => 'Chili', 'ur' => 'مرچ', 'icon' => '🌶️'],
        'spinach' => ['en' => 'Spinach', 'ur' => 'پالک', 'icon' => '🥬'],
        'eggplant' => ['en' => 'Eggplant', 'ur' => 'بینگن', 'icon' => '🍆'],
        'broccoli' => ['en' => 'Broccoli', 'ur' => 'بروکلی', 'icon' => '🥦'],
        'mango' => ['en' => 'Mango', 'ur' => 'آم', 'icon' => '🥭'],
        'apple' => ['en' => 'Apple', 'ur' => 'سیب', 'icon' => '🍎'],
        'kinnow' => ['en' => 'Kinnow / Orange', 'ur' => 'کنو', 'icon' => '🍊'],
        'watermelon' => ['en' => 'Watermelon', 'ur' => 'تربوز', 'icon' => '🍉'],
        'grapes' => ['en' => 'Grapes', 'ur' => 'انگور', 'icon' => '🍇'],
        'other' => ['en' => 'Other Crop', 'ur' => 'اور فصل', 'icon' => '🌱'],
    ],

    /*
     | All land units convert to square meters (sqm) as the base.
     | Marla / Bigha have local variants — farmer chooses the standard.
     */
    'land_units' => [
        'marla_272' => [
            'label' => 'Marla (272.25 sq ft)',
            'to_sqm' => 25.2929,
            'group' => 'marla',
        ],
        'marla_225' => [
            'label' => 'Marla (225 sq ft)',
            'to_sqm' => 20.9032,
            'group' => 'marla',
        ],
        'kanal' => [
            'label' => 'Kanal',
            // 20 × marla 272.25
            'to_sqm' => 505.857,
            'group' => 'kanal',
        ],
        'acre' => [
            'label' => 'Acre / Killa',
            'to_sqm' => 4046.86,
            'group' => 'acre',
        ],
        'hectare' => [
            'label' => 'Hectare',
            'to_sqm' => 10000,
            'group' => 'hectare',
        ],
        'bigha_punjab' => [
            'label' => 'Bigha (Punjab ~4 Kanal)',
            'to_sqm' => 2023.43,
            'group' => 'bigha',
        ],
        'bigha_up' => [
            'label' => 'Bigha (common ~27,000 sq ft)',
            'to_sqm' => 2508.38,
            'group' => 'bigha',
        ],
        'murabba' => [
            'label' => 'Murabba (~25 Acre)',
            'to_sqm' => 101171.41,
            'group' => 'murabba',
        ],
        'sqft' => [
            'label' => 'Square Feet',
            'to_sqm' => 0.092903,
            'group' => 'area',
        ],
        'sqyd' => [
            'label' => 'Square Yard / Gaz',
            'to_sqm' => 0.836127,
            'group' => 'area',
        ],
        'sqm' => [
            'label' => 'Square Meter',
            'to_sqm' => 1,
            'group' => 'area',
        ],
    ],

    'land_helpers' => [
        '1 Kanal ≈ 20 Marla (same marla standard)',
        '1 Acre / Killa ≈ 8 Kanal',
        '1 Hectare ≈ 2.471 Acre',
        'Marla size can be 272.25 or 225 sq ft — choose your area standard',
    ],

    /*
     | Quantity / mass units convert to kilograms where possible.
     | Discrete units (packet, plant, crate) stay as "count" and must match price unit family.
     */
    'qty_units' => [
        'g' => ['label' => 'Gram', 'family' => 'mass', 'to_kg' => 0.001],
        'kg' => ['label' => 'Kilogram (Kg)', 'family' => 'mass', 'to_kg' => 1],
        'maund' => ['label' => 'Maund', 'family' => 'mass', 'to_kg' => 40],
        'ton' => ['label' => 'Ton', 'family' => 'mass', 'to_kg' => 1000],
        'packet' => ['label' => 'Seed Packet', 'family' => 'count', 'to_kg' => null],
        'plant' => ['label' => 'Plants', 'family' => 'count', 'to_kg' => null],
        'crate' => ['label' => 'Crate', 'family' => 'count', 'to_kg' => null],
        'other' => ['label' => 'Other', 'family' => 'count', 'to_kg' => null],
    ],

    'price_units' => [
        'per_g' => ['label' => 'Per Gram', 'family' => 'mass', 'per_kg_factor' => 1000],
        'per_kg' => ['label' => 'Per Kg', 'family' => 'mass', 'per_kg_factor' => 1],
        'per_maund' => ['label' => 'Per Maund', 'family' => 'mass', 'per_kg_factor' => 1 / 40],
        'per_ton' => ['label' => 'Per Ton', 'family' => 'mass', 'per_kg_factor' => 1 / 1000],
        'per_crate' => ['label' => 'Per Crate', 'family' => 'count', 'per_kg_factor' => null],
        'per_plant' => ['label' => 'Per Plant', 'family' => 'count', 'per_kg_factor' => null],
        'per_other' => ['label' => 'Other', 'family' => 'count', 'per_kg_factor' => null],
    ],

    /*
     | Practical crop notes for farmers (estimates / extension-style tips).
     | Shown beside the calculator when a crop is selected.
     */
    'crop_insights' => [
        'potato' => [
            'season_en' => 'Cool season (often Oct–Feb planting in many areas)',
            'season_ur' => 'ٹھنڈا موسم (بہت سی جگہوں پر اکتوبر–فروری بوائی)',
            'tips_en' => [
                'Keep soil moist but avoid waterlogging — tubers rot in standing water.',
                'Store harvested potatoes in dark, cool, dry place to reduce greening.',
                'Watch for soft/dark tubers and separate them early.',
            ],
            'tips_ur' => [
                'مٹی نم رکھیں مگر پانی نہ کھڑا ہونے دیں — tubers سڑ سکتے ہیں۔',
                'کاٹی ہوئی آلو کو اندھیری، ٹھنڈی، خشک جگہ رکھیں تاکہ سبز نہ ہوں۔',
                'نرم/کالے tubers الگ کر لیں تاکہ مسئلہ نہ پھیلے۔',
            ],
            'watch_en' => 'Late blight risk rises in humid / rainy spells.',
            'watch_ur' => 'نمی اور بارش میں late blight کا خطرہ بڑھ سکتا ہے۔',
        ],
        'wheat' => [
            'season_en' => 'Rabi crop — timely sowing improves yield',
            'season_ur' => 'ربیع فصل — وقت پر بوائی پیداوار بہتر کرتی ہے',
            'tips_en' => [
                'Avoid delayed sowing if possible; late wheat often yields less.',
                'Do not over-irrigate after flowering without need.',
                'Check lodging risk after heavy wind/rain.',
            ],
            'tips_ur' => [
                'ممکن ہو تو دیر سے بوائی سے بچیں — پیداوار کم ہو سکتی ہے۔',
                'پھول آنے کے بعد بلا ضرورت زیادہ پانی نہ دیں۔',
                'تیز ہوا/بارش کے بعد lodging کا خیال رکھیں۔',
            ],
            'watch_en' => 'Rust / leaf spots — inspect fields after humid weather.',
            'watch_ur' => 'زنگ / پتوں کے داغ — نمی والے موسم کے بعد فیلڈ چیک کریں۔',
        ],
        'rice' => [
            'season_en' => 'Needs careful water management',
            'season_ur' => 'پانی کا محتاط انتظام ضروری',
            'tips_en' => [
                'Maintain proper water depth by growth stage.',
                'Keep nursery and field clean of weeds early.',
                'Plan harvest when grain moisture is suitable for milling.',
            ],
            'tips_ur' => [
                'نشوونما کے مرحلے کے مطابق پانی کی گہرائی رکھیں۔',
                'نرسری اور فیلڈ سے جڑی بوٹیاں جلد صاف رکھیں۔',
                'فصل اس وقت کاٹیں جب دانے milling کے لیے موزوں ہوں۔',
            ],
            'watch_en' => 'Blast / bacterial leaf blight more likely in humid spells.',
            'watch_ur' => 'نمی میں blast / پتوں کی بیماری کا امکان بڑھ سکتا ہے۔',
        ],
        'corn' => [
            'season_en' => 'Warm-season crop — heat and moisture matter',
            'season_ur' => 'گرم موسم کی فصل — گرمی اور نمی اہم',
            'tips_en' => [
                'Critical water need around tasseling / silking.',
                'Watch stem borers and leaf damage early.',
                'Store dry cobs/grain to reduce mold.',
            ],
            'tips_ur' => [
                'پھول/بال نکلتے وقت پانی خاص طور پر ضروری ہو سکتا ہے۔',
                'تنے کے کیڑے اور پتوں کا نقصان جلد دیکھیں۔',
                'خشک بھٹے/اناج رکھیں تاکہ پھپھوند نہ لگے۔',
            ],
            'watch_en' => 'Heat stress can reduce kernel set.',
            'watch_ur' => 'شدید گرمی دانوں کی بھرپورت کو کم کر سکتی ہے۔',
        ],
        'tomato' => [
            'season_en' => 'Needs support, airflow, and steady watering',
            'season_ur' => 'سہارا، ہوا کا گزر اور باقاعدہ پانی درکار',
            'tips_en' => [
                'Water at base; keep leaves dry when possible.',
                'Yellow older leaves can be water or nutrient stress — check before adding fertilizer.',
                'Stake plants for better airflow and fruit quality.',
            ],
            'tips_ur' => [
                'جڑوں کے پاس پانی دیں؛ ممکن ہو تو پتے خشک رکھیں۔',
                'پرانی پتیاں زرد ہوں تو پہلے پانی/غذائیت چیک کریں، فوراً کھاد نہ ڈالیں۔',
                'پودوں کو سہارا دیں تاکہ ہوا اور پھل بہتر رہیں۔',
            ],
            'watch_en' => 'Leaf spots / wilt — remove badly affected leaves with care.',
            'watch_ur' => 'داغ / مرجھانا — خراب پتے احتیاط سے ہٹائیں۔',
        ],
        'onion' => [
            'season_en' => 'Sensitive to wet soil and tip burn',
            'season_ur' => 'گیلی مٹی اور tip burn کے لیے حساس',
            'tips_en' => [
                'Avoid waterlogging — bulbs suffer in wet beds.',
                'Dark/black tips: check pests, tip burn, or fungal leaf issues.',
                'Cure bulbs in airy shade before long storage.',
            ],
            'tips_ur' => [
                'پانی کھڑا نہ ہونے دیں — گیلی قطاروں میں بلب خراب ہو سکتے ہیں۔',
                'کالے tips: کیڑے، tip burn یا پتوں کی بیماری چیک کریں۔',
                'لمبی سٹوریج سے پہلے سایہ دار ہوا دار جگہ پر cure کریں۔',
            ],
            'watch_en' => 'Purple blotch / tip drying after humid weather.',
            'watch_ur' => 'نمی کے بعد purple blotch / tips کا سوکھنا دیکھیں۔',
        ],
        'carrot' => [
            'season_en' => 'Loose soil helps straight roots',
            'season_ur' => 'نرم مٹی سیدھی جڑوں میں مدد کرتی ہے',
            'tips_en' => [
                'Avoid hard/cloddy soil for better root shape.',
                'Keep even moisture — irregular water can split roots.',
                'Thin seedlings for proper spacing.',
            ],
            'tips_ur' => [
                'سخت مٹی سے بچیں تاکہ جڑیں سیدھی بنیں۔',
                'باقاعدہ نمی رکھیں — بے ترتیب پانی سے جڑیں پھٹ سکتی ہیں۔',
                'پودوں کے درمیان فاصلہ ٹھیک رکھنے کے لیے thin کریں۔',
            ],
            'watch_en' => 'Leaf blight in wet spells.',
            'watch_ur' => 'گیلے موسم میں پتوں کی بیماری دیکھیں۔',
        ],
        'cucumber' => [
            'season_en' => 'Needs regular water and pest watch',
            'season_ur' => 'باقاعدہ پانی اور کیڑوں پر نظر',
            'tips_en' => [
                'Mulch helps keep moisture steady.',
                'Check underside of leaves for pests.',
                'Harvest often — overripe fruit slows new set.',
            ],
            'tips_ur' => [
                'ملچ نمی برابر رکھنے میں مدد دیتا ہے۔',
                'پتوں کے نیچے کیڑے چیک کریں۔',
                'بار بار توڑیں — زیادہ پکے پھل نئی پیداوار کم کر سکتے ہیں۔',
            ],
            'watch_en' => 'Downy mildew risk in humid weather.',
            'watch_ur' => 'نمی میں downy mildew کا خطرہ۔',
        ],
        'chili' => [
            'season_en' => 'Likes warmth; avoid overwatering',
            'season_ur' => 'گرمی پسند؛ زیادہ پانی سے بچیں',
            'tips_en' => [
                'Flower drop can follow heat or water stress.',
                'Support heavy fruiting plants if needed.',
                'Harvest regularly for continuous flowering.',
            ],
            'tips_ur' => [
                'پھول جھڑنا گرمی یا پانی کے دباؤ سے ہو سکتا ہے۔',
                'بھاری پیداوار والے پودوں کو سہارا دیں۔',
                'باقاعدہ توڑیں تاکہ نئے پھول آتے رہیں۔',
            ],
            'watch_en' => 'Leaf curl / mites — check early.',
            'watch_ur' => 'پتوں کا مڑنا / mites — جلد چیک کریں۔',
        ],
        'spinach' => [
            'season_en' => 'Cool-season leafy crop — harvest young',
            'season_ur' => 'ٹھنڈے موسم کی پتی دار فصل — جوان کاٹیں',
            'tips_en' => [
                'Keep soil evenly moist for tender leaves.',
                'Bolt faster in heat — harvest on time.',
                'Successive sowings give continuous supply.',
            ],
            'tips_ur' => [
                'نرم پتوں کے لیے مٹی کی نمی برابر رکھیں۔',
                'گرمی میں جلدی پھول آ سکتا ہے — وقت پر کاٹیں۔',
                'وقفے وقفے سے بوائی سے مسلسل سپلائی مل سکتی ہے۔',
            ],
            'watch_en' => 'Leaf spots after rain.',
            'watch_ur' => 'بارش کے بعد پتوں کے داغ۔',
        ],
        'eggplant' => [
            'season_en' => 'Warm crop — fruit regularly for more yield',
            'season_ur' => 'گرم موسم کی فصل — باقاعدہ توڑیں',
            'tips_en' => [
                'Do not let fruit over-mature on plant.',
                'Watch wilt and shoot borers.',
                'Steady watering improves fruit quality.',
            ],
            'tips_ur' => [
                'پھل پودے پر زیادہ پکا نہ چھوڑیں۔',
                'مرجھانا اور شوٹ بورر دیکھیں۔',
                'باقاعدہ پانی سے پھل کا معیار بہتر ہو سکتا ہے۔',
            ],
            'watch_en' => 'Fruit and shoot borer damage.',
            'watch_ur' => 'پھل اور شوٹ بورر کا نقصان۔',
        ],
        'broccoli' => [
            'season_en' => 'Prefers cool weather for tight heads',
            'season_ur' => 'ٹھنڈے موسم میں بہتر ہیڈ بنتا ہے',
            'tips_en' => [
                'Harvest when heads are tight and green.',
                'Heat can cause loose/poor heads.',
                'Keep nitrogen balanced — excess soft growth invites pests.',
            ],
            'tips_ur' => [
                'ہیڈ سخت اور سبز ہو تو کاٹیں۔',
                'گرمی میں ہیڈ ڈھیلا/خراب ہو سکتا ہے۔',
                'نائٹروجن متوازن رکھیں — زیادہ نرم نشوونما کیڑوں کو بلا سکتی ہے۔',
            ],
            'watch_en' => 'Caterpillars on leaves/heads.',
            'watch_ur' => 'پتوں/ہیڈ پر caterpillars۔',
        ],
        'mango' => [
            'season_en' => 'Tree crop — prune for light and airflow',
            'season_ur' => 'درخت کی فصل — روشنی اور ہوا کے لیے کٹائی',
            'tips_en' => [
                'Remove crowded branches for better fruiting.',
                'Watch flower/fruit drop after sudden weather change.',
                'Keep orchard floor clean.',
            ],
            'tips_ur' => [
                'گھنی شاخیں ہٹا کر پھل بہتر بنائیں۔',
                'موسم کی اچانک تبدیلی پر پھول/پھل جھڑنا دیکھیں۔',
                'باغیچہ صاف رکھیں۔',
            ],
            'watch_en' => 'Anthracnose / powdery mildew in humid spells.',
            'watch_ur' => 'نمی میں anthracnose / پھپھوندی دیکھیں۔',
        ],
        'apple' => [
            'season_en' => 'Needs chill hours and careful pruning',
            'season_ur' => 'ٹھنڈ کے گھنٹے اور محتاط کٹائی درکار',
            'tips_en' => [
                'Prune for open canopy and sunlight.',
                'Thin fruit for better size/quality.',
                'Store bruised fruit separately.',
            ],
            'tips_ur' => [
                'کھلی چھتری اور دھوپ کے لیے کٹائی کریں۔',
                'بہتر سائز کے لیے پھل thin کریں۔',
                'زخمی پھل الگ رکھیں۔',
            ],
            'watch_en' => 'Scab / fruit spots in wet seasons.',
            'watch_ur' => 'گیلے موسم میں scab / پھل کے داغ۔',
        ],
        'kinnow' => [
            'season_en' => 'Citrus needs drainage and zinc/micronutrient care',
            'season_ur' => 'کھٹے پھل کو نکاس اور micronutrients کی دیکھ بھال',
            'tips_en' => [
                'Avoid waterlogged roots.',
                'Yellowing may relate to nutrients or root stress — check before heavy fertilizer.',
                'Keep orchard hygiene to reduce fruit fly pressure.',
            ],
            'tips_ur' => [
                'جڑوں کے پاس پانی نہ کھڑا ہونے دیں۔',
                'زرد پن غذائیت یا جڑوں کے دباؤ سے ہو سکتا ہے — بھاری کھاد سے پہلے چیک کریں۔',
                'فروٹ فلائی کم کرنے کے لیے باغیچہ صاف رکھیں۔',
            ],
            'watch_en' => 'Citrus canker / fruit fly risk.',
            'watch_ur' => 'کینکر / فروٹ فلائی کا خطرہ۔',
        ],
        'watermelon' => [
            'season_en' => 'Needs space, sun, and careful watering near ripening',
            'season_ur' => 'جگہ، دھوپ، اور پکنے کے قریب محتاط پانی',
            'tips_en' => [
                'Reduce excess water near harvest to improve sweetness.',
                'Use mulch / dry bed under fruit if possible.',
                'Rotate fields to reduce soil disease build-up.',
            ],
            'tips_ur' => [
                'کٹائی کے قریب زیادہ پانی کم کریں تاکہ مٹھاس بہتر ہو۔',
                'ممکن ہو تو پھل کے نیچے خشک بستر/ملچ رکھیں۔',
                'مٹی کی بیماری کم کرنے کے لیے فصل تبدیل کریں۔',
            ],
            'watch_en' => 'Wilt / leaf spots in humid warm weather.',
            'watch_ur' => 'گرم نمی میں مرجھانا / پتوں کے داغ۔',
        ],
        'grapes' => [
            'season_en' => 'Needs training, pruning, and air movement',
            'season_ur' => 'ٹریننگ، کٹائی اور ہوا کا گزر ضروری',
            'tips_en' => [
                'Prune for open canopy to reduce berry disease.',
                'Avoid long leaf wetness.',
                'Support vines properly before fruit load.',
            ],
            'tips_ur' => [
                'کھلی چھتری کے لیے کٹائی کریں تاکہ بیماری کم ہو۔',
                'پتوں کو زیادہ دیر گیلا نہ رکھیں۔',
                'پھل سے پہلے بیل کو مضبوط سہارا دیں۔',
            ],
            'watch_en' => 'Downy / powdery mildew in humid seasons.',
            'watch_ur' => 'نمی والے موسم میں mildew دیکھیں۔',
        ],
        'other' => [
            'season_en' => 'General farm tips for any crop',
            'season_ur' => 'کسی بھی فصل کے عمومی مشورے',
            'tips_en' => [
                'Record costs and harvest so next season estimates improve.',
                'Check soil moisture before watering.',
                'Ask local agriculture office before chemical use.',
            ],
            'tips_ur' => [
                'خرچ اور پیداوار لکھ کر رکھیں تاکہ اگلے موسم کا حساب بہتر ہو۔',
                'پانی دینے سے پہلے مٹی کی نمی چیک کریں۔',
                'کیمیکل سے پہلے مقامی زراعت دفتر سے مشورہ لیں۔',
            ],
            'watch_en' => 'Sudden weather change can stress any crop.',
            'watch_ur' => 'اچانک موسم کی تبدیلی کسی بھی فصل پر دباؤ ڈال سکتی ہے۔',
        ],
    ],
];
