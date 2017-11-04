<?php
/**
 * See LICENSE.txt for license details.
 */

namespace MageWare\Hibp\Model;

class UserManagement
{
    /**
     * @var PawnedPasswordInterface
     */
    private $pawnedPasswordService;

    /**
     * @var bool
     */
    private $checkPawnedPassword;

    /**
     * @var bool
     */
    private $requiredUnpawnedPassword;

    /**
     * @var bool
     */
    private $pawnedPassword;

    /**
     * @param PawnedPasswordInterface $pawnedPasswordService
     */
    public function __construct(PawnedPasswordInterface $pawnedPasswordService)
    {
        $this->pawnedPasswordService = $pawnedPasswordService;
        $this->checkPawnedPassword = false;
        $this->requiredUnpawnedPassword = false;
        $this->pawnedPassword = false;
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
     * @param string|null $password
     * @return bool
     */
    public function isPawnedPassword($password = null)
    {
        if (null !== $password) {
            $this->pawnedPassword = $this->pawnedPasswordService->isPawned($password);
        }
        return $this->pawnedPassword;
    }
}
