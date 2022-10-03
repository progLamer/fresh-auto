<?php
declare(strict_types=1);

// Смысла использовать на автозагрузки классов нет
require_once 'src/ConverterInterface.php';
require_once 'src/XmlReverseConverter.php';
require_once 'src/FileConverter.php';
require_once 'src/InvalidXmlException.php';
require_once 'src/FileReaderInterface.php';
require_once 'src/FileReader.php';
require_once 'src/FileWriterInterface.php';
require_once 'src/FileWriter.php';
require_once 'src/FixerInterface.php';
require_once 'src/XmlAppendStringAtEndOfLine.php';

$srcFilename = $argv[1] ?? 'xml/source.xml';
$dstFilename = $argv[2] ?? 'destination.xml';

$converter = new FileConverter(new FileReader($srcFilename), new FileWriter($dstFilename), new XmlReverseConverter);
$converter->convert();