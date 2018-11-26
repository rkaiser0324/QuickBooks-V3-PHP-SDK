<?php

require('./config.php');

require realpath(dirname(__FILE__) . '/../vendor/autoload.php');
use com\mikebevz\xsd2php\Xsd2Php;

function replace_string_in_file($filename, $string_to_replace, $replace_with){
	if (!$content=file_get_contents($filename))
	{
		throw new exception (sprintf("Cannot open \"%s\"", $filename));
	}
    $content_chunks=explode($string_to_replace, $content);
    $content=implode($replace_with, $content_chunks);
	file_put_contents($filename, $content);
}

$xml = new Xsd2Php('./XSD/Finance.xsd', true);
$xml->overrideAsSingleNamespace = 'http://schema.intuit.com/finance/v3';
$xml->saveClasses('./Data/', true);

replace_strings(dirname(__FILE__) . '/Data');

function replace_strings($directory)
{
	foreach (scandir($directory) as $file) {
		if ('.' === $file) continue;
		if ('..' === $file) continue;
		$full_path = realpath($directory . '/' . $file);
		if (is_dir($full_path))
			replace_strings($full_path . '/');
		else
		{
			replace_string_in_file($full_path, '@var \IPP', '@var IPP');
			replace_string_in_file($full_path, '@var IntuitObject', '@var IPPIntuitEntity');
			replace_string_in_file($full_path, '@var id', '@var string');
			replace_string_in_file($full_path, '@var EntityStatusEnum[]', '@var IPPEntityStatusEnum[]');
		}
	}
}

echo "\n";

