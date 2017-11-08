<?php
/**
 * See LICENSE.txt for license details.
 */

namespace MageWare\Hibp\Controller\Plugin\Customer\Account;

class ResetPasswordPost
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var \Magento\Customer\Api\AccountManagementInterface
     */
    private $accountManagement;

    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \MageWare\Hibp\Model\PwnedPasswordInterface $pwnedPasswordService
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Customer\Api\AccountManagementInterface $accountManagement
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->accountManagement = $accountManagement;
    }

    /**
     * @param \Magento\Customer\Controller\Account\ResetPasswordPost $subject
     * @param \Closure $proceed
     * @param \Magento\Framework\App\RequestInterface $request
     *
     * @return mixed
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundDispatch(
        \Magento\Customer\Controller\Account\ResetPasswordPost $subject,
        \Closure $proceed,
        \Magento\Framework\App\RequestInterface $request
    ) {
        $requiredUnpwnedPassword = $this->scopeConfig->isSetFlag('mageware_hibp/storefront/required_unpwned_password');

        $currentRequiredUnpwnedPassword = $this->accountManagement->requiredUnpwnedPassword($requiredUnpwnedPassword);

        $result = $proceed($request);

        $this->accountManagement->requiredUnpwnedPassword($currentRequiredUnpwnedPassword);

        return $result;
    }
}
