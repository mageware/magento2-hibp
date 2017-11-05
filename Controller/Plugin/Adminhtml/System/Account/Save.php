<?php
/**
 * See LICENSE.txt for license details.
 */

namespace MageWare\Hibp\Controller\Plugin\Adminhtml\System\Account;

class Save
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
     * @var \MageWare\Hibp\Model\UserManagement
     */
    private $userManagement;

    /**
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \MageWare\Hibp\Model\UserManagement $userManagement
     */
    public function __construct(
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \MageWare\Hibp\Model\UserManagement $userManagement
    ) {
        $this->messageManager = $messageManager;
        $this->scopeConfig = $scopeConfig;
        $this->userManagement = $userManagement;
    }

    /**
     * @param \Magento\Backend\Controller\Adminhtml\System\Account\Save $subject
     * @param \Closure $proceed
     * @param \Magento\Framework\App\RequestInterface $request
     *
     * @return mixed
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundDispatch(
        \Magento\Backend\Controller\Adminhtml\System\Account\Save $subject,
        \Closure $proceed,
        \Magento\Framework\App\RequestInterface $request
    ) {
        $checkPawnedPassword = $this->scopeConfig->isSetFlag('mageware_hibp/admin/check_pawned_password');
        $requiredUnpawnedPassword = $this->scopeConfig->isSetFlag('mageware_hibp/admin/required_unpawned_password');

        $currentCheckPawnedPassword = $this->userManagement->checkPawnedPassword($checkPawnedPassword);
        $currentRequiredUnpawnedPassword = $this->userManagement->requiredUnpawnedPassword($requiredUnpawnedPassword);

        $result = $proceed($request);

        $this->userManagement->requiredUnpawnedPassword($currentRequiredUnpawnedPassword);
        $this->userManagement->checkPawnedPassword($currentCheckPawnedPassword);

        if (!$requiredUnpawnedPassword && $this->userManagement->isPawnedPassword()) {
            $this->messageManager->addWarning(
                __('Your password is pawned. Learn more <a href="https://haveibeenpwned.com/Passwords">here</a>.')
            );
        }

        return $result;
    }
}
