<?php
/**
 * See LICENSE.txt for license details.
 */

namespace MageWare\Hibp\Model;

class UserManagement
{
    /**
     * @var PwnedPasswordInterface
     */
    private $pwnedPasswordService;

    /**
     * @var bool
     */
    private $checkPwnedPassword;

    /**
     * @var bool
     */
    private $requiredUnpwnedPassword;

    /**
     * @var bool
     */
    private $pwnedPassword;

    /**
     * @param PwnedPasswordInterface $pwnedPasswordService
     */
    public function __construct(PwnedPasswordInterface $pwnedPasswordService)
    {
        $this->pwnedPasswordService = $pwnedPasswordService;
        $this->checkPwnedPassword = false;
        $this->requiredUnpwnedPassword = false;
        $this->pwnedPassword = false;
    }

    /**
     * @param bool|null $flag
     * @return bool
     */
    public function checkPwnedPassword($flag = null)
    {
        $value = $this->checkPwnedPassword;

        if (null !== $flag) {
            $this->checkPwnedPassword = (bool)$flag;
        }

        return $value;
    }

    /**
     * @param bool|null $flag
     * @return bool
     */
    public function requiredUnpwnedPassword($flag = null)
    {
        $value = $this->requiredUnpwnedPassword;

        if (null !== $flag) {
            $this->requiredUnpwnedPassword = (bool)$flag;
        }

        return $value;
    }

    /**
     * @param string|null $password
     * @return bool
     */
    public function isPwnedPassword($password = null)
    {
        if (null !== $password) {
            $this->pwnedPassword = $this->pwnedPasswordService->isPwned($password);
        }
        return $this->pwnedPassword;
    }
}
