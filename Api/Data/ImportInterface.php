<?php
declare(strict_types=1);

namespace Vml\Import\Api\Data;

use Symfony\Component\Console\Input\InputInterface;

/**
 * Interface for importing data.
 */
interface ImportInterface
{
    public const PROFILE_NAME = "profile";
    public const FILE_PATH = "filepath";
    public const PROFILE_TYPES = ['sample-csv','sample-json'];

    /**
     * Read data from the file
     *
     * @param InputInterface $input
     * @return array
     */
    public function readData(InputInterface $input): array;

    /**
     * Retrieve data in array format from the csv file
     *
     * @param string $file
     * @return array
     */
    public function retrieveCsvData(string $file): array;

    /**
     * Retrieve data in array format from the json file
     *
     * @param string $file
     * @return array
     */
    public function retrieveJsonData(string $file): array;
}
