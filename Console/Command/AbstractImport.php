<?php
declare(strict_types=1);

namespace Vml\Import\Console\Command;

use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Vml\Import\Api\Data\ImportInterface;
use Vml\Import\Model\FileImporter;

abstract class AbstractImport extends Command
{
    /**
     * AbstractImport constructor
     *
     * @param FileImporter $fileImporter
     * @param LoggerInterface $logger
     */
    public function __construct(
        private FileImporter $fileImporter,
        private LoggerInterface $logger
    ) {
        parent::__construct();
    }

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this->setDefinition([
                new InputArgument(ImportInterface::PROFILE_NAME, InputArgument::REQUIRED, "Profile Name"),
                new InputArgument(ImportInterface::FILE_PATH, InputArgument::REQUIRED, "File Path")
            ]);
    }

    /**
     * Check input arguments
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return array|false
     */
    protected function checkInput(InputInterface $input, OutputInterface $output): array|false
    {
        $profileType = $input->getArgument(ImportInterface::PROFILE_NAME);
        $filePath = $input->getArgument(ImportInterface::FILE_PATH);
        $output->writeln("Processing...");
        
        if (in_array($profileType, ImportInterface::PROFILE_TYPES)) {
            return $this->fileImporter->readData($input);
        } else {
            $output->writeln("Profile type is invalid");
            return false;
        }
    }

    /**
     * Common function to handle error
     *
     * @param OutputInterface $output
     * @param \Exception $e
     * @return int
     */
    protected function handleError(OutputInterface $output, \Exception $e): int
    {

        $this->logger->critical($e->getMessage(), ['exception' => $e]);
        $output->writeln('<error>' . $e->getMessage() . '</error>');
        if ($output->getVerbosity() >= OutputInterface::VERBOSITY_VERBOSE) {
            $output->writeln($e->getTraceAsString());
        }
        return \Magento\Framework\Console\Cli::RETURN_FAILURE;
    }
}
