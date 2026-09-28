<?php

/**
 * Build polished MarketLink Word documentation into:
 * TechWiz Knight Documentation/Documentation/
 */
$root = dirname(__DIR__);
$mdPath = $root.DIRECTORY_SEPARATOR.'DOCUMENTATION.md';
$docsDir = $root.DIRECTORY_SEPARATOR.'TechWiz Knight Documentation'.DIRECTORY_SEPARATOR.'Documentation';
$outPath = $docsDir.DIRECTORY_SEPARATOR.'MarketLink-TechWiz-Documentation.docx';
$legacyPath = $docsDir.DIRECTORY_SEPARATOR.'MarketLink-Complete-Documentation.docx';
$shotsDir = $root.DIRECTORY_SEPARATOR.'TechWiz Knight Documentation'.DIRECTORY_SEPARATOR.'Screenshots';

if (! is_dir($docsDir)) {
    mkdir($docsDir, 0777, true);
}
if (! is_file($mdPath)) {
    fwrite(STDERR, "DOCUMENTATION.md not found\n");
    exit(1);
}

$md = file_get_contents($mdPath);
$lines = preg_split("/\r\n|\n|\r/", $md);

$paragraphs = [];
$paragraphs[] = wPara('MARKETLINK', true, 40, false, '1F6B45');
$paragraphs[] = wPara('Farmers’ Market Pickup Platform', true, 28, false, '0E3B2E');
$paragraphs[] = wPara('TechWiz Knight Documentation Pack', true, 22, false, '145C38');
$paragraphs[] = wPara('Complete Professional Project Documentation · Aptech TechWiz 7', false, 20, true);
$paragraphs[] = wPara('Author: Muqaddar Shaheer  ·  Generated: '.date('d F Y').'  ·  Stack: Laravel 11 · PHP 8.2+ · MySQL', false, 18);
$paragraphs[] = wEmpty();
$paragraphs[] = wPara('Business rule: No courier delivery. No online payment. Reserve online → collect at the stall → pay the farmer in person (Rs).', false, 19, true);
$paragraphs[] = wEmpty();
$paragraphs[] = wPara('This Word file lives inside: TechWiz Knight Documentation / Documentation', false, 18);
$paragraphs[] = wPara('Matching screenshots live inside: TechWiz Knight Documentation / Screenshots', false, 18);
$paragraphs[] = wPara('Human-written source code lives inside: TechWiz Knight Documentation / Source Code', false, 18);
$paragraphs[] = wEmpty();

// Screenshot index
$paragraphs[] = wPara('SCREENSHOT INDEX', true, 26, false, '1F6B45');
$paragraphs[] = wPara('All images below are included in the Screenshots folder of this documentation pack.', false, 19);
$paragraphs[] = wEmpty();

$shotGroups = [
    '01-Public-Website' => 'Public website pages',
    '02-Guest-Checkout' => 'Guest basket & checkout',
    '03-Customer-Panel' => 'Customer panel',
    '04-Farmer-Panel' => 'Farmer panel',
    '05-Admin-Panel' => 'Admin panel',
];

foreach ($shotGroups as $folder => $label) {
    $dir = $shotsDir.DIRECTORY_SEPARATOR.$folder;
    $paragraphs[] = wPara($label, true, 22, false, '0E3B2E');
    if (! is_dir($dir)) {
        $paragraphs[] = wPara('• (folder ready — add screenshots here)', false, 18, true);
        continue;
    }
    $files = glob($dir.DIRECTORY_SEPARATOR.'*.png');
    if (! $files) {
        $paragraphs[] = wPara('• (no images yet in this folder)', false, 18, true);
        continue;
    }
    sort($files);
    foreach ($files as $file) {
        $name = basename($file);
        $pretty = preg_replace('/^\d+-/', '', pathinfo($name, PATHINFO_FILENAME));
        $pretty = str_replace('-', ' ', $pretty);
        $paragraphs[] = wPara('• '.$pretty.'  →  Screenshots/'.$folder.'/'.$name, false, 18);
    }
    $paragraphs[] = wEmpty();
}

$paragraphs[] = wPara('FULL FEATURE & TECHNICAL DOCUMENTATION', true, 26, false, '1F6B45');
$paragraphs[] = wEmpty();

