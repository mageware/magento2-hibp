<?php
/**
 * See LICENSE.txt for license details.
 */

namespace MageWare\Hibp\Model;

use Magento\Customer\Model\CustomerFactory;
use Magento\Customer\Model\CustomerRegistry;
use Magento\Customer\Api\AddressRepositoryInterface;
use Magento\Customer\Api\CustomerMetadataInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\ValidationResultsInterfaceFactory;
use Magento\Customer\Helper\View as CustomerViewHelper;
use Magento\Customer\Model\Config\Share as ConfigShare;
use Magento\Customer\Model\Customer as CustomerModel;
use Magento\Customer\Model\Metadata\Validator;
use Magento\Framework\Api\ExtensibleDataObjectConverter;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Encryption\EncryptorInterface as Encryptor;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Exception\InputException;
use Magento\Framework\DataObjectFactory as ObjectFactory;
use Magento\Framework\Registry;
use Magento\Store\Model\ScopeInterface;
use Psr\Log\LoggerInterface as PsrLogger;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Math\Random;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\Stdlib\DateTime;
use Magento\Framework\Stdlib\StringUtils as StringHelper;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Customer\Model\Customer\CredentialsValidator;
use MageWare\Hibp\Model\PawnedPasswordInterface;

class AccountManagement extends \Magento\Customer\Model\AccountManagement
{
    /**
     * @var PawnedPasswordInterface
     */
    private $pawnedPasswordService;

    /**
     * @var bool
     */
    private $pawnedPassword;

    /**
     * @var bool
     */
    private $checkPawnedPassword;

    /**
     * @var bool
     */
    private $requiredUnpawnedPassword;

    /**
     * @param CustomerFactory $customerFactory
     * @param ManagerInterface $eventManager
     * @param StoreManagerInterface $storeManager
     * @param Random $mathRandom
     * @param Validator $validator
     * @param ValidationResultsInterfaceFactory $validationResultsDataFactory
     * @param AddressRepositoryInterface $addressRepository
     * @param CustomerMetadataInterface $customerMetadataService
     * @param CustomerRegistry $customerRegistry
     * @param PsrLogger $logger
     * @param Encryptor $encryptor
     * @param ConfigShare $configShare
     * @param StringHelper $stringHelper
     * @param CustomerRepositoryInterface $customerRepository
     * @param ScopeConfigInterface $scopeConfig
     * @param TransportBuilder $transportBuilder
     * @param DataObjectProcessor $dataProcessor
     * @param Registry $registry
     * @param CustomerViewHelper $customerViewHelper
     * @param DateTime $dateTime
     * @param CustomerModel $customerModel
     * @param ObjectFactory $objectFactory
     * @param ExtensibleDataObjectConverter $extensibleDataObjectConverter
     * @param CredentialsValidator|null $credentialsValidator
     * @param PawnedPasswordInterface $pawnedPasswordService
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        CustomerFactory $customerFactory,
        ManagerInterface $eventManager,
        StoreManagerInterface $storeManager,
        Random $mathRandom,
        Validator $validator,
        ValidationResultsInterfaceFactory $validationResultsDataFactory,
        AddressRepositoryInterface $addressRepository,
        CustomerMetadataInterface $customerMetadataService,
        CustomerRegistry $customerRegistry,
        PsrLogger $logger,
        Encryptor $encryptor,
        ConfigShare $configShare,
        StringHelper $stringHelper,
        CustomerRepositoryInterface $customerRepository,
        ScopeConfigInterface $scopeConfig,
        TransportBuilder $transportBuilder,
        DataObjectProcessor $dataProcessor,
        Registry $registry,
        CustomerViewHelper $customerViewHelper,
        DateTime $dateTime,
        CustomerModel $customerModel,
        ObjectFactory $objectFactory,
        ExtensibleDataObjectConverter $extensibleDataObjectConverter,
        CredentialsValidator $credentialsValidator = null,
        PawnedPasswordInterface $pawnedPasswordService
    ) {
        parent::__construct(
            $customerFactory,
            $eventManager,
            $storeManager,
            $mathRandom,
            $validator,
            $validationResultsDataFactory,
            $addressRepository,
            $customerMetadataService,
            $customerRegistry,
            $logger,
            $encryptor,
            $configShare,
            $stringHelper,
            $customerRepository,
            $scopeConfig,
            $transportBuilder,
            $dataProcessor,
            $registry,
            $customerViewHelper,
            $dateTime,
            $customerModel,
            $objectFactory,
            $extensibleDataObjectConverter,
            $credentialsValidator
        );

        $this->pawnedPasswordService = $pawnedPasswordService;
        $this->pawnedPassword = false;
        $this->checkPawnedPassword = false;
        $this->requiredUnpawnedPassword = false;
    }

    /**
     * @param bool|null $flag
     * @return bool
     */
    public function checkPawnedPassword($flag = null)
    {
        $value = $this->checkPawnedPassword;

        if (null !== $flag) {
            $this->checkPawnedPassword = (bool)$flag;
        }

        return $value;
    }

    /**
     * @param bool|null $flag
     * @return bool
     */
    public function requiredUnpawnedPassword($flag = null)
    {
        $value = $this->requiredUnpawnedPassword;

        if (null !== $flag) {
            $this->requiredUnpawnedPassword = (bool)$flag;
        }

        return $value;
    }

    /**
     * @return bool
     */
    public function isPawnedPassword()
    {
        return $this->pawnedPassword;
    }

    /**
     * {@inheritdoc}
     */
    protected function checkPasswordStrength($password)
    {
        parent::checkPasswordStrength($password);

        if ($this->checkPawnedPassword() || $this->requiredUnpawnedPassword()) {
            $this->pawnedPassword = $this->pawnedPasswordService->isPawned($password);
            if ($this->requiredUnpawnedPassword() && $this->pawnedPassword) {
                throw new InputException(__('Given password is pawned, please enter different password. Learn more <a href="https://haveibeenpwned.com/Passwords">here</a>.'));
            }
        }
    }
}
