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
        $checkPwnedPassword = $this->scopeConfig->isSetFlag('mageware_hibp/admin/check_pwned_password');
        $requiredUnpwnedPassword = $this->scopeConfig->isSetFlag('mageware_hibp/admin/required_unpwned_password');

        $currentCheckPwnedPassword = $this->userManagement->checkPwnedPassword($checkPwnedPassword);
        $currentRequiredUnpwnedPassword = $this->userManagement->requiredUnpwnedPassword($requiredUnpwnedPassword);

        $result = $proceed($request);

        $this->userManagement->requiredUnpwnedPassword($currentRequiredUnpwnedPassword);
        $this->userManagement->checkPwnedPassword($currentCheckPwnedPassword);

        if (!$requiredUnpwnedPassword && $this->userManagement->isPwnedPassword()) {
            $this->messageManager->addWarning(
                __('Your password is pwned. Learn more <a href="https://haveibeenpwned.com/Passwords">here</a>.')
            );
        }

        return $result;
    }
}
