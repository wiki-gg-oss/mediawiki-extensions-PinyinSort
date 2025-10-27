<?php

// Maintenance script, only runnable from shell
if (PHP_SAPI !== 'cli') {
	exit;
}

function uchr($code)
{
	return html_entity_decode('&#' . $code . ';', ENT_NOQUOTES, 'UTF-8');
}

$file = file_get_contents(__DIR__ . '/Unihan_Readings.txt');
$lines = explode("\n", $file);

$output = <<<EOT
<?php

/**
 * Chinese/Pinyin Conversion Table
 *
 * Automatically generated using maintenance/generate.php
 * Do not modify directly!
 *
 */

return [

EOT;

foreach ($lines as $line) {
	$line = trim($line);
	// Empty line
	if (!$line) {
		continue;
	}
	// Comment
	if ($line[0] === '#') {
		continue;
	}
	$comp = explode("\t", $line);
	// We only need mandarin
	if ($comp[1] !== 'kMandarin') {
		continue;
	}
	$code = hexdec(str_replace('U+', '', $comp[0]));
	$char = uchr($code);

	$pinyin = str_replace(['ā', 'á', 'ǎ', 'à'], 'a', explode(' ', $comp[2])[0]);
	$pinyin = str_replace(['ī', 'í', 'ǐ', 'ì'], 'i', $pinyin);
	$pinyin = str_replace(['ū', 'ú', 'ǔ', 'ù'], 'u', $pinyin);
	$pinyin = str_replace(['ē', 'é', 'ě', 'è', 'ê'], 'e', $pinyin);
	$pinyin = str_replace(['ō', 'ó', 'ǒ', 'ò'], 'o', $pinyin);
	$pinyin = str_replace(['ǖ', 'ǘ', 'ǚ', 'ǜ', 'ü'], 'v', $pinyin);

	$output .= "\t'{$char}' => '{$pinyin}',\n";
}

$output .= "    ];\n}\n";
file_put_contents(__DIR__ . '/../includes/ConversionTable.data.php', $output);
