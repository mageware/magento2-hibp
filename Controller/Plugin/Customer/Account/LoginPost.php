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
     * @var \MageWare\Hibp\Model\PawnedPasswordInterface
     */
    private $pawnedPasswordService;

    /**
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Customer\Model\Session $session
     * @param \MageWare\Hibp\Model\PawnedPasswordInterface $pawnedPasswordService
     */
    public function __construct(
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Customer\Model\Session $session,
        \MageWare\Hibp\Model\PawnedPasswordInterface $pawnedPasswordService
    ) {
        $this->messageManager = $messageManager;
        $this->scopeConfig = $scopeConfig;
        $this->session = $session;
        $this->pawnedPasswordService = $pawnedPasswordService;
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
        $checkPawnedPassword = false;
        $login = $request->getPost('login');

        if (!$this->session->isLoggedIn()) {
            $checkPawnedPassword = $this->scopeConfig->isSetFlag('mageware_hibp/storefront/check_pawned_password');
        }

        $result = $proceed($request);

        if ($this->session->isLoggedIn()) {
            if ($checkPawnedPassword) {
                if ($this->pawnedPasswordService->isPawned($login['password'])) {
                    $this->messageManager->addWarning(
                        __('Your password is pawned. Learn more <a href="https://haveibeenpwned.com/Passwords">here</a>.')
                    );
                }
            }
        }

        return $result;
    }
}
