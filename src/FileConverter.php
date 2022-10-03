<?php
declare(strict_types=1);

class FileConverter
{
    private FileReaderInterface $reader;
    private FileWriterInterface $writer;
    private ConverterInterface $converter;
    private int $bufferSize = 16384;

    public function __construct(
        FileReaderInterface $reader,
        FileWriterInterface $writer,
        ConverterInterface $converter
    ) {
        $this->reader = $reader;
        $this->writer = $writer;
        $this->converter = $converter;
    }

    /**
     * @throws Exception
     */
    public function convert()
    {
        $this->checkForErrors();

        while ($xmlString = $this->reader->readLine($this->bufferSize)) {
            $this->writer->write($this->converter->convert($xmlString));
        }

        $this->writer->write($this->converter->convert('', true));
    }

    /**
     * @throws Exception
     */
    private function checkForErrors()
    {
        do {
            $hasError = false;
            try {
                while ($xmlString = $this->reader->readLine($this->bufferSize)) {
                    $this->converter->convert($xmlString);
                }
                $this->converter->convert('', true);
            } catch (Exception $e) {
                $hasError = true;
                if (!$this->converter->canFix($e)) {
                    throw new Exception('', 1, $e);
                }
            } finally {
                $this->converter->reset();
                $this->reader->reset();
            }
        } while ($hasError);
    }
}