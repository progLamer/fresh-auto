<?php
declare(strict_types=1);

class XmlReverseConverter implements ConverterInterface
{

    /**
     * @var resource
     */
    private $parser;
    private string $xmlString;
    private array $fixers = [];

    public function __construct()
    {
        $this->initialize();
    }

    public function __destruct()
    {
        xml_parser_free($this->parser);
    }

    public function convert(string $xmlString, ?bool $isEnd = false): string
    {
        $this->xmlString = '';
        $line = xml_get_current_line_number($this->parser);

        if (!empty($this->fixers[$line])) {
            $xmlString = $this->applyFixes($line, $xmlString);
        }

        if (!xml_parse($this->parser, $xmlString, $isEnd)) {
            $errorCode = xml_get_error_code($this->parser);
            if (XML_ERROR_NONE != $errorCode) {
                throw new InvalidXmlException(xml_error_string($errorCode), $errorCode, null,
                    xml_get_current_line_number($this->parser), xml_get_current_column_number($this->parser));
            }
        }

        return $this->xmlString ?: $xmlString;
    }

    public function addFix(int $line,  FixerInterface $fixer)
    {
        $this->fixers[$line][] = $fixer;
    }

    public function reset()
    {
        xml_parser_free($this->parser);
        $this->initialize();
    }

    private function defaultHandler($parser, string $data)
    {
        $this->xmlString .= trim($data) == '' ? $data : strrev($data);
    }

    private function elementStartHandler($parser, string $name, array $attributes)
    {
        $newName = strtolower(strrev($name));
        $newAttrsString = '';
        foreach ($attributes as $attrName => $attrValue) {
            $newAttrName = strtolower(strrev($attrName));
            $newAttrsString .= sprintf(' %s="%s"', $newAttrName, strrev($attrValue));
        }
        $this->xmlString .= sprintf('<%s%s>', $newName, $newAttrsString);
    }

    private function elementEndHandler($parser, string $name)
    {
        $this->xmlString .= sprintf('</%s>', strtolower(strrev($name)));
    }

    /**
     * @throws Exception
     */
    private function instructionHandler($parser, string $target, string $data)
    {
        throw new Exception('Not implemented');
    }

    /**
     * @throws Exception
     */
    private function unparsedEntityDeclHandler(
        $parser,
        string $entityName,
        string $base,
        string $systemId,
        string $publicId,
        string $notationName
    ) {
        throw new Exception('Not implemented');
    }

    /**
     * @throws Exception
     */
    private function notationDeclHandler(
        $parser,
        string $notationName,
        string $base,
        string $systemId,
        string $publicId
    ) {
        throw new Exception('Not implemented');
    }

    /**
     * @throws Exception
     */
    private function externalEntityRefHandler(
        $parser,
        string $openEntityNames,
        string $base,
        string $systemId,
        string $publicId
    ) {
        throw new Exception('Not implemented');
    }

    /**
     * @throws Exception
     */
    private function startNamespaceDeclHandler($parser, string $prefix, string $uri)
    {
        throw new Exception('Not implemented');
    }

    /**
     * @throws Exception
     */
    private function endNamespaceDeclHandler($parser, string $prefix)
    {
        throw new Exception('Not implemented');
    }

    private function initialize()
    {
        $this->parser = xml_parser_create();
        xml_set_default_handler($this->parser, [$this, 'defaultHandler']);
        xml_set_processing_instruction_handler($this->parser, 'instructionHandler');
        xml_set_element_handler($this->parser, [$this, 'elementStartHandler'], [$this, 'elementEndHandler']);
        xml_set_character_data_handler($this->parser, [$this, 'defaultHandler']);
        xml_set_unparsed_entity_decl_handler($this->parser, 'unparsedEntityDeclHandler');
        xml_set_notation_decl_handler($this->parser, 'notationDeclHandler');
        xml_set_external_entity_ref_handler($this->parser, 'externalEntityRefHandler');
        xml_set_start_namespace_decl_handler($this->parser, 'startNamespaceDeclHandler');
        xml_set_end_namespace_decl_handler($this->parser, 'endNamespaceDeclHandler');
    }

    private function applyFixes(int $line, string $string): string
    {
        $fixedString = '';
        /** @var FixerInterface $fixer */
        foreach ($this->fixers[$line] as $fixer) {
            $fixedString = $fixer->apply($string);
        }

        return $fixedString;
    }

    public function canFix(InvalidXmlException $e): bool
    {
        if ($e->getCode() == 73) {
            $this->addFix($e->getFileLine() - 1, new XmlAppendStringAtEndOfLine(0, '>'));
            return true;
        }

        return false;
    }
}