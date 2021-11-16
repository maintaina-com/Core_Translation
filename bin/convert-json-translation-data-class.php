<?php

use Horde\Core\Translation\Data\JsonToPhpWriter;

$filepath = $argv[1];

$converter = new JsonToPhpArrayWriter();
$converter->convert($filepath);
