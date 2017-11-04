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
     * @param \MageWare\Hibp\Model\PawnedPasswordInterface $pawnedPasswordService
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
        $checkPawnedPassword = $this->scopeConfig->isSetFlag('mageware_hibp/storefront/check_pawned_password');
        $requiredUnpawnedPassword = $this->scopeConfig->isSetFlag('mageware_hibp/storefront/required_unpawned_password');

        $currentCheckPawnedPassword = $this->accountManagement->checkPawnedPassword($checkPawnedPassword);
        $currentRequiredUnpawnedPassword = $this->accountManagement->requiredUnpawnedPassword($requiredUnpawnedPassword);

        $result = $proceed($request);

        $this->accountManagement->requiredUnpawnedPassword($currentRequiredUnpawnedPassword);
        $this->accountManagement->checkPawnedPassword($currentCheckPawnedPassword);

        if (!$requiredUnpawnedPassword && $this->accountManagement->isPawnedPassword()) {
            $this->messageManager->addWarning(
                __('Your password is pawned. Learn more <a href="https://haveibeenpwned.com/Passwords">here</a>.')
            );
        }

        return $result;
    }
}
