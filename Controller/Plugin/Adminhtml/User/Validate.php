<?php
/**
 * See LICENSE.txt for license details.
 */

namespace MageWare\Hibp\Controller\Plugin\Adminhtml\User;

class Validate
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var \MageWare\Hibp\Model\UserManagement
     */
    private $userManagement;

    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
     * @param \MageWare\Hibp\Model\UserManagement $userManagement
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \MageWare\Hibp\Model\UserManagement $userManagement
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->userManagement = $userManagement;
    }

    /**
     * @param \MageWare\Hibp\Controller\Plugin\Adminhtml\User\Validate $subject
     * @param \Closure $proceed
     * @param \Magento\Framework\App\RequestInterface $request
     *
     * @return mixed
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundDispatch(
        \Magento\User\Controller\Adminhtml\User\Validate $subject,
        \Closure $proceed,
        \Magento\Framework\App\RequestInterface $request
    ) {
        $requiredUnpawnedPassword = $this->scopeConfig->isSetFlag('mageware_hibp/admin/required_unpawned_password');

        $currentRequiredUnpawnedPassword = $this->userManagement->requiredUnpawnedPassword($requiredUnpawnedPassword);

        $result = $proceed($request);

        $this->userManagement->requiredUnpawnedPassword($currentRequiredUnpawnedPassword);

        return $result;
    }
}
