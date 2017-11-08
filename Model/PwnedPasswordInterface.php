<?php
/**
 * See LICENSE.txt for license details.
 */

namespace MageWare\Hibp\Model;

interface PwnedPasswordInterface
{
    /**
     * @param string $password
     * @return bool
     */
    public function isPwned($password);
}