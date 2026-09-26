<?php

namespace Database\Seeders;

use App\Models\SmartCrop;
use Illuminate\Database\Seeder;

class SmartCropSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->crops() as $i => $crop) {
            SmartCrop::query()->updateOrCreate(
                ['slug' => $crop['slug']],
                array_merge($crop, [
                    'sort_order' => $i + 1,
                    'is_active' => true,
                    'ideal_humidity_min' => $crop['ideal_humidity_min'] ?? 40,
                    'ideal_humidity_max' => $crop['ideal_humidity_max'] ?? 75,
                ])
            );
        }
    }

    private function crops(): array
    {
        $rows = [];

        $rows[] = $this->make('tomato', 'Tomato', 'ٹماٹر', '🍅', 'vegetable', 15, 30, true,
            $this->pack(
                '25–40 mm/week (more in heat)', 'Every 2–3 days; keep soil evenly moist', 'Early morning',
                'Avoid waterlogging — root rot and fruit crack',
                'Loamy, well-drained, rich organic matter', 'pH 6.0–6.8; compost before transplant',
                '18–27°C ideal', 'Above 35°C flower drop; below 10°C slow growth',
                '70–90 days from transplant', 'First pick ~75 days; harvest every 3–5 days',
                'Stake plants, prune suckers, mulch beds',
                'Aphids, whitefly, fruit borer', 'Early/late blight, wilt, leaf curl',
                'Rotate crops, remove infected leaves, avoid wet foliage overnight'
            ),
            $this->pack(
                'ہفتہ وار 25–40 ملی میٹر (گرمی میں زیادہ)', 'ہر 2–3 دن؛ مٹی یکساں نم رکھیں', 'صبح جلدی',
                'کھڑا پانی نہ رہنے دیں — جڑ سڑن اور پھل پھٹ سکتے ہیں',
                'چکنائی والی، اچھی نکاسی والی مٹی', 'pH 6.0–6.8؛ شفٹنگ سے پہلے کمپوسٹ',
                'دن میں 18–27°C بہترین', '35°C سے اوپر پھول جھڑیں؛ 10°C سے کم نشوونما سست',
                'شفٹنگ کے بعد 70–90 دن', 'پہلی کٹائی ~75 دن؛ ہر 3–5 دن توڑیں',
                'سہارا دیں، اضافی شاخیں کاٹیں، ملچ لگائیں',
                'افڈ، سفید مکھی، فروٹ بورر', 'ارلی/لیٹ بلائٹ، ولٹ، لیف کرل',
                'فصل گردش، بیمار پتے ہٹائیں، رات کو پتے گیلا نہ رکھیں'
            )
        );

        $rows[] = $this->make('potato', 'Potato', 'آلو', '🥔', 'vegetable', 10, 25, true,
            $this->pack(
                '20–30 mm/week during tuber growth', 'Light, frequent irrigation; never flood', 'Morning',
                'Reduce water before harvest to firm skins',
                'Loose sandy-loam, stone-free', 'pH 5.5–6.5; well-drained ridges',
                '15–20°C soil ideal', 'Heat above 30°C reduces tuber quality',
                '90–120 days', 'When foliage yellows; dig carefully',
                'Earth up ridges; use healthy seed tubers',
                'Cutworm, aphids, potato beetle', 'Late blight, scab, soft rot',
                'Certified seed, wide spacing, remove volunteers'
            ),
            $this->pack(
                'ٹیوبر بنتے وقت ہفتہ وار 20–30 ملی میٹر', 'ہلکی باقاعدہ آبپاشی؛ سیلاب نہ کریں', 'صبح',
                'کٹائی سے پہلے بھاری پانی بند کریں',
                'ہلکی ریتیلی لوم، پتھر سے پاک', 'pH 5.5–6.5؛ اچھی نکاسی والی مینڈیں',
                'مٹی 15–20°C بہترین', '30°C سے زیادہ گرمی معیار گھٹاتی ہے',
                '90–120 دن', 'جب پتے زرد ہوں؛ احتیاط سے کھودیں',
                'مٹی چڑھائیں؛ صحت مند بیج استعمال کریں',
                'کٹ ورم، افڈ، پوٹیٹو بیٹل', 'لیٹ بلائٹ، سکاب، سافٹ روٹ',
                'سرٹیفائیڈ بیج، فاصلہ رکھیں، جنگلی پودے ہٹائیں'
            )
        );

        $rows[] = $this->make('onion', 'Onion', 'پیاز', '🧅', 'vegetable', 12, 28, true,
            $this->pack(
                '15–25 mm weekly', 'Light irrigation every 4–7 days', 'Morning',
                'Reduce water near maturity to cure bulbs',
                'Fertile sandy-loam', 'pH 6.0–7.0; avoid fresh manure late',
                '13–24°C', 'Extreme heat slows bulbing',
                '100–150 days', 'When tops fall; cure in shade',
                'Keep weed-free; avoid drought swings',
                'Thrips, onion fly', 'Downy mildew, purple blotch',
                'Good drainage; rotate alliums'
            ),
            $this->pack(
                'ہفتہ وار 15–25 ملی میٹر', 'ہر 4–7 دن ہلکی آبپاشی', 'صبح',
                'پختگی پر پانی کم کریں',
                'زرخیز ریتیلی لوم', 'pH 6.0–7.0؛ آخر میں تازہ گوبر نہ ڈالیں',
                '13–24°C', 'شدید گرمی گوندے بننا سست کرتی ہے',
                '100–150 دن', 'جب پتے جھکیں؛ سایہ میں خشک کریں',
                'جڑی بوٹی صاف رکھیں؛ خشکی-نم کا اتار چڑھاؤ نقصان دہ',
                'تھrips، اونین فلائی', 'ڈاونی ملڈیو، پرپل بلاچ',
                'اچھی نکاسی؛ الائیم گردش'
            )
        );

        $rows[] = $this->make('cucumber', 'Cucumber', 'کھیرا', '🥒', 'vegetable', 18, 32, true,
            $this->pack(
                '30–40 mm/week; consistent moisture', 'Every 1–2 days in heat', 'Early morning',
                'Irregular water causes bitter fruit',
                'Rich loam with good drainage', 'pH 6.0–7.0; trellis for airflow',
                '20–30°C', 'Cold nights below 15°C slow vines',
                '50–70 days', 'Pick every 1–2 days when firm and green',
                'Mulch, train vines, remove yellow leaves',
                'Aphids, cucumber beetle', 'Downy mildew, powdery mildew',
                'Avoid overhead watering late day; rotate cucurbits'
            ),
            $this->pack(
                'ہفتہ وار 30–40 ملی میٹر؛ مسلسل نمی', 'گرمی میں ہر 1–2 دن', 'صبح جلدی',
                'غیر باقاعدہ پانی سے پھل کڑوا ہو سکتا ہے',
                'زرخیز لوم، اچھی نکاسی', 'pH 6.0–7.0؛ ٹریلس سے ہوا بہتر',
                '20–30°C', '15°C سے کم راتیں بیل سست کرتی ہیں',
                '50–70 دن', 'ہر 1–2 دن توڑیں جب سخت اور سبز ہو',
                'ملچ، بیل سیدھی کریں، زرد پتے ہٹائیں',
                'افڈ، ککمبر بیٹل', 'ڈاونی/پاؤڈری ملڈیو',
                'شام کو اوپر سے پانی نہ دیں؛ ککر بٹ گردش'
            )
        );

        $rows[] = $this->make('carrot', 'Carrot', 'گاجر', '🥕', 'vegetable', 10, 26, true,
            $this->pack(
                '20–25 mm/week', 'Light frequent water until established', 'Morning',
                'Heavy water after long dry spell splits roots',
                'Deep sandy-loam, no hardpan', 'pH 6.0–6.8; fine seedbed',
                '15–22°C', 'Heat makes roots coarse and pale',
                '70–100 days', 'When shoulders reach size; loosen soil gently',
                'Thin seedlings; keep soil moist evenly',
                'Carrot fly, aphids', 'Leaf blight, soft rot',
                'Fine tilth, crop rotation, avoid fresh manure'
            ),
            $this->pack(
                'ہفتہ وار 20–25 ملی میٹر', 'ابتدا میں ہلکی باقاعدہ آبپاشی', 'صبح',
                'طویل خشکی کے بعد بھاری پانی سے جڑ پھٹ سکتی ہے',
                'گہری ریتیلی لوم، سخت تہہ نہیں', 'pH 6.0–6.8؛ باریک بیڈ',
                '15–22°C', 'گرمی جڑیں سخت اور پھیکی کرتی ہے',
                '70–100 دن', 'کندھے درست سائز پر؛ مٹی نرم کر کے نکالیں',
                'پودے پتلے کریں؛ نمی یکساں رکھیں',
                'گاجر فلائی، افڈ', 'لیف بلائٹ، سافٹ روٹ',
                'باریک مٹی، گردش، تازہ گوبر سے گریز'
            )
        );

        $rows[] = $this->make('spinach', 'Spinach', 'پالک', '🥬', 'vegetable', 5, 22, true,
            $this->pack(
                '20–30 mm/week', 'Keep soil moist; never bone dry', 'Morning',
                'Water stress causes early bolting',
                'Fertile moist loam', 'pH 6.5–7.5; high nitrogen',
                '10–20°C', 'Heat above 25°C triggers bolting',
                '35–50 days', 'Cut outer leaves or whole plant young',
                'Succession sow every 2 weeks in cool season',
                'Leaf miners, aphids', 'Downy mildew, leaf spot',
                'Good airflow; avoid wet leaves overnight'
            ),
            $this->pack(
                'ہفتہ وار 20–30 ملی میٹر', 'مٹی نم رکھیں؛ بالکل خشک نہ ہونے دیں', 'صبح',
                'پانی کی کمی سے جلدی پھول آجاتے ہیں',
                'زرخیز نم لوم', 'pH 6.5–7.5؛ نائٹروجن زیادہ',
                '10–20°C', '25°C سے اوپر بولٹنگ',
                '35–50 دن', 'باہر والے پتے کاٹیں یا پورا پودا جوان توڑیں',
                'ٹھنڈے موسم میں ہر 2 ہفتے نئی بوائی',
                'لیف مائنر، افڈ', 'ڈاونی ملڈیو، لیف اسپاٹ',
                'ہوا کا بہاؤ؛ رات کو پتے گیلا نہ رکھیں'
            )
        );

        $rows[] = $this->make('chilli', 'Chilli', 'مرچ', '🌶️', 'vegetable', 18, 32, true,
            $this->pack(
                '25–35 mm/week', 'Every 2–3 days; deeper less often later', 'Morning',
                'Overwatering drops flowers and invites wilt',
                'Well-drained loam', 'pH 6.0–7.0; sunny site',
                '20–30°C', 'Cold below 15°C stalls growth',
                '80–120 days', 'Pick when firm; color per variety',
                'Stake tall types; harvest often to keep fruiting',
                'Thrips, mites, fruit borer', 'Anthracnose, bacterial wilt',
                'Mulch, rotate nightshades, remove fallen fruit'
            ),
            $this->pack(
                'ہفتہ وار 25–35 ملی میٹر', 'ہر 2–3 دن؛ بعد میں گہری کم بار', 'صبح',
                'زیادہ پانی سے پھول جھڑیں اور ولٹ آئے',
                'اچھی نکاسی والی لوم', 'pH 6.0–7.0؛ دھوپ والی جگہ',
                '20–30°C', '15°C سے کم سردی نشوونما روکتی ہے',
                '80–120 دن', 'جب سخت ہو توڑیں؛ رنگ قسم کے مطابق',
                'لمبی اقسام سہارا دیں؛ باقاعدہ توڑیں',
                'تھrips، مائٹ، فروٹ بورر', 'اینتھراکنوز، بیکٹیریل ولٹ',
                'ملچ، نائٹ شیڈ گردش، گری پھل ہٹائیں'
            )
        );

        $rows[] = $this->make('brinjal', 'Brinjal', 'بینگن', '🍆', 'vegetable', 18, 32, true,
            $this->pack(
                '25–35 mm/week', 'Regular deep watering', 'Morning',
                'Drought causes bitter fruit and flower drop',
                'Deep fertile loam', 'pH 5.5–7.0; compost rich',
                '22–30°C', 'Cool nights below 15°C reduce set',
                '90–120 days', 'Harvest glossy fruits before seeds harden',
                'Stake plants; pick regularly',
                'Fruit & shoot borer, whitefly', 'Phomopsis, wilt',
                'Remove bored shoots; avoid water stress'
            ),
            $this->pack(
                'ہفتہ وار 25–35 ملی میٹر', 'باقاعدہ گہری آبپاشی', 'صبح',
                'خشکی سے پھل کڑوا اور پھول جھڑیں',
                'گہری زرخیز لوم', 'pH 5.5–7.0؛ کمپوسٹ سے بھرپور',
                '22–30°C', '15°C سے کم راتیں پھل کم کرتی ہیں',
                '90–120 دن', 'چمکیلا پھل بیج سخت ہونے سے پہلے توڑیں',
                'سہارا دیں؛ باقاعدہ توڑیں',
                'فروٹ اینڈ شوٹ بورر، سفید مکھی', 'فوموپسس، ولٹ',
                'سوراخ شدہ شاخیں کاٹیں؛ پانی کا دباؤ نہ آنے دیں'
            )
        );

        $rows[] = $this->make('cauliflower', 'Cauliflower', 'پھول گوبھی', '🥦', 'vegetable', 10, 24, true,
            $this->pack(
                '25–35 mm/week', 'Even moisture; never dry out', 'Morning',
                'Water stress causes buttoning / poor curds',
                'Fertile moist loam', 'pH 6.5–7.5; boron important',
                '15–20°C', 'Heat above 25°C spoils curd quality',
                '60–100 days', 'When curd is compact and white',
                'Tie leaves over curd for blanching if needed',
                'Aphids, diamondback moth', 'Clubroot, black rot',
                'Cool season planting; rotate brassicas'
            ),
            $this->pack(
                'ہفتہ وار 25–35 ملی میٹر', 'یکساں نمی؛ خشک نہ ہونے دیں', 'صبح',
                'پانی کی کمی سے چھوٹے / خراب گوبھے',
                'زرخیز نم لوم', 'pH 6.5–7.5؛ بوران اہم',
                '15–20°C', '25°C سے اوپر معیار خراب',
                '60–100 دن', 'جب گوبھی سخت اور سفید ہو',
                'ضرورت ہو تو پتے باندھ کر سفید رکھیں',
                'افڈ، ڈائمنڈ بیک موتھ', 'کلبر روٹ، بلیک روٹ',
                'ٹھنڈے موسم میں لگائیں؛ براسیکا گردش'
            )
        );

        $rows[] = $this->make('cabbage', 'Cabbage', 'بند گوبھی', '🥬', 'vegetable', 8, 24, true,
            $this->pack(
                '25–35 mm/week', 'Steady irrigation through head fill', 'Morning',
                'Sudden heavy water after drought splits heads',
                'Fertile well-drained loam', 'pH 6.5–7.5',
                '15–20°C', 'Prolonged heat loosens heads',
                '70–100 days', 'When heads are firm and heavy',
                'Space well; control weeds early',
                'Cabbage worm, aphids', 'Black rot, clubroot',
                'Rotate brassicas; remove crop debris'
            ),
            $this->pack(
                'ہفتہ وار 25–35 ملی میٹر', 'گوبھی بھرتے وقت مستقل آبپاشی', 'صبح',
                'خشکی کے بعد اچانک بھاری پانی سے گوبھی پھٹ سکتی ہے',
                'زرخیز اچھی نکاسی والی لوم', 'pH 6.5–7.5',
                '15–20°C', 'طویل گرمی گوبھی ڈھیلی کرتی ہے',
                '70–100 دن', 'جب سخت اور بھاری ہو',
                'فاصلہ رکھیں؛ جڑی بوٹی جلد کنٹرول',
                'کیبج ورم، افڈ', 'بلیک روٹ، کلبر روٹ',
                'براسیکا گردش؛ باقیات صاف کریں'
            )
        );

        $rows[] = $this->make('okra', 'Okra', 'بھنڈی', '🟢', 'vegetable', 20, 35, false,
            $this->pack(
                '20–30 mm/week', 'Deep water every 3–5 days', 'Morning',
                'Do not keep soil soggy — pods drop',
                'Warm well-drained loam', 'pH 6.0–7.0; full sun',
                '24–32°C', 'Cold below 18°C stalls growth',
                '50–65 days to first pods', 'Pick every 1–2 days when tender',
                'Harvest often to keep plants productive',
                'Aphids, fruit borers', 'Powdery mildew, wilt',
                'Sunny site; avoid cool wet soils'
            ),
            $this->pack(
                'ہفتہ وار 20–30 ملی میٹر', 'ہر 3–5 دن گہری آبپاشی', 'صبح',
                'مٹی گیلی نہ رکھیں — پھلیاں جھڑ سکتی ہیں',
                'گرم اچھی نکاسی والی لوم', 'pH 6.0–7.0؛ پوری دھوپ',
                '24–32°C', '18°C سے کم سردی روکتی ہے',
                'پہلی پھلی 50–65 دن', 'ہر 1–2 دن نرم پھلی توڑیں',
                'باقاعدہ توڑیں تاکہ پیداوار جاری رہے',
                'افڈ، فروٹ بورر', 'پاؤڈری ملڈیو، ولٹ',
                'دھوپ والی جگہ؛ ٹھنڈی گیلی مٹی سے گریز'
            )
        );

        $rows[] = $this->make('peas', 'Peas', 'مٹر', '🫛', 'vegetable', 8, 22, true,
            $this->pack(
                '20–25 mm/week', 'Even moisture at flowering/pod set', 'Morning',
                'Overwatering invites root rot',
                'Cool well-drained loam', 'pH 6.0–7.5; light nitrogen',
                '10–20°C', 'Heat above 25°C reduces pods',
                '60–80 days', 'Pick pods when full but still tender',
                'Provide support for tall types',
                'Aphids, thrips', 'Powdery mildew, root rot',
                'Cool season crop; good airflow on trellis'
            ),
            $this->pack(
                'ہفتہ وار 20–25 ملی میٹر', 'پھول/پھلی پر یکساں نمی', 'صبح',
                'زیادہ پانی سے جڑ سڑن',
                'ٹھنڈی اچھی نکاسی والی لوم', 'pH 6.0–7.5؛ نائٹروجن ہلکی',
                '10–20°C', '25°C سے اوپر پھلیاں کم',
                '60–80 دن', 'پھلی بھری مگر نرم ہو تو توڑیں',
                'لمبی اقسام کو سہارا دیں',
                'افڈ، تھrips', 'پاؤڈری ملڈیو، جڑ سڑن',
                'ٹھنڈے موسم کی فصل؛ ٹریلس پر ہوا'
            )
        );

        $rows[] = $this->make('corn', 'Corn', 'مکئی', '🌽', 'vegetable', 15, 34, false,
            $this->pack(
                '30–40 mm/week; critical at tasseling', 'Deep irrigation 1–2× weekly', 'Morning',
                'Water stress at silk stage cuts kernels',
                'Deep fertile loam', 'pH 5.8–7.0; high nitrogen',
                '18–30°C', 'Frost kills young plants',
                '80–110 days', 'When kernels milky; silks brown',
                'Plant in blocks for pollination',
                'Stem borer, armyworm', 'Leaf blight, smut',
                'Rotate cereals; keep weeds down early'
            ),
            $this->pack(
                'ہفتہ وار 30–40 ملی میٹر؛ ٹسلنگ پر اہم', 'ہفتہ میں 1–2 بار گہری آبپاشی', 'صبح',
                'ریشوں کے وقت پانی کی کمی دانے کم کرتی ہے',
                'گہری زرخیز لوم', 'pH 5.8–7.0؛ نائٹروجن زیادہ',
                '18–30°C', 'پالا نوجوان پودے مار دیتا ہے',
                '80–110 دن', 'جب دانے دودھیا ہوں؛ ریشے بھورے',
                'بلاک میں لگائیں تاکہ پولینیشن بہتر ہو',
                'سٹیم بورر، آرمی ورم', 'لیف بلائٹ، سمٹ',
                'اناج گردش؛ ابتدا میں جڑی بوٹی کنٹرول'
            )
        );

        $rows[] = $this->make('watermelon', 'Watermelon', 'تربوز', '🍉', 'fruit', 20, 35, false,
            $this->pack(
                '25–35 mm/week; ease off near ripening', 'Deep water; reduce late for sweetness', 'Morning',
                'Heavy late water splits fruit and dilutes sugar',
                'Sandy-loam, warm, well-drained', 'pH 6.0–7.0; full sun',
                '22–32°C', 'Cold soils delay vines',
                '80–100 days', 'Tendril dry, belly yellow, hollow sound',
                'Mulch under fruit; turn gently',
                'Aphids, fruit fly', 'Fusarium wilt, anthracnose',
                'Rotate cucurbits; avoid wet foliage'
            ),
            $this->pack(
                'ہفتہ وار 25–35 ملی میٹر؛ پکنے پر کم', 'گہری آبپاشی؛ آخر میں مٹھاس کے لیے کم', 'صبح',
                'آخر میں بھاری پانی پھل پھاڑتا اور میٹھا کم کرتا ہے',
                'ریتیلی لوم، گرم، اچھی نکاسی', 'pH 6.0–7.0؛ پوری دھوپ',
                '22–32°C', 'ٹھنڈی مٹی بیل سست کرتی ہے',
                '80–100 دن', 'ٹینڈرل خشک، پیٹ پیلا، کھوکھلی آواز',
                'پھل کے نیچے ملچ؛ ہلکے سے گھمائیں',
                'افڈ، فروٹ فلائی', 'فیوزیریم ولٹ، اینتھراکنوز',
                'ککر بٹ گردش؛ پتے گیلا نہ رکھیں'
            )
        );

        $rows[] = $this->make('melon', 'Melon', 'خربوزہ', '🍈', 'fruit', 18, 34, false,
            $this->pack(
                '25–35 mm/week; reduce near ripening', 'Steady then taper water', 'Morning',
                'Late overwatering hurts flavor and storage',
                'Warm sandy-loam', 'pH 6.0–7.0',
                '20–30°C', 'Cool wet weather invites mildew',
                '75–95 days', 'When aroma strong and stem slips easily',
                'Elevate fruit on mulch; good sun',
                'Aphids, mites', 'Powdery mildew, gummy stem',
                'Airflow, drip irrigation preferred'
            ),
            $this->pack(
                'ہفتہ وار 25–35 ملی میٹر؛ پکنے پر کم', 'پہلے مستقل پھر پانی گھٹائیں', 'صبح',
                'آخر میں زیادہ پانی ذائقہ اور ذخیرہ خراب کرتا ہے',
                'گرم ریتیلی لوم', 'pH 6.0–7.0',
                '20–30°C', 'ٹھنڈا گیلا موسم ملڈیو لاتا ہے',
                '75–95 دن', 'جب خوشبو اور تنے کا سلپ آسان ہو',
                'پھل ملچ پر رکھیں؛ دھوپ دیں',
                'افڈ، مائٹ', 'پاؤڈری ملڈیو، گمی سٹیم',
                'ہوا کا بہاؤ؛ ڈرپ آبپاشی بہتر'
            )
        );

        $rows[] = $this->make('mango', 'Mango', 'آم', '🥭', 'fruit', 15, 40, false,
            $this->pack(
                'Deep irrigation every 10–15 days in dry season', 'Young trees more often; mature trees deep & rare', 'Morning',
                'Avoid watering during flowering if soil is moist',
                'Deep well-drained loam', 'pH 5.5–7.5; no waterlogging',
                '24–35°C', 'Frost damages flowers and young fruit',
                'Trees fruit in 3–5 years', 'Harvest mature firm fruit; ripen off-tree',
                'Prune for light; mulch basin',
                'Fruit fly, hoppers', 'Anthracnose, powdery mildew',
                'Sanitation, prune crowded shoots'
            ),
            $this->pack(
                'خشک موسم میں ہر 10–15 دن گہری آبپاشی', 'نوجوان درخت زیادہ؛ بڑے درخت گہری کم بار', 'صبح',
                'پھول کے وقت مٹی نم ہو تو پانی نہ دیں',
                'گہری اچھی نکاسی والی لوم', 'pH 5.5–7.5؛ کھڑا پانی نہیں',
                '24–35°C', 'پالا پھول اور نوجوان پھل نقصان دیتا ہے',
                'درخت 3–5 سال میں پھل دیتے ہیں', 'پختہ سخت پھل توڑیں؛ درخت سے اتر کر پکائیں',
                'روشنی کے لیے کٹائی؛ بیسن پر ملچ',
                'فروٹ فلائی، ہوپر', 'اینتھراکنوز، پاؤڈری ملڈیو',
                'صفائی، گھنی شاخیں کاٹیں'
            )
        );

        $rows[] = $this->make('apple', 'Apple', 'سیب', '🍎', 'fruit', 5, 28, true,
            $this->pack(
                '25–40 mm/week in growing season', 'Deep weekly water; mulch roots', 'Morning',
                'Waterlogging causes root death',
                'Deep loam with drainage', 'pH 6.0–7.0; chill hours needed',
                '15–25°C growing; chill in winter', 'Late frost ruins bloom',
                'Trees fruit in 3–6 years', 'Pick when color and firmness match variety',
                'Prune annually; thin fruit',
                'Codling moth, aphids', 'Scab, powdery mildew',
                'Sanitation, resistant varieties where possible'
            ),
            $this->pack(
                'نشوونما میں ہفتہ وار 25–40 ملی میٹر', 'ہفتہ وار گہری آبپاشی؛ جڑوں پر ملچ', 'صبح',
                'کھڑا پانی جڑیں مار دیتا ہے',
                'گہری لوم، اچھی نکاسی', 'pH 6.0–7.0؛ سردی کے گھنٹے درکار',
                'نشوونما 15–25°C؛ سردی میں چل', 'دیر سے پالا پھول برباد کرتا ہے',
                'درخت 3–6 سال میں پھل', 'رنگ اور سختی قسم کے مطابق توڑیں',
                'سالانہ کٹائی؛ پھل پتلے کریں',
                'کوڈلنگ موتھ، افڈ', 'سکاب، پاؤڈری ملڈیو',
                'صفائی؛ ممکن ہو تو مزاحم اقسام'
            )
        );

        $rows[] = $this->make('banana', 'Banana', 'کیلا', '🍌', 'fruit', 20, 35, true,
            $this->pack(
                'High water need — keep moist, never swampy', 'Frequent light-to-moderate irrigation', 'Morning',
                'Standing water causes corm rot',
                'Deep rich moist loam', 'pH 6.0–7.5; wind shelter',
                '25–32°C', 'Cold below 15°C slows growth badly',
                '9–12 months to bunch', 'Harvest green mature; ripen in shade',
                'Remove suckers; prop heavy bunches',
                'Nematodes, weevils', 'Sigatoka, Panama wilt',
                'Clean tools; good drainage; remove old leaves'
            ),
            $this->pack(
                ' زیادہ پانی — نم رکھیں مگر دلدل نہیں', 'بار بار ہلکی تا درمیانی آبپاشی', 'صبح',
                'کھڑا پانی کارم سڑاتا ہے',
                'گہری زرخیز نم لوم', 'pH 6.0–7.5؛ ہوا سے بچاؤ',
                '25–32°C', '15°C سے کم سردی بہت سست کرتی ہے',
                'گچھا 9–12 ماہ', 'سبز پختہ توڑیں؛ سایہ میں پکائیں',
                'اضافی سکر ہٹائیں؛ بھاری گچھے سہارا دیں',
                'نیماٹوڈ، ویول', 'سگاٹوکا، پاناما ولٹ',
                'اوزار صاف؛ نکاسی؛ پرانے پتے ہٹائیں'
            )
        );

        $rows[] = $this->make('orange', 'Orange', 'سنگترہ', '🍊', 'fruit', 10, 35, false,
            $this->pack(
                'Deep irrigation every 7–14 days dry season', 'Young trees more often', 'Morning',
                'Avoid wet feet — root rot risk',
                'Well-drained sandy-loam', 'pH 6.0–7.5',
                '15–30°C', 'Frost damages fruit and flush',
                'Trees fruit in 3–5 years', 'Harvest when color and juice mature',
                'Mulch; prune for light inside canopy',
                'Citrus psylla, fruit fly', 'Canker, greening (where present)',
                'Nutrition balance; remove mummies'
            ),
            $this->pack(
                'خشک موسم میں ہر 7–14 دن گہری آبپاشی', 'نوجوان درخت زیادہ بار', 'صبح',
                'گیلی جڑیں سڑن کا خطرہ',
                'اچھی نکاسی والی ریتیلی لوم', 'pH 6.0–7.5',
                '15–30°C', 'پالا پھل اور نئی شاخیں نقصان دیتا ہے',
                'درخت 3–5 سال میں پھل', 'رنگ اور رس پختہ ہو تو توڑیں',
                'ملچ؛ چھتری اندر روشنی کے لیے کٹائیں',
                'سائٹر س سائلا، فروٹ فلائی', 'کینکر، گریننگ (جہاں ہو)',
                'غذائیت متوازن؛ خشک پھل ہٹائیں'
            )
        );

        $rows[] = $this->make('guava', 'Guava', 'امرود', '🟢', 'fruit', 15, 35, false,
            $this->pack(
                '20–30 mm equivalent weekly in dry spells', 'Deep water; avoid constant wet feet', 'Morning',
                'Waterlogging kills trees quickly',
                'Wide soil range if drained', 'pH 5.5–7.0',
                '20–32°C', 'Prolonged cold slows flowering',
                'Trees fruit in 2–4 years', 'Pick when color turns and aroma rises',
                'Prune after harvest; thin crowded wood',
                'Fruit fly, mealybug', 'Wilt, anthracnose',
                'Bag fruit; orchard sanitation'
            ),
            $this->pack(
                'خشکی میں ہفتہ وار ~20–30 ملی میٹر', 'گہری آبپاشی؛ مسلسل گیلی جڑیں نہیں', 'صبح',
                'کھڑا پانی درخت جلد مار دیتا ہے',
                'نکاسی ہو تو کئی مٹیاں چلتی ہیں', 'pH 5.5–7.0',
                '20–32°C', 'طویل سردی پھول کم کرتی ہے',
                'درخت 2–4 سال میں پھل', 'رنگ بدلے اور خوشبو آئے تو توڑیں',
                'کٹائی کے بعد پرون؛ گھنی لکڑی پتلی کریں',
                'فروٹ فلائی، میلی بگ', 'ولٹ، اینتھراکنوز',
                'پھل تھیلے میں؛ باغ صاف رکھیں'
            )
        );

        $rows[] = $this->make('grapes', 'Grapes', 'انگور', '🍇', 'fruit', 10, 35, true,
            $this->pack(
                'Moderate; critical from berry set to veraison', 'Deep less often; keep canopy dry', 'Morning',
                'Wet canopy invites mildew — prefer drip',
                'Well-drained loam or sandy-loam', 'pH 6.0–7.0; full sun',
                '15–30°C', 'Late rain near harvest splits berries',
                'Vines fruit year 2–3', 'Harvest when sugar/acid balance is right',
                'Train and prune yearly; leaf pull for light',
                'Mealybug, thrips', 'Downy/powdery mildew, anthracnose',
                'Airflow, drip irrigation, spray timing if needed'
            ),
            $this->pack(
                'درمیانی؛ بیڑی سیٹ سے ویرائزن تک اہم', 'گہری کم بار؛ چھتری خشک رکھیں', 'صبح',
                'گیلی چھتری ملڈیو لاتی ہے — ڈرپ بہتر',
                'اچھی نکاسی والی لوم یا ریتیلی لوم', 'pH 6.0–7.0؛ پوری دھوپ',
                '15–30°C', 'کٹائی کے قریب بارش دانے پھاڑتی ہے',
                'بیل سال 2–3 میں پھل', 'شکر/تیزاب درست ہو تو توڑیں',
                'سالانہ تربیت و کٹائی؛ روشنی کے لیے پتے ہٹائیں',
                'میلی بگ، تھrips', 'ڈاونی/پاؤڈری ملڈیو، اینتھراکنوز',
                'ہوا، ڈرپ، ضرورت پر اسپرے ٹائمنگ'
            )
        );

        $rows[] = $this->make('strawberry', 'Strawberry', 'اسٹرابری', '🍓', 'fruit', 8, 26, true,
            $this->pack(
                '25–35 mm/week; keep crown dry', 'Frequent light water; drip preferred', 'Morning',
                'Wet crowns cause rot — water soil not leaves',
                'Raised beds, sandy-loam + organic matter', 'pH 5.5–6.5',
                '15–24°C', 'Heat above 30°C softens fruit fast',
                '60–90 days to first berries', 'Pick fully red; cool immediately',
                'Mulch under berries; renew plants timely',
                'Aphids, spider mites', 'Gray mold, leaf spot',
                'Airflow, drip, remove rotting fruit daily'
            ),
            $this->pack(
                'ہفتہ وار 25–35 ملی میٹر؛ تاج خشک رکھیں', 'بار بار ہلکی آبپاشی؛ ڈرپ بہتر', 'صبح',
                'گیلا تاج سڑاتا ہے — پتے نہیں، مٹی گیلی کریں',
                'اونچی بیڈ، ریتیلی لوم + نامیاتی مادہ', 'pH 5.5–6.5',
                '15–24°C', '30°C سے اوپر پھل جلد نرم',
                'پہلی بیری 60–90 دن', 'پوری سرخ توڑیں؛ فوراً ٹھنڈا کریں',
                'بیری کے نیچے ملچ؛ پودے وقت پر تبدیل',
                'افڈ، مکڑی مائٹ', 'گری مولڈ، لیف اسپاٹ',
                'ہوا، ڈرپ، سڑا پھل روز ہٹائیں'
            )
        );

        // Fix typo in onion/chilli urdu thrips
        return array_map(function ($row) {
            $ur = $row['details_ur'];
            if (isset($ur['problems']['pests'])) {
                $ur['problems']['pests'] = str_replace('تھrips', 'تھرپس', $ur['problems']['pests']);
                $row['details_ur'] = $ur;
            }

            return $row;
        }, $rows);
    }

    private function make(
        string $slug,
        string $nameEn,
        string $nameUr,
        string $emoji,
        string $category,
        float $tMin,
        float $tMax,
        bool $rainSensitive,
        array $en,
        array $ur
    ): array {
        return [
            'slug' => $slug,
            'name_en' => $nameEn,
            'name_ur' => $nameUr,
            'emoji' => $emoji,
            'category' => $category,
            'temp_min_c' => $tMin,
            'temp_max_c' => $tMax,
            'sensitive_to_rain' => $rainSensitive,
            'details_en' => $en,
            'details_ur' => $ur,
        ];
    }

    private function pack(
        string $amount,
        string $frequency,
        string $bestTime,
        string $warning,
        string $soilType,
        string $soilReq,
        string $tempRange,
        string $tempWarn,
        string $period,
        string $harvest,
        string $care,
        string $pests,
        string $diseases,
        string $prevention
    ): array {
        return [
            'water' => [
                'amount' => $amount,
                'frequency' => $frequency,
                'best_time' => $bestTime,
                'warning' => $warning,
            ],
            'soil' => [
                'type' => $soilType,
                'requirements' => $soilReq,
            ],
            'temperature' => [
                'range' => $tempRange,
                'warning' => $tempWarn,
            ],
            'growth' => [
                'period' => $period,
                'harvest' => $harvest,
                'care' => $care,
            ],
            'problems' => [
                'pests' => $pests,
                'diseases' => $diseases,
                'prevention' => $prevention,
            ],
        ];
    }
}
