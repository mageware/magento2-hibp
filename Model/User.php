<?php
/**
 * See LICENSE.txt for license details.
 */

namespace MageWare\Hibp\Model;

class User extends \Magento\User\Model\User
{
    /**
     * @var UserManagement
     */
    private $userManagement;

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\User\Helper\Data $userData
     * @param \Magento\Backend\App\ConfigInterface $config
     * @param \Magento\Framework\Validator\DataObjectFactory $validatorObjectFactory
     * @param \Magento\Authorization\Model\RoleFactory $roleFactory
     * @param \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
     * @param \Magento\Framework\Encryption\EncryptorInterface $encryptor
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\User\Model\UserValidationRules $validationRules
     * @param PwnedPasswordInterface $pwnedPasswordService
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb $resourceCollection
     * @param array $data
     * @param \Magento\Framework\Serialize\Serializer\Json $serializer
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\User\Helper\Data $userData,
        \Magento\Backend\App\ConfigInterface $config,
        \Magento\Framework\Validator\DataObjectFactory $validatorObjectFactory,
        \Magento\Authorization\Model\RoleFactory $roleFactory,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Framework\Encryption\EncryptorInterface $encryptor,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\User\Model\UserValidationRules $validationRules,
        UserManagement $userManagement,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = [],
        \Magento\Framework\Serialize\Serializer\Json $serializer = null
    ) {
        parent::__construct(
            $context,
            $registry,
            $userData,
            $config,
            $validatorObjectFactory,
            $roleFactory,
            $transportBuilder,
            $encryptor,
            $storeManager,
            $validationRules,
            $resource,
            $resourceCollection,
            $data,
            $serializer
        );

        $this->userManagement = $userManagement;
    }

    /**
     * {@inheritdoc}
     */
    protected function validatePasswordChange()
    {
        $result = parent::validatePasswordChange();

        if (true === $result) {
            if ($this->userManagement->checkPwnedPassword() || $this->userManagement->requiredUnpwnedPassword()) {
                if ($this->userManagement->isPwnedPassword($this->getPassword()) && $this->userManagement->requiredUnpwnedPassword()) {
                    return [__('Given password is pwned, please enter different password. Learn more <a href="https://haveibeenpwned.com/Passwords">here</a>.')];
                }
            }
        }

        return $result;
    }
}
