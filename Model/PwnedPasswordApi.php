<?php
/**
 * See LICENSE.txt for license details.
 */

namespace MageWare\Hibp\Model;

class PwnedPasswordApi implements PwnedPasswordInterface
{
    const ENDPOINT = 'https://haveibeenpwned.com/api/v2/pwnedpassword';
    const USER_AGENT = 'mageware/magento2-hibp';

    /**
     * @var \Magento\Framework\HTTP\Adapter\CurlFactory
     */
    protected $curlFactory;

    /**
     * @param \Magento\Framework\HTTP\Adapter\CurlFactory $curlFactory
     */
    public function __construct(
        \Magento\Framework\HTTP\Adapter\CurlFactory $curlFactory
    ) {
        $this->curlFactory = $curlFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function isPwned($password)
    {
        $httpAdapter = $this->curlFactory->create();
        $postbackQuery = http_build_query(['password' => sha1($password)]);
        $httpAdapter->write(\Zend_Http_Client::POST, self::ENDPOINT, '1.1', ['User-Agent: ' . self::USER_AGENT], $postbackQuery);

        $postbackResult = $httpAdapter->read();
        $responseCode = \Zend_Http_Response::extractCode($postbackResult);

        return $responseCode === 200;
    }
}
