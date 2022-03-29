<?php

function exitWithHelpText()
{
    $help = <<<'HELPTEXT'
    This script will convert a json file into a subclass of GetTranslationBase to be used as a endpoint for gettings translation strings.
    Every json file in jsonPath will be added to the php array with the filename as key (namespace) and the json in the file as the value.
    Use it like this:
    > convert-json-translation-data-class namespace jsonPath
        namespace  - The namespace of the resulting php file
        jsonPath   - The path to the folder with the json files

    HELPTEXT;
    print($help);
    exit(0);
}

if ($argc != 3) {
    exitWithHelpText();
}


$ns = $argv[1];
$jsonPath = $argv[2];

require_once(__DIR__ . '/../src/JsonToPhpArrayWriter.php');

use Horde\Core\Translation\JsonToPhpArrayWriter;

$converter = new JsonToPhpArrayWriter();
$converter->convert($ns, $jsonPath);
