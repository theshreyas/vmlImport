VmlImport

Tested on PHP 8.3.9, Magento 2.4.7-p1

1. Installation: 
      - Please execute the following two commands in order to set Composer to accept dev releases
        - `composer config minimum-stability dev`
        - `composer config prefer-stable true`
      - For the installation of the module, please run
        -  `composer require theshreyas/vmlimport`
      - Once installed, please run `php bin/magento setup:di:compile && php bin/magento setup:uphgrade && php bin/magento module:enable Vml_Import`
      - Flush the cache by running following command
        - `php bin/magento cache:flush`

2. Usage
 - Please run the following commands to import data from given sample files  
 - For JSON Profile 
   - `php bin/magento customer:import sample-json var/import/sample.json`
  - For CSV Profile
    -`php bin/magento customer:import sample-csv var/import/sample.csv`
