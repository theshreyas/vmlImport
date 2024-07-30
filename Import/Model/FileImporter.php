<?php
declare(strict_types=1);

namespace Vml\Import\Model;

use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\File\Csv;
use Magento\Framework\Filesystem\Driver\File;
use Magento\Framework\Filesystem\Io\File as IoFileSystem;
use Magento\Framework\Serialize\SerializerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Input\InputInterface;
use Vml\Import\Api\Data\ImportInterface;

class FileImporter implements ImportInterface
{
    /**
     * FileImporter constructor
     *
     * @param Csv $csv
     * @param File $file
     * @param IoFileSystem $ioFileSystem
     * @param LoggerInterface $logger
     * @param SerializerInterface $serializer
     */
    public function __construct(
        private Csv $csv,
        private File $file,
        private IoFileSystem $ioFileSystem,
        private LoggerInterface $logger,
        private SerializerInterface $serializer
    ) {
    }

    /**
     * Read data from the file
     *
     * @param InputInterface $input
     * @return array
     * @throws LocalizedException
     */
    public function readData(InputInterface $input): array
    {
        $file = $input->getArgument(ImportInterface::FILE_PATH);
        if (!$this->file->isExists($file)) {
            throw new LocalizedException(__('Invalid file path or no file found.'));
        }
        $pathInfo = $this->ioFileSystem->getPathInfo($file);
        $extension = $pathInfo['extension'];
        switch ($extension) {
            case 'csv':
            case 'xlsx':
            case 'xls':
                $data = $this->retrieveCsvData($file);
                break;
            case 'json':
                $data = $this->retrieveJsonData($file);
                break;
            default:
                throw new LocalizedException(__('Invalid File Type'));
        }
        return $data;
    }

    /**
     * Retrieve data in array format from the csv file
     *
     * @param string $file
     * @return array
     */
    public function retrieveCsvData(string $file): array
    {
        $this->csv->setDelimiter(",");
        $data = $this->csv->getData($file);
        $headers = array_shift($data);
        return array_map(function ($row) use ($headers) {
            return array_combine($headers, $row);
        }, $data);
    }

    /**
     * Retrieve data in array format from the json file
     *
     * @param string $file
     * @return array
     * @throws LocalizedException
     */
    public function retrieveJsonData(string $file): array
    {
        try {
            $data = $this->file->fileGetContents($file);
        } catch (Exception $e) {
            $this->logger->critical($e->getMessage(), ['exception' => $e]);
            throw new LocalizedException(__('File Exception: ' . $e->getMessage()));
        }
        return $this->serializer->unserialize($data);
    }
}
