<?php
/**
 * See LICENSE.txt for license details.
 */

namespace MageWare\Hibp\Controller\Plugin\Adminhtml\Auth;

class ResetPasswordPost
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
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \MageWare\Hibp\Model\PwnedPasswordInterface $pwnedPasswordService
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \MageWare\Hibp\Model\UserManagement $userManagement
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->userManagement = $userManagement;
    }

    /**
     * @param \Magento\User\Controller\Adminhtml\Auth\ResetPasswordPost $subject
     * @param \Closure $proceed
     * @param \Magento\Framework\App\RequestInterface $request
     *
     * @return mixed
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundDispatch(
        \Magento\User\Controller\Adminhtml\Auth\ResetPasswordPost $subject,
        \Closure $proceed,
        \Magento\Framework\App\RequestInterface $request
    ) {
        $requiredUnpwnedPassword = $this->scopeConfig->isSetFlag('mageware_hibp/admin/required_unpwned_password');

        $currentRequiredUnpwnedPassword = $this->userManagement->requiredUnpwnedPassword($requiredUnpwnedPassword);

        $result = $proceed($request);

        $this->userManagement->requiredUnpwnedPassword($currentRequiredUnpwnedPassword);

        return $result;
    }
}
