# HIBP

Magento 2.x CE integration module for HIBP service.

## Installation Instructions

1. Use composer to download the module:

   ```
   composer require mageware/magento2-hibp
   ```

2. Enable downloaded module:

   ```
   php bin/magento module:enable MageWare_Common MageWare_Hibp
   ```

3. Upgrade your database:

   ```
   php bin/magento setup:upgrade
   ```
