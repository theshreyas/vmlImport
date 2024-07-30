<?php
declare(strict_types=1);

namespace Vml\Import\Console\Command;

use Psr\Log\LoggerInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterfaceFactory;
use Magento\Framework\Console\Cli;
use Magento\Store\Model\StoreManagerInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Vml\Import\Model\FileImporter;

class CustomerImport extends AbstractImport
{
    /**
     * CustomerImport constructor
     *
     * @param CustomerInterfaceFactory $customerInterfaceFactory
     * @param CustomerRepositoryInterface $customerRepository
     * @param FileImporter $fileImporter
     * @param LoggerInterface $logger
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        private CustomerInterfaceFactory $customerInterfaceFactory,
        private CustomerRepositoryInterface $customerRepository,
        private FileImporter $fileImporter,
        private LoggerInterface $logger,
        private StoreManagerInterface $storeManager
    ) {
        parent::__construct($fileImporter, $logger);
    }

    /**
     * @inheritdoc
     */
    protected function configure(): void
    {
        $this->setName("customer:import")
            ->setDescription("Vml Customer Import");
        parent::configure();
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $importData = $this->checkInput($input, $output);
        if (!$importData) {
            return Cli::RETURN_FAILURE;
        }
        try {
            $this->saveCustomerData($importData);
        } catch (\Exception $e) {
            return $this->handleError($output, $e);
        }
        $output->writeln(sprintf("All %d customers are imported", count($importData)));
        return Cli::RETURN_SUCCESS;
    }

    /**
     * Save customers
     *
     * @param array $customers
     */
    public function saveCustomerData(array $customers): void
    {
        $store = $this->storeManager->getStore();
        $storeId = $store !== null ? $store->getId() : null;
        $websiteId = $storeId !== null ? $this->storeManager->getStore($storeId)->getWebsiteId() : null;

        foreach ($customers as $data) {
            $customer = $this->customerInterfaceFactory->create();
            $customer->setFirstname($data['fname'] ?? '');
            $customer->setLastname($data['lname'] ?? '');
            $customer->setEmail($data['emailaddress'] ?? '');

            if ($websiteId !== null) {
                $customer->setWebsiteId($websiteId);
            }
            $this->customerRepository->save($customer);
        }
    }
}
