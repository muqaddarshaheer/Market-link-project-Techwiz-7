/**
 * Farmer panel full EN ↔ Urdu (word-to-word).
 * Uses: data-i18n keys, data-en/data-ur, data-ph-en/data-ph-ur
 */
(function () {
  'use strict';

  var dict = {
    'nav.overview': { en: 'Overview', ur: 'جائزہ' },
    'nav.dashboard': { en: 'Dashboard', ur: 'ڈیش بورڈ' },
    'nav.insights': { en: 'Insights', ur: 'رپورٹ' },
    'nav.calculator': { en: 'Crop Calculator', ur: 'فصل کیلکولیٹر' },
    'nav.produceGuide': { en: 'HarvestWise', ur: 'HarvestWise' },
    'nav.stall': { en: 'Stall', ur: 'اسٹال' },
    'nav.products': { en: 'Products', ur: 'پروڈکٹس' },
    'nav.slots': { en: 'Pickup slots', ur: 'پک اپ سلاٹ' },
    'nav.profile': { en: 'Stall profile', ur: 'اسٹال پروفائل' },
    'nav.account': { en: 'Account', ur: 'اکاؤنٹ' },
    'nav.sales': { en: 'Sales', ur: 'فروخت' },
    'nav.orders': { en: 'Orders', ur: 'آرڈرز' },
    'nav.reviews': { en: 'Reviews', ur: 'ریویوز' },
    'nav.accountGroup': { en: 'Account', ur: 'اکاؤنٹ' },
    'nav.notifications': { en: 'Notifications', ur: 'نوٹیفکیشنز' },
    'nav.logout': { en: 'Log out', ur: 'لاگ آؤٹ' },
    'top.panel': { en: 'Farmer panel', ur: 'کسان پینل' },
    'top.role': { en: 'Farmer', ur: 'کسان' },
    'top.site': { en: 'View site', ur: 'سائٹ دیکھیں' },

    'dash.kicker': { en: 'Stall', ur: 'اسٹال' },
    'dash.lead': { en: 'Manage stock, accept pickups, and track stall revenue in Rs.', ur: 'اسٹاک سنبھالیں، پک اپ قبول کریں، اور روپوں میں آمدنی دیکھیں۔' },
    'dash.products': { en: 'Products', ur: 'پروڈکٹس' },
    'dash.orders': { en: 'Orders', ur: 'آرڈرز' },
    'dash.status': { en: 'Stall status:', ur: 'اسٹال کی حیثیت:' },
    'dash.statusNote': { en: 'Listings stay hidden until an admin approves your stall. You can still finish your profile and pickup slots.', ur: 'ایڈمن منظوری تک لسٹنگز چھپی رہتی ہیں۔ آپ پروفائل اور پک اپ سلاٹ مکمل کر سکتے ہیں۔' },
    'dash.ordersStat': { en: 'Orders', ur: 'آرڈرز' },
    'dash.pending': { en: 'Pending', ur: 'زیرِ التوا' },
    'dash.revenue': { en: 'Revenue', ur: 'آمدنی' },
    'dash.productsStat': { en: 'Products', ur: 'پروڈکٹس' },
    'dash.recent': { en: 'Recent orders', ur: 'حالیہ آرڈرز' },
    'dash.lowStock': { en: 'Low stock', ur: 'کم اسٹاک' },
    'dash.best': { en: 'Best sellers', ur: 'بہترین فروخت' },
    'dash.none': { en: 'Nothing here yet.', ur: 'ابھی کچھ نہیں۔' },
    'dash.listen': { en: 'Listen', ur: 'سنیں' },

    'ins.title': { en: 'Sales insights', ur: 'فروخت کی رپورٹ' },
    'ins.today': { en: 'Today', ur: 'آج' },
    'ins.7': { en: '7 days', ur: '۷ دن' },
    'ins.30': { en: '30 days', ur: '۳۰ دن' },
    'ins.apply': { en: 'Apply', ur: 'لگائیں' },
    'ins.ordersRange': { en: 'Orders (selected range)', ur: 'آرڈرز (منتخب مدت)' },
    'ins.revenueRange': { en: 'Revenue (selected range)', ur: 'آمدنی (منتخب مدت)' },
    'ins.dayByDay': { en: 'Day by day', ur: 'دن بہ دن' },
    'ins.day': { en: 'Day', ur: 'دن' },
    'ins.orders': { en: 'Orders', ur: 'آرڈرز' },
    'ins.revenue': { en: 'Revenue', ur: 'آمدنی' },
    'ins.best': { en: 'Best sellers', ur: 'بہترین فروخت کنندہ' },
    'ins.sold': { en: 'sold', ur: 'فروخت' },
    'ins.views': { en: 'views', ur: 'ویوز' },
    'ins.emptySales': { en: 'No sales data in this range yet.', ur: 'اس مدت میں ابھی سیلز ڈیٹا نہیں ملا۔' },
    'ins.emptyBest': { en: 'No best-seller data yet.', ur: 'ابھی بہترین فروخت کا ڈیٹا نہیں۔' },

    'ord.kicker': { en: 'Sales', ur: 'فروخت' },
    'ord.title': { en: 'Orders', ur: 'آرڈرز' },
    'ord.all': { en: 'All statuses', ur: 'تمام حیثیتیں' },
    'ord.accept': { en: 'Accept', ur: 'قبول کریں' },
    'ord.decline': { en: 'Decline', ur: 'مسترد کریں' },
    'ord.ready': { en: 'Mark ready', ur: 'تیار کریں' },
    'ord.complete': { en: 'Mark completed', ur: 'مکمل کریں' },
    'ord.note': { en: 'Note:', ur: 'نوٹ:' },
    'ord.notePh': { en: 'Optional note', ur: 'اختیاری نوٹ' },
    'ord.reasonPh': { en: 'Reason', ur: 'وجہ' },
    'ord.empty': { en: 'No orders for this filter.', ur: 'اس فلٹر میں کوئی آرڈر نہیں۔' },
    'ord.placed': { en: 'placed', ur: 'نیا' },
    'ord.accepted': { en: 'accepted', ur: 'قبول' },
    'ord.ready_for_pickup': { en: 'ready for pickup', ur: 'پک اپ کے لیے تیار' },
    'ord.completed': { en: 'completed', ur: 'مکمل' },
    'ord.declined': { en: 'declined', ur: 'مسترد' },
    'ord.cancelled': { en: 'cancelled', ur: 'منسوخ' },

    'rev.title': { en: 'Reviews', ur: 'ریویوز' },
    'rev.reply': { en: 'Reply', ur: 'جواب دیں' },
    'rev.replyPh': { en: 'Write your reply…', ur: 'اپنا جواب لکھیں…' },

    'slot.title': { en: 'Pickup slots', ur: 'پک اپ سلاٹ' },
    'slot.cutoff': { en: 'Cutoff hours before the slot starts', ur: 'سلاٹ شروع ہونے سے کتنے گھنٹے پہلے بند' },
    'slot.addPh': { en: 'Add another slot, e.g. 14:00-16:00', ur: 'نیا سلاٹ شامل کریں، جیسے 14:00-16:00' },
    'slot.save': { en: 'Save slots', ur: 'سلاٹ محفوظ کریں' },

    'prof.kicker': { en: 'Stall', ur: 'اسٹال' },
    'prof.title': { en: 'Stall profile', ur: 'اسٹال پروفائل' },
    'prof.lead': { en: 'Markets, hours, and the photo shoppers see.', ur: 'مارکیٹس، اوقات، اور وہ تصویر جو خریدار دیکھتے ہیں۔' },
    'prof.accountLink': { en: 'Account & password', ur: 'اکاؤنٹ اور پاس ورڈ' },
    'prof.stallName': { en: 'Stall name', ur: 'اسٹال کا نام' },
    'prof.contact': { en: 'Contact person', ur: 'رابطہ شخص' },
    'prof.phone': { en: 'Phone', ur: 'فون' },
    'prof.lat': { en: 'Latitude', ur: 'عرض بلد' },
    'prof.lng': { en: 'Longitude', ur: 'طول بلد' },
    'prof.address': { en: 'Address', ur: 'پتہ' },
    'prof.about': { en: 'About the stall', ur: 'اسٹال کے بارے میں' },
    'prof.days': { en: 'Operating days', ur: 'کام کے دن' },
    'prof.logo': { en: 'Stall logo', ur: 'اسٹال لوگو' },
    'prof.currentPhoto': { en: 'Current stall photo', ur: 'موجودہ اسٹال تصویر' },
    'prof.markets': { en: 'Markets', ur: 'مارکیٹس' },
    'prof.save': { en: 'Save profile', ur: 'پروفائل محفوظ کریں' },
    'prof.Mon': { en: 'Monday', ur: 'پیر' },
    'prof.Tue': { en: 'Tuesday', ur: 'منگل' },
    'prof.Wed': { en: 'Wednesday', ur: 'بدھ' },
    'prof.Thu': { en: 'Thursday', ur: 'جمعرات' },
    'prof.Fri': { en: 'Friday', ur: 'جمعہ' },
    'prof.Sat': { en: 'Saturday', ur: 'ہفتہ' },
    'prof.Sun': { en: 'Sunday', ur: 'اتوار' },

    'acc.kicker': { en: 'Account', ur: 'اکاؤنٹ' },
    'acc.title': { en: 'Your account', ur: 'آپ کا اکاؤنٹ' },
    'acc.lead': { en: 'Photo, contact details, and password.', ur: 'تصویر، رابطہ تفصیل، اور پاس ورڈ۔' },

    'products.kicker': { en: 'Stall', ur: 'اسٹال' },
    'products.title': { en: 'Products', ur: 'پروڈکٹس' },
    'products.lead': { en: 'Add and manage your stall items easily.', ur: 'اپنے اسٹال کی چیزیں آسانی سے شامل اور سنبھالیں۔' },
    'products.addTitle': { en: 'Add new product', ur: 'نیا پروڈکٹ شامل کریں' },
    'products.name': { en: 'Product name', ur: 'پروڈکٹ کا نام' },
    'products.category': { en: 'Category', ur: 'قسم' },
    'products.market': { en: 'Market', ur: 'مارکیٹ' },
    'products.price': { en: 'Price (Rs)', ur: 'قیمت (روپے)' },
    'products.unit': { en: 'Unit', ur: 'یونٹ' },
    'products.quality': { en: 'Quality', ur: 'کوالٹی' },
    'products.stock': { en: 'Stock', ur: 'اسٹاک' },
    'products.image': { en: 'Photo', ur: 'تصویر' },
    'products.desc': { en: 'Description (optional)', ur: 'تفصیل (اختیاری)' },
    'products.available': { en: 'Available for sale', ur: 'فروخت کے لیے دستیاب' },
    'products.addBtn': { en: 'Add product', ur: 'پروڈکٹ شامل کریں' },
    'products.listTitle': { en: 'Your products', ur: 'آپ کے پروڈکٹس' },
    'products.live': { en: 'Live', ur: 'لائیو' },
    'products.soldout': { en: 'Sold out', ur: 'ختم' },
    'products.save': { en: 'Save', ur: 'محفوظ کریں' },
    'products.delete': { en: 'Delete', ur: 'حذف کریں' },
    'products.views': { en: 'views', ur: 'ویوز' },
    'products.saves': { en: 'saves', ur: 'محفوظ' },
    'products.templateSave': { en: 'Save weekly template', ur: 'ہفتہ وار ٹیمپلیٹ محفوظ کریں' },
    'products.templateApply': { en: 'Apply template', ur: 'ٹیمپلیٹ لگائیں' },
    'products.empty': { en: 'No products yet. Add your first item above.', ur: 'ابھی کوئی پروڈکٹ نہیں۔ اوپر پہلا آئٹم شامل کریں۔' },
    'products.needMarket': { en: 'Join at least one market on your profile, and wait for approval, before listing products.', ur: 'پروڈکٹ لگانے سے پہلے پروفائل پر کم از کم ایک مارکیٹ جوائن کریں اور منظوری کا انتظار کریں۔' },
    'products.premium': { en: 'Premium', ur: 'پریمیم' },
    'products.fresh': { en: 'Fresh', ur: 'تازہ' },
    'products.standard': { en: 'Standard', ur: 'معیاری' },

    'calc.badge': { en: 'Crop Profit Calculator', ur: 'فصل منافع کیلکولیٹر' },
    'calc.title': { en: 'Plan your harvest numbers', ur: 'اپنی پیداوار کا حساب لگائیں' },
    'calc.lead': { en: 'Land, cost, sale — estimate only. Crop tips update on the side.', ur: 'زمین، خرچہ، فروخت — صرف اندازہ۔ کنارے پر فصل کے مشورے بدلتے ہیں۔' },
    'calc.step1': { en: 'Which crop?', ur: 'کون سی فصل؟' },
    'calc.step2': { en: 'How much land?', ur: 'کتنی زمین؟' },
    'calc.step3': { en: 'Total cost (Rs)', ur: 'کل خرچہ (روپے)' },
    'calc.step4': { en: 'Expected sale', ur: 'متوقع فروخت' },
    'calc.crop': { en: 'Crop', ur: 'فصل' },
    'calc.cropOther': { en: 'Crop name', ur: 'فصل کا نام' },
    'calc.cropOtherPh': { en: 'Your crop name', ur: 'اپنی فصل کا نام' },
    'calc.land': { en: 'Land amount', ur: 'رقبہ' },
    'calc.unit': { en: 'Unit', ur: 'یونٹ' },
    'calc.cost': { en: 'Total estimated cost', ur: 'کل اندازاً خرچہ' },
    'calc.costHint': { en: 'Seed + water + labor + transport — one total.', ur: 'بیج + پانی + مزدوری + ٹرانسپورٹ — ایک کل۔' },
    'calc.prod': { en: 'Expected harvest', ur: 'متوقع پیداوار' },
    'calc.rate': { en: 'Selling rate (Rs)', ur: 'بیچنے کا ریٹ (روپے)' },
    'calc.waste': { en: 'Wastage estimate', ur: 'ضائع ہونے کا اندازہ' },
    'calc.submit': { en: 'Calculate profit', ur: 'منافع نکالیں' },
    'calc.insightKicker': { en: 'About your fasal', ur: 'آپ کی فصل کے بارے میں' },
    'calc.insightTips': { en: 'Practical tips', ur: 'عملی مشورے' },
    'calc.insightWatch': { en: 'Watch this season', ur: 'اس موسم میں خیال رکھیں' },
    'calc.insightNote': { en: 'General farm notes — not a disease diagnosis.', ur: 'عمومی فارم نوٹس — بیماری کی تشخیص نہیں۔' },
    'calc.summary': { en: 'Summary', ur: 'خلاصہ' },
    'calc.estimateOnly': { en: 'Estimate only — not a guarantee', ur: 'صرف اندازہ — یقینی نہیں' },
    'calc.export': { en: 'Export', ur: 'ایکسپورٹ' },
    'calc.print': { en: 'Print', ur: 'پرنٹ' },
    'calc.wa': { en: 'WhatsApp number', ur: 'واٹس ایپ نمبر' },
    'calc.send': { en: 'Send', ur: 'بھیجیں' },
    'calc.waHint': { en: 'Enter Pakistan number — message opens in WhatsApp.', ur: 'پاکستان نمبر لکھیں — پیغام واٹس ایپ میں کھلے گا۔' },
    'calc.profit': { en: 'Estimated Profit', ur: 'اندازاً منافع' },
    'calc.loss': { en: 'Estimated Loss', ur: 'اندازاً نقصان' },
  };

  // Exact phrase fallback for leftover English in farmer content
  var phrases = [
    ['Farmer dashboard', 'کسان ڈیش بورڈ'],
    ['Incoming orders', 'آنے والے آرڈرز'],
    ['Sales insights', 'فروخت کی رپورٹ'],
    ['Pickup slots', 'پک اپ سلاٹ'],
    ['Stall profile', 'اسٹال پروفائل'],
    ['Your account', 'آپ کا اکاؤنٹ'],
    ['Smart Crop Calculator', 'سمارٹ فصل کیلکولیٹر'],
    ['Crop Calculator', 'فصل کیلکولیٹر'],
    ['Products', 'پروڈکٹس'],
    ['Orders', 'آرڈرز'],
    ['Reviews', 'ریویوز'],
    ['Dashboard', 'ڈیش بورڈ'],
    ['Insights', 'رپورٹ'],
    ['Account', 'اکاؤنٹ'],
    ['Notifications', 'نوٹیفکیشنز'],
    ['Log out', 'لاگ آؤٹ'],
    ['View site', 'سائٹ دیکھیں'],
    ['Farmer panel', 'کسان پینل'],
    ['Farmer', 'کسان'],
    ['Save', 'محفوظ کریں'],
    ['Delete', 'حذف کریں'],
    ['Accept', 'قبول کریں'],
    ['Decline', 'مسترد کریں'],
    ['Reply', 'جواب دیں'],
    ['Apply', 'لگائیں'],
    ['Listen', 'سنیں'],
    ['Today', 'آج'],
    ['Pending', 'زیرِ التوا'],
    ['Revenue', 'آمدنی'],
    ['Stock', 'اسٹاک'],
    ['Live', 'لائیو'],
    ['Sold out', 'ختم'],
    ['Mark ready', 'تیار کریں'],
    ['Mark completed', 'مکمل کریں'],
    ['Save slots', 'سلاٹ محفوظ کریں'],
    ['Save profile', 'پروفائل محفوظ کریں'],
    ['Add product', 'پروڈکٹ شامل کریں'],
    ['See Result', 'نتیجہ دیکھیں'],
    ['Fill by voice', 'آواز سے بھریں'],
    ['Export', 'ایکسپورٹ'],
    ['Print', 'پرنٹ'],
    ['Send', 'بھیجیں'],
    ['Optional note', 'اختیاری نوٹ'],
    ['Reason', 'وجہ'],
    ['All statuses', 'تمام حیثیتیں'],
    ['No orders for this filter.', 'اس فلٹر میں کوئی آرڈر نہیں۔'],
    ['Day by day', 'دن بہ دن'],
    ['Best sellers', 'بہترین فروخت کنندہ'],
    ['Recent orders', 'حالیہ آرڈرز'],
    ['Low stock', 'کم اسٹاک'],
    ['Operating days', 'کام کے دن'],
    ['Monday', 'پیر'],
    ['Tuesday', 'منگل'],
    ['Wednesday', 'بدھ'],
    ['Thursday', 'جمعرات'],
    ['Friday', 'جمعہ'],
    ['Saturday', 'ہفتہ'],
    ['Sunday', 'اتوار'],
    ['Premium', 'پریمیم'],
    ['Fresh', 'تازہ'],
    ['Standard', 'معیاری'],
    ['placed', 'نیا'],
    ['accepted', 'قبول'],
    ['ready for pickup', 'پک اپ کے لیے تیار'],
    ['completed', 'مکمل'],
    ['declined', 'مسترد'],
    ['Manage orders', 'آرڈرز سنبھالیں'],
    ['No pickup orders yet.', 'ابھی کوئی پک اپ آرڈر نہیں۔'],
    ['Stock looks healthy.', 'اسٹاک ٹھیک لگ رہا ہے۔'],
    ['Windows and cutoff hours', 'وقت اور بند ہونے کے گھنٹے'],
    ['Sales by day', 'دن بہ دن فروخت'],
    ['Photo, phone, markets', 'تصویر، فون، مارکیٹس'],
    ['Avatar & password', 'تصویر اور پاس ورڈ'],
    ['Your details', 'آپ کی تفصیل'],
    ['Change password', 'پاس ورڈ تبدیل کریں'],
    ['Update password', 'پاس ورڈ اپ ڈیٹ کریں'],
    ['Profile photo', 'پروفائل تصویر'],
    ['Current password', 'موجودہ پاس ورڈ'],
    ['New password', 'نیا پاس ورڈ'],
    ['Confirm password', 'پاس ورڈ تصدیق'],
    ['Save profile', 'پروفائل محفوظ کریں'],
    ['Save stall profile', 'اسٹال پروفائل محفوظ کریں'],
  ];

  function t(key, lang) {
    var row = dict[key];
    if (!row) return key;
    return row[lang] || row.en || key;
  }

    function setText(el, text) {
      if (el.tagName === 'OPTION' || el.tagName === 'TITLE') {
        el.textContent = text;
        return;
      }
      var labeled = el.querySelector(':scope > span');
      if (labeled && !labeled.querySelector('i') && el.querySelector(':scope > i.bi')) {
        labeled.textContent = text;
        return;
      }
      if (el.hasAttribute('data-i18n') && el.children.length === 0) {
        el.textContent = text;
        return;
      }
      if (el.children.length === 0) {
        el.textContent = text;
        return;
      }
      // Prefer nested span with same key / plain span
      var span = el.querySelector('span[data-i18n], span:not(.badge):not(.ms-auto)');
      if (span && span.children.length === 0) {
        span.textContent = text;
        return;
      }
      el.textContent = text;
    }

  function applyAttrPairs(lang) {
    document.querySelectorAll('[data-en][data-ur]').forEach(function (el) {
      var text = lang === 'ur' ? el.getAttribute('data-ur') : el.getAttribute('data-en');
      if (text == null) return;
      if (el.tagName === 'OPTION') {
        el.textContent = text;
      } else if (el.tagName === 'INPUT' || el.tagName === 'TEXTAREA') {
        // ignore — use ph attrs
      } else {
        setText(el, text);
      }
    });

    document.querySelectorAll('[data-ph-en][data-ph-ur]').forEach(function (el) {
      el.setAttribute('placeholder', lang === 'ur' ? el.getAttribute('data-ph-ur') : el.getAttribute('data-ph-en'));
    });
  }

  function applyKeys(lang) {
    document.querySelectorAll('[data-i18n]').forEach(function (el) {
      var key = el.getAttribute('data-i18n');
      if (!key || !dict[key]) return;
      setText(el, t(key, lang));
    });
    document.querySelectorAll('[data-i18n-placeholder]').forEach(function (el) {
      var key = el.getAttribute('data-i18n-placeholder');
      if (key && dict[key]) el.setAttribute('placeholder', t(key, lang));
    });
  }

  function applyPhrases(lang) {
    if (lang !== 'ur') return;
    var root = document.querySelector('.farmer-content') || document.querySelector('.admin-main');
    if (!root) return;
    var walker = document.createTreeWalker(root, NodeFilter.SHOW_TEXT, null);
    var nodes = [];
    while (walker.nextNode()) nodes.push(walker.currentNode);
    nodes.forEach(function (node) {
      if (!node.parentElement) return;
      var tag = node.parentElement.tagName;
      if (tag === 'SCRIPT' || tag === 'STYLE' || tag === 'CODE' || tag === 'PRE') return;
      if (node.parentElement.closest('[data-i18n],[data-en],[data-no-i18n]')) return;
      var raw = node.nodeValue;
      if (!raw || !raw.trim()) return;
      var out = raw;
      phrases.forEach(function (pair) {
        if (out.indexOf(pair[0]) !== -1) {
          out = out.split(pair[0]).join(pair[1]);
        }
      });
      if (out !== raw) node.nodeValue = out;
    });
  }

  function syncWeather(lang) {
    var card = document.getElementById('weatherCard');
    if (!card) return;
    var ur = lang === 'ur';
    card.setAttribute('data-lang', ur ? 'ur' : 'en');
    card.querySelectorAll('.wx-en').forEach(function (el) { el.hidden = ur; });
    card.querySelectorAll('.wx-ur').forEach(function (el) { el.hidden = !ur; });
    var dayModal = document.getElementById('weatherDayModal');
    if (dayModal) {
      dayModal.querySelectorAll('.wx-en').forEach(function (el) { el.hidden = ur; });
      dayModal.querySelectorAll('.wx-ur').forEach(function (el) { el.hidden = !ur; });
    }
    var label = document.getElementById('weatherTranslateLabel');
    if (label) label.textContent = ur ? 'English' : 'اردو';
    var voiceLabel = document.getElementById('weatherVoiceLabel');
    if (voiceLabel) voiceLabel.textContent = ur ? 'سنیں' : 'Listen';
    var btn = document.getElementById('weatherTranslateBtn');
    if (btn) btn.setAttribute('aria-pressed', ur ? 'true' : 'false');
    localStorage.setItem('ml-weather-ur', ur ? '1' : '0');
  }

  function applyPageTitle(lang) {
    var el = document.querySelector('.admin-top strong');
    if (!el) return;
    if (!el.getAttribute('data-en-title')) {
      el.setAttribute('data-en-title', el.textContent.trim());
    }
    var en = el.getAttribute('data-en-title');
    var map = {
      'Farmer dashboard': 'کسان ڈیش بورڈ',
      'Incoming orders': 'آنے والے آرڈرز',
      'Insights': 'رپورٹ',
      'Pickup slots': 'پک اپ سلاٹ',
      'Stall profile': 'اسٹال پروفائل',
      'Account': 'اکاؤنٹ',
      'Products': 'پروڈکٹس',
      'Reviews': 'ریویوز',
      'Crop Calculator': 'فصل کیلکولیٹر',
      'Smart Crop Calculator': 'سمارٹ فصل کیلکولیٹر',
      'Notifications': 'نوٹیفکیشنز',
    };
    el.textContent = lang === 'ur' && map[en] ? map[en] : en;
  }

  function apply(lang) {
    lang = lang === 'ur' ? 'ur' : 'en';
    document.documentElement.setAttribute('data-farmer-lang', lang);
    document.documentElement.setAttribute('lang', lang === 'ur' ? 'ur' : 'en');
    document.documentElement.setAttribute('dir', lang === 'ur' ? 'rtl' : 'ltr');
    localStorage.setItem('farmer-lang', lang);

    if (lang === 'en') {
      applyKeys(lang);
      applyAttrPairs(lang);
    } else {
      applyKeys(lang);
      applyAttrPairs(lang);
      applyPhrases(lang);
    }

    applyPageTitle(lang);
    syncWeather(lang);

    var btn = document.getElementById('farmerLangBtn');
    if (btn) {
      btn.setAttribute('aria-pressed', lang === 'ur' ? 'true' : 'false');
      btn.innerHTML = lang === 'ur'
        ? '<i class="bi bi-translate"></i> <span>EN</span>'
        : '<i class="bi bi-translate"></i> <span>اردو</span>';
      btn.title = lang === 'ur' ? 'Switch to English' : 'پورے پینل کو اردو میں دیکھیں';
    }

    window.dispatchEvent(new CustomEvent('farmer-lang-changed', { detail: { lang: lang } }));
  }

  function boot() {
    var lang = localStorage.getItem('farmer-lang') || 'en';
    apply(lang);
    var btn = document.getElementById('farmerLangBtn');
    if (btn) {
      btn.addEventListener('click', function () {
        var cur = document.documentElement.getAttribute('data-farmer-lang') || 'en';
        if (cur === 'ur') {
          // Full restore: soft reload to undo phrase mutations cleanly
          localStorage.setItem('farmer-lang', 'en');
          location.reload();
          return;
        }
        apply('ur');
      });
    }
  }

  window.farmerI18n = { t: t, apply: apply, dict: dict };

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', boot);
  } else {
    boot();
  }
})();