$skipTitleDup = true;
foreach ($lines as $line) {
    $line = rtrim($line);

    if ($skipTitleDup && preg_match('/^#\s+/', $line)) {
        $skipTitleDup = false;
        continue;
    }
    $skipTitleDup = false;

    if ($line === '') {
        $paragraphs[] = wEmpty();
        continue;
    }

    if (preg_match('/^#{1,3}\s+(.*)$/', $line, $m)) {
        $hashes = strspn($line, '#');
        if ($hashes === 1) {
            $paragraphs[] = wEmpty();
            $paragraphs[] = wPara(stripMd($m[1]), true, 28, false, '1F6B45');
        } elseif ($hashes === 2) {
            $paragraphs[] = wPara(stripMd($m[1]), true, 24, false, '0E3B2E');
        } else {
            $paragraphs[] = wPara(stripMd($m[1]), true, 21, false, '145C38');
        }
        continue;
    }

    if (preg_match('/^>\s?(.*)$/', $line, $m)) {
        $paragraphs[] = wPara(stripMd($m[1]), false, 19, true);
        continue;
    }

    if (preg_match('/^[-*]\s+(.*)$/', $line, $m) || preg_match('/^\d+\.\s+(.*)$/', $line, $m)) {
        $paragraphs[] = wPara('• '.stripMd($m[1]), false, 20);
        continue;
    }

    if (preg_match('/^\|/', $line) || preg_match('/^```/', $line) || preg_match('/^---+$/', $line)) {
        if (preg_match('/^\|/', $line) && ! preg_match('/^\|\s*-+/', $line)) {
            $cells = array_values(array_filter(array_map('trim', explode('|', trim($line, '|')))));
            $paragraphs[] = wPara(stripMd(implode('   ·   ', $cells)), false, 18);
        }
        continue;
    }

    $paragraphs[] = wPara(stripMd($line), false, 20);
}

$contentTypes = <<<'XML'
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">
  <Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>
  <Default Extension="xml" ContentType="application/xml"/>
  <Override PartName="/word/document.xml" ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.document.main+xml"/>
</Types>
XML;

$rels = <<<'XML'
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
  <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="word/document.xml"/>
</Relationships>
XML;

$document = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
    .'<w:document xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main">'
    .'<w:body>'
    .implode('', $paragraphs)
    .'<w:sectPr>'
    .'<w:pgSz w:w="12240" w:h="15840"/>'
    .'<w:pgMar w:top="1296" w:right="1296" w:bottom="1296" w:left="1296"/>'
    .'</w:sectPr>'
    .'</w:body></w:document>';

if (is_file($outPath)) {
    @unlink($outPath);
}

$zip = new ZipArchive();
if ($zip->open($outPath, ZipArchive::CREATE) !== true) {
    fwrite(STDERR, "Cannot create docx\n");
    exit(1);
}
$zip->addFromString('[Content_Types].xml', $contentTypes);
$zip->addFromString('_rels/.rels', $rels);
$zip->addFromString('word/document.xml', $document);
$zip->close();

// Best-effort mirror for older filename if it is not locked/open.
if (@copy($outPath, $legacyPath)) {
    echo "Also updated {$legacyPath}\n";
}

echo "Wrote {$outPath}\n";
echo 'Size: '.filesize($outPath)." bytes\n";

function stripMd(string $text): string
{
    $text = preg_replace('/\*\*(.+?)\*\*/', '$1', $text);
    $text = preg_replace('/`([^`]+)`/', '$1', $text);
    $text = preg_replace('/\[([^\]]+)\]\([^)]+\)/', '$1', $text);
    $text = str_replace(['**', '__', '*', '`'], '', $text);

    return html_entity_decode(trim($text), ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

function wPara(string $text, bool $bold = false, int $size = 20, bool $italic = false, ?string $color = null): string
{
    $text = htmlspecialchars($text, ENT_XML1 | ENT_QUOTES, 'UTF-8');
    $rPr = '<w:rPr><w:rFonts w:ascii="Calibri" w:hAnsi="Calibri" w:cs="Calibri"/>'
        .'<w:sz w:val="'.($size * 2).'"/><w:szCs w:val="'.($size * 2).'"/>'
        .($bold ? '<w:b/><w:bCs/>' : '')
        .($italic ? '<w:i/><w:iCs/>' : '')
        .($color ? '<w:color w:val="'.$color.'"/>' : '')
        .'</w:rPr>';

    $pPr = '<w:pPr><w:spacing w:after="120" w:line="276" w:lineRule="auto"/></w:pPr>';

    return '<w:p>'.$pPr.'<w:r>'.$rPr.'<w:t xml:space="preserve">'.$text.'</w:t></w:r></w:p>';
}

function wEmpty(): string
{
    return '<w:p><w:pPr><w:spacing w:after="60"/></w:pPr><w:r><w:t></w:t></w:r></w:p>';
}
