<?php
/**
 * See LICENSE.txt for license details.
 */

namespace MageWare\Hibp\Controller\Plugin\Customer\Account;

class LoginPost
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
     * @var \Magento\Customer\Model\Session $session
     */
    private $session;

    /**
     * @var \MageWare\Hibp\Model\PwnedPasswordInterface
     */
    private $pwnedPasswordService;

    /**
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Customer\Model\Session $session
     * @param \MageWare\Hibp\Model\PwnedPasswordInterface $pwnedPasswordService
     */
    public function __construct(
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Customer\Model\Session $session,
        \MageWare\Hibp\Model\PwnedPasswordInterface $pwnedPasswordService
    ) {
        $this->messageManager = $messageManager;
        $this->scopeConfig = $scopeConfig;
        $this->session = $session;
        $this->pwnedPasswordService = $pwnedPasswordService;
    }

    /**
     * @param \Magento\Customer\Controller\Account\LoginPost $subject
     * @param \Closure $proceed
     * @param \Magento\Framework\App\RequestInterface $request
     *
     * @return mixed
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundDispatch(
        \Magento\Customer\Controller\Account\LoginPost $subject,
        \Closure $proceed,
        \Magento\Framework\App\RequestInterface $request
    ) {
        $checkPwnedPassword = false;
        $login = $request->getPost('login');

        if (!$this->session->isLoggedIn()) {
            $checkPwnedPassword = $this->scopeConfig->isSetFlag('mageware_hibp/storefront/check_pwned_password');
        }

        $result = $proceed($request);

        if ($this->session->isLoggedIn()) {
            if ($checkPwnedPassword) {
                if ($this->pwnedPasswordService->isPwned($login['password'])) {
                    $this->messageManager->addWarning(
                        __('Your password is pwned. Learn more <a href="https://haveibeenpwned.com/Passwords">here</a>.')
                    );
                }
            }
        }

        return $result;
    }
}
