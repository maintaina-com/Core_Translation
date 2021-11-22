<?php

use Horde\Core\Translation\Data\JsonToPhpWriter;

function exitWithHelpText()
{
    $help = "This script will convert a json file into a subclass of GetTranslationBase to be used as a endpoint for gettings translation strings.
    Json File needs to be an (nested) object where the leafes are strings to be translated.";
    print($help);
    exit(0);
}

if ($argc != 2) {
    exitWithHelpText();
}

// this needs to be called from a different app it seems
// require_once dirname(__FILE__) . '/../lib/Application.php';
// Horde_Registry::appInit('', array('cli' => true));

$ns = $argv[1];
$filepath = $argv[2];

$converter = new JsonToPhpArrayWriter();
$converter->convert($ns, $filepath);
