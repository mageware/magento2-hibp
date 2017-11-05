<?php
/**
 * See LICENSE.txt for license details.
 */

namespace MageWare\Hibp\Controller\Plugin\Adminhtml\User;

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
     * @param \MageWare\Hibp\Controller\Plugin\Adminhtml\User\Save $subject
     * @param \Closure $proceed
     * @param \Magento\Framework\App\RequestInterface $request
     *
     * @return mixed
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundDispatch(
        \Magento\User\Controller\Adminhtml\User\Save $subject,
        \Closure $proceed,
        \Magento\Framework\App\RequestInterface $request
    ) {
        $checkPawnedPassword = $this->scopeConfig->isSetFlag('mageware_hibp/admin/check_pawned_password');
        $requiredUnpawnedPassword = $this->scopeConfig->isSetFlag('mageware_hibp/admin/required_unpawned_password');

        $result = $proceed($request);

        $data = $request->getPostValue();

        if (!$requiredUnpawnedPassword && $checkPawnedPassword && $this->userManagement->isPawnedPassword($data['password'])) {
            $this->messageManager->addWarning(
                __('Given password is pawned. Learn more <a href="https://haveibeenpwned.com/Passwords">here</a>.')
            );
        }

        return $result;
    }
}
