/**
 * MarketLink harvest data — seasons, crops, vitamins, matching helpers.
 * Source of truth for the advanced What’s Growing calendar.
 */
(function (global) {
  'use strict';

  var SEASONS = {
    winter: {
      id: 'winter',
      name: 'Winter',
      months: [12, 1, 2],
      starts: 'December 1',
      note: 'Winter opens on the first of December. The board leans on hardy greens, stored squash, and citrus.',
    },
    spring: {
      id: 'spring',
      name: 'Spring',
      months: [3, 4, 5],
      starts: 'March 1',
      note: 'Spring opens on the first of March. Spears, leaves, and the first berries follow the warming soil.',
    },
    summer: {
      id: 'summer',
      name: 'Summer',
      months: [6, 7, 8],
      starts: 'June 1',
      note: 'Summer opens on the first of June. Berries, stone fruit, and tender vegetables overlap for a short, fast season.',
    },
    autumn: {
      id: 'autumn',
      name: 'Autumn',
      months: [9, 10, 11],
      starts: 'September 1',
      note: 'Autumn opens on the first of September. Apples, pears, grapes, and winter squash take the stalls.',
    },
  };

  var CROPS = [
    { id: 'kale', name: 'Lacinato kale', kind: 'vegetable', glyph: 'kale', months: [1, 2, 3, 10, 11, 12], starts: 'October', info: 'A cold-hardy leaf that sweetens after frost. Bunches stay on the board from October through early spring.', vitamins: [{ name: 'Vitamin K', helps: 'Beneficial for normal blood clotting and bone strength.' }, { name: 'Vitamin C', helps: 'Beneficial for immune defense and helping the body absorb iron.' }, { name: 'Vitamin A', helps: 'Beneficial for vision and the health of skin and lining tissues.' }], benefits: ['Holds up in soup and a quick sauté', 'One of the last greens still growing when the market thins out'] },
    { id: 'cabbage', name: 'Savoy cabbage', kind: 'vegetable', glyph: 'greens', months: [1, 2, 11, 12], starts: 'November', info: 'Crinkled heads that store well in a cool kitchen. The main cut begins in November and runs through February.', vitamins: [{ name: 'Vitamin C', helps: 'Beneficial for immune defense and collagen, which supports skin and gums.' }, { name: 'Vitamin K', helps: 'Beneficial for blood clotting and keeping bones sturdy.' }, { name: 'Folate (B9)', helps: 'Beneficial for making new cells, including red blood cells.' }], benefits: ['Crunch and fiber for slaws and braises', 'Keeps for weeks without losing its bite'] },
    { id: 'leek', name: 'Leeks', kind: 'vegetable', glyph: 'onion', months: [1, 2, 3, 11, 12], starts: 'November', info: 'Mild alliums pulled from November into March. The white shank is the part most cooks want; the greens flavor stock.', vitamins: [{ name: 'Vitamin K', helps: 'Beneficial for normal clotting and bone health.' }, { name: 'Folate (B9)', helps: 'Beneficial for cell growth and healthy red blood cells.' }, { name: 'Vitamin C', helps: 'Beneficial for immune support, in a smaller amount than citrus.' }], benefits: ['Gentler than a raw onion', 'Useful for broths, tarts, and potato soup'] },
    { id: 'citrus', name: 'Meyer lemons', kind: 'fruit', glyph: 'citrus', months: [12, 1, 2], starts: 'December', info: 'Thin-skinned and floral. The local and coastal crop is at its best from December through February.', vitamins: [{ name: 'Vitamin C', helps: 'Beneficial for immune defense, iron absorption, and collagen for skin and gums.' }], benefits: ['Bright acid for winter cooking', 'Zest and juice from the same fruit'] },
    { id: 'sprout', name: 'Brussels sprouts', kind: 'vegetable', glyph: 'sprout', months: [1, 11, 12], starts: 'November', info: 'Tight little cabbages on a tall stalk. They are sweetest after a cold snap, from November into January.', vitamins: [{ name: 'Vitamin C', helps: 'Beneficial for immune defense and protecting cells from everyday wear.' }, { name: 'Vitamin K', helps: 'Beneficial for blood clotting and bone strength.' }, { name: 'Folate (B9)', helps: 'Beneficial for cell growth.' }], benefits: ['Roast well with a little fat and salt', 'A true cold-season crop'] },
    { id: 'squash', name: 'Winter squash', kind: 'vegetable', glyph: 'squash', months: [1, 9, 10, 11, 12], starts: 'September', info: 'Cured in September so the skin hardens, then sold through January. Butternut, kabocha, and delicata share this window.', vitamins: [{ name: 'Vitamin A', helps: 'Beneficial for vision, skin, and immune function. The orange flesh supplies it as beta-carotene.' }, { name: 'Vitamin C', helps: 'Beneficial for immune defense and collagen.' }], benefits: ['Stores for months in a cool room', 'Roasts into soup, pie, or a side'] },
    { id: 'spinach', name: 'Spinach', kind: 'vegetable', glyph: 'greens', months: [3, 4, 5, 10], starts: 'March', info: 'A cool-weather leaf with two runs: March through May, then a shorter return in October before hard frost.', vitamins: [{ name: 'Vitamin K', helps: 'Beneficial for clotting and bone health. Spinach is one of the richest leaves for it.' }, { name: 'Vitamin A', helps: 'Beneficial for vision and skin.' }, { name: 'Folate (B9)', helps: 'Beneficial for new cells and red blood cells.' }, { name: 'Vitamin C', helps: 'Beneficial for immune support and iron absorption.' }], benefits: ['Eaten raw when young, cooked when the leaves toughen', 'One of the first greens of spring'] },
    { id: 'radish', name: 'Radishes', kind: 'vegetable', glyph: 'radish', months: [4, 5, 6], starts: 'April', info: 'Fast roots, ready about a month after sowing. The crisp season is April through June.', vitamins: [{ name: 'Vitamin C', helps: 'Beneficial for immune defense. Radishes are a light source, not a heavy one.' }], benefits: ['Peppery crunch with almost no cooking', 'Leaves are edible when they are still tender'] },
    { id: 'asparagus', name: 'Asparagus', kind: 'vegetable', glyph: 'spear', months: [4, 5, 6], starts: 'April', info: 'Spears are cut daily once the soil warms. The window is brief: April into June, then the plants are left to fern.', vitamins: [{ name: 'Folate (B9)', helps: 'Beneficial for cell growth. Asparagus is a strong source.' }, { name: 'Vitamin K', helps: 'Beneficial for blood clotting and bones.' }, { name: 'Vitamin C', helps: 'Beneficial for immune defense.' }], benefits: ['Best the day it is cut', 'A spring crop you will not see again until next year'] },
    { id: 'rhubarb', name: 'Rhubarb', kind: 'vegetable', glyph: 'spear', months: [4, 5, 6], starts: 'April', info: 'Tart stalks, not a fruit, though cooks treat them like one. Pull from April through June. The leaves are not eaten.', vitamins: [{ name: 'Vitamin K', helps: 'Beneficial for clotting and bone health.' }, { name: 'Vitamin C', helps: 'Beneficial for immune support, in a modest amount.' }], benefits: ['Sharp flavor for crisps and compote', 'Pairs with the first strawberries'] },
    { id: 'pea', name: 'Snap peas', kind: 'vegetable', glyph: 'greens', months: [5, 6], starts: 'May', info: 'Eaten pod and all. They show up in May and finish as the weather turns hot in June.', vitamins: [{ name: 'Vitamin C', helps: 'Beneficial for immune defense and collagen.' }, { name: 'Vitamin K', helps: 'Beneficial for clotting and bones.' }, { name: 'Folate (B9)', helps: 'Beneficial for cell growth.' }], benefits: ['Sweet raw, and just as good barely cooked', 'A short season, so the crate goes quickly'] },
    { id: 'strawberry', name: 'Strawberries', kind: 'fruit', glyph: 'berry', months: [5, 6, 7], starts: 'May', info: 'Field berries start in May and peak in June. By late July the local flats are usually done.', vitamins: [{ name: 'Vitamin C', helps: 'Beneficial for immune defense, skin repair, and iron absorption. Strawberries are one of the best fruit sources.' }, { name: 'Folate (B9)', helps: 'Beneficial for making new cells.' }], benefits: ['Best unwashed until you eat them', 'The fruit that opens berry season'] },
    { id: 'cherry', name: 'Cherries', kind: 'fruit', glyph: 'berry', months: [6, 7], starts: 'June', info: 'A two-month stone fruit. Sweet cherries arrive in June; the last pie cherries linger into July.', vitamins: [{ name: 'Vitamin C', helps: 'Beneficial for immune defense and collagen.' }, { name: 'Vitamin A', helps: 'Beneficial for vision and skin, in a smaller amount than squash or carrots.' }], benefits: ['They do not store, so eat them this week', 'The deep red flesh also carries antioxidants beyond the vitamins'] },
    { id: 'basil', name: 'Basil', kind: 'herb', glyph: 'herb', months: [6, 7, 8, 9], starts: 'June', info: 'Warm-weather herb. Bunches start in June and keep coming until the first cool nights of September.', vitamins: [{ name: 'Vitamin K', helps: 'Beneficial for clotting and bones. A small bunch still contributes.' }, { name: 'Vitamin A', helps: 'Beneficial for vision and skin.' }, { name: 'Vitamin C', helps: 'Beneficial for immune support.' }], benefits: ['Turns tomatoes and peaches into a meal', 'A summer marker on the herb table'] },
    { id: 'blueberry', name: 'Blueberries', kind: 'fruit', glyph: 'berry', months: [7, 8], starts: 'July', info: 'Pints show up in July and August. The later berries are often the sweetest.', vitamins: [{ name: 'Vitamin C', helps: 'Beneficial for immune defense and collagen.' }, { name: 'Vitamin K', helps: 'Beneficial for blood clotting and bone health.' }], benefits: ['Freeze well if you buy extra', 'The deep color comes from anthocyanins, which sit alongside the vitamins'] },
    { id: 'tomato', name: 'Tomatoes', kind: 'fruit', glyph: 'tomato', months: [7, 8, 9], starts: 'July', info: 'Ripe fruit, not the early green ones. Expect them from July through September, with the heaviest crates in August.', vitamins: [{ name: 'Vitamin C', helps: 'Beneficial for immune defense and skin repair.' }, { name: 'Vitamin A', helps: 'Beneficial for vision and skin.' }, { name: 'Vitamin K', helps: 'Beneficial for clotting and bones, in a modest amount.' }], benefits: ['Cooked sauce concentrates lycopene, which is not a vitamin but travels with these', 'The center of the late-summer stall'] },
    { id: 'peach', name: 'Peaches', kind: 'fruit', glyph: 'peach', months: [7, 8], starts: 'July', info: 'Soft fruit with a short life. The local season is July and August; eat the ripe ones the day you buy them.', vitamins: [{ name: 'Vitamin C', helps: 'Beneficial for immune defense and collagen.' }, { name: 'Vitamin A', helps: 'Beneficial for vision and skin. The yellow-orange flesh is the clue.' }], benefits: ['Juice and aroma you cannot get from a stored peach', 'A peak-summer fruit'] },
    { id: 'zucchini', name: 'Zucchini', kind: 'vegetable', glyph: 'squash', months: [7, 8, 9], starts: 'July', info: 'Summer squash, picked small. The plants produce from July into September if the grower keeps cutting.', vitamins: [{ name: 'Vitamin C', helps: 'Beneficial for immune defense and collagen.' }, { name: 'Vitamin B6', helps: 'Beneficial for helping the body use protein and energy from food.' }, { name: 'Vitamin A', helps: 'Beneficial for vision and skin, especially in the deeper yellow ones.' }], benefits: ['Mild, so it takes on other flavors', 'Flowers are edible early in the same season'] },
    { id: 'cucumber', name: 'Cucumbers', kind: 'vegetable', glyph: 'greens', months: [7, 8], starts: 'July', info: 'Cool crunch in hot weather. Most stalls have them in July and August, then the vines slow down.', vitamins: [{ name: 'Vitamin K', helps: 'Beneficial for clotting and bone health. Most of it sits in the peel.' }, { name: 'Vitamin C', helps: 'Beneficial for immune support, in a modest amount.' }], benefits: ['Mostly water, which is the point on a hot day', 'Good raw, and useful as quick pickles'] },
    { id: 'corn', name: 'Sweet corn', kind: 'vegetable', glyph: 'corn', months: [8, 9], starts: 'August', info: 'Sugar turns to starch fast after picking. August and early September are the ears worth buying.', vitamins: [{ name: 'Thiamin (B1)', helps: 'Beneficial for turning carbohydrates from food into energy.' }, { name: 'Folate (B9)', helps: 'Beneficial for cell growth.' }, { name: 'Vitamin C', helps: 'Beneficial for immune support, in a smaller amount than peppers or berries.' }], benefits: ['Eat it the day it is picked', 'The late-summer crop people plan a meal around'] },
    { id: 'pepper', name: 'Sweet peppers', kind: 'vegetable', glyph: 'pepper', months: [8, 9, 10], starts: 'August', info: 'Green ones come first; red, orange, and yellow follow as they ripen on the plant. August through October.', vitamins: [{ name: 'Vitamin C', helps: 'Beneficial for immune defense and collagen. Red peppers are among the richest foods for it.' }, { name: 'Vitamin A', helps: 'Beneficial for vision and skin. The color deepens as this vitamin rises.' }, { name: 'Vitamin B6', helps: 'Beneficial for using protein and supporting a healthy mood and nervous system.' }], benefits: ['Raw or roasted', 'They bridge summer fruit and autumn squash'] },
    { id: 'apple', name: 'Apples', kind: 'fruit', glyph: 'apple', months: [8, 9, 10, 11], starts: 'August', info: 'Early varieties in August, the main crop in September and October, storage apples into November.', vitamins: [{ name: 'Vitamin C', helps: 'Beneficial for immune defense. Apples are a modest source, and more of it is in the skin.' }], benefits: ['Fiber, including in the skin', 'They keep, unlike berries'] },
    { id: 'pear', name: 'Pears', kind: 'fruit', glyph: 'pear', months: [8, 9, 10], starts: 'August', info: 'Often picked firm and ripened at home. Bartletts lead in August; later varieties carry October.', vitamins: [{ name: 'Vitamin C', helps: 'Beneficial for immune defense, in a modest amount.' }, { name: 'Vitamin K', helps: 'Beneficial for clotting and bone health.' }], benefits: ['Ripe when the neck gives under your thumb', 'A softer sweetness than apples, plus fiber'] },
    { id: 'fig', name: 'Black figs', kind: 'fruit', glyph: 'fig', months: [8, 9], starts: 'August', info: 'A brief crop. The main flush is late August into September, and a crate can sell out in a morning.', vitamins: [{ name: 'Vitamin K', helps: 'Beneficial for blood clotting and bone strength.' }, { name: 'Vitamin B6', helps: 'Beneficial for energy metabolism and a healthy nervous system.' }], benefits: ['Eaten fresh, not stored', 'One of the shortest fruit seasons on the board'] },
    { id: 'grape', name: 'Grapes', kind: 'fruit', glyph: 'grape', months: [9, 10], starts: 'September', info: 'Table grapes arrive as the nights cool. September and October are the months to look for local bunches.', vitamins: [{ name: 'Vitamin C', helps: 'Beneficial for immune defense and collagen.' }, { name: 'Vitamin K', helps: 'Beneficial for clotting and bones.' }], benefits: ['Easy fruit to share', 'The skins also carry polyphenols, which are not vitamins'] },
    { id: 'pumpkin', name: 'Pumpkins', kind: 'vegetable', glyph: 'squash', months: [9, 10, 11], starts: 'September', info: 'Field pumpkins for cooking, not just the porch. They are cut in September and sold through November.', vitamins: [{ name: 'Vitamin A', helps: 'Beneficial for vision, skin, and immune function. The orange flesh supplies it as beta-carotene.' }, { name: 'Vitamin C', helps: 'Beneficial for immune defense and collagen.' }], benefits: ['The flesh cooks like winter squash', 'Seeds are edible once roasted'] },
    { id: 'broccoli', name: 'Broccoli', kind: 'vegetable', glyph: 'kale', months: [3, 4, 10, 11], starts: 'October', info: 'Two cool seasons: a spring cut in March and April, and the stronger autumn run from October into November.', vitamins: [{ name: 'Vitamin C', helps: 'Beneficial for immune defense and collagen. Broccoli is a strong source.' }, { name: 'Vitamin K', helps: 'Beneficial for clotting and bone strength.' }, { name: 'Folate (B9)', helps: 'Beneficial for cell growth and red blood cells.' }, { name: 'Vitamin A', helps: 'Beneficial for vision and skin.' }], benefits: ['Better flavor in cool weather', 'A bridge crop between summer and the winter greens'] },
  ];

  var MONTH_NAMES = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
  var MONTH_NAMES_UR = ['جنوری', 'فروری', 'مارچ', 'اپریل', 'مئی', 'جون', 'جولائی', 'اگست', 'ستمبر', 'اکتوبر', 'نومبر', 'دسمبر'];
  var MONTH_SHORT = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

  var GLYPHS = {
    kale: '🥬', greens: '🥗', onion: '🧅', citrus: '🍋', sprout: '🥦', squash: '🎃',
    radish: '🔴', spear: '🌱', berry: '🫐', herb: '🌿', tomato: '🍅', peach: '🍑',
    corn: '🌽', pepper: '🫑', apple: '🍎', pear: '🍐', fig: '🍇', grape: '🍇',
  };

  function monthName(month, lang) {
    var names = lang === 'ur' ? MONTH_NAMES_UR : MONTH_NAMES;
    return names[month - 1];
  }

  function seasonForMonth(month) {
    return Object.values(SEASONS).find(function (season) {
      return season.months.indexOf(month) !== -1;
    });
  }

  function cropsInMonth(month) {
    return CROPS.filter(function (crop) {
      return crop.months.indexOf(month) !== -1;
    });
  }

  function cropOnDay(month, day) {
    var crops = cropsInMonth(month);
    if (!crops.length) return null;
    return crops[(day - 1) % crops.length];
  }

  function searchTerm(crop) {
    var word = crop.name.trim().split(/\s+/).pop().toLowerCase();
    if (/(ches|shes)$/.test(word)) return word.slice(0, -2);
    return word;
  }

  function rootOf(word) {
    var lower = word.toLowerCase();
    if (lower.length <= 4) return lower;
    return lower.replace(/(es|s)$/, '');
  }

  function hasCropWord(hay, root) {
    return hay.split(/[^a-z]+/).some(function (word) {
      return word === root || (word.indexOf(root) === 0 && word.length <= root.length + 2);
    });
  }

  function matchCrop(productName) {
    var hay = String(productName || '').toLowerCase();
    for (var i = 0; i < CROPS.length; i++) {
      var crop = CROPS[i];
      var root = rootOf(searchTerm(crop));
      if (root.length >= 3 && hasCropWord(hay, root)) return crop;
    }
    return null;
  }

  function glyphFor(crop) {
    if (!crop) return '🌱';
    return GLYPHS[crop.glyph] || '🌱';
  }

  global.MLHarvest = {
    SEASONS: SEASONS,
    CROPS: CROPS,
    MONTH_NAMES: MONTH_NAMES,
    MONTH_NAMES_UR: MONTH_NAMES_UR,
    MONTH_SHORT: MONTH_SHORT,
    monthName: monthName,
    seasonForMonth: seasonForMonth,
    cropsInMonth: cropsInMonth,
    cropOnDay: cropOnDay,
    searchTerm: searchTerm,
    matchCrop: matchCrop,
    glyphFor: glyphFor,
  };
})(window);
