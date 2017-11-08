<?php
/**
 * See LICENSE.txt for license details.
 */

namespace MageWare\Hibp\Controller\Plugin\Customer\Account;

class CreatePost
{
    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    private $messageManager;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var \Magento\Customer\Api\AccountManagementInterface
     */
    private $accountManagement;

    /**
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \MageWare\Hibp\Model\PwnedPasswordInterface $pwnedPasswordService
     */
    public function __construct(
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Customer\Api\AccountManagementInterface $accountManagement
    ) {
        $this->messageManager = $messageManager;
        $this->scopeConfig = $scopeConfig;
        $this->accountManagement = $accountManagement;
    }

    /**
     * @param \Magento\Customer\Controller\Account\CreatePost $subject
     * @param \Closure $proceed
     * @param \Magento\Framework\App\RequestInterface $request
     *
     * @return mixed
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundDispatch(
        \Magento\Customer\Controller\Account\CreatePost $subject,
        \Closure $proceed,
        \Magento\Framework\App\RequestInterface $request
    ) {
        $checkPwnedPassword = $this->scopeConfig->isSetFlag('mageware_hibp/storefront/check_pwned_password');
        $requiredUnpwnedPassword = $this->scopeConfig->isSetFlag('mageware_hibp/storefront/required_unpwned_password');

        $currentCheckPwnedPassword = $this->accountManagement->checkPwnedPassword($checkPwnedPassword);
        $currentRequiredUnpwnedPassword = $this->accountManagement->requiredUnpwnedPassword($requiredUnpwnedPassword);

        $result = $proceed($request);

        $this->accountManagement->requiredUnpwnedPassword($currentRequiredUnpwnedPassword);
        $this->accountManagement->checkPwnedPassword($currentCheckPwnedPassword);

        if (!$requiredUnpwnedPassword && $this->accountManagement->isPwnedPassword()) {
            $this->messageManager->addWarning(
                __('Your password is pwned. Learn more <a href="https://haveibeenpwned.com/Passwords">here</a>.')
            );
        }

        return $result;
    }
}
