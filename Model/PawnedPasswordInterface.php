<?php
/**
 * See LICENSE.txt for license details.
 */

namespace MageWare\Hibp\Model;

interface PawnedPasswordInterface
{
    /**
     * @param string $password
     * @return bool
     */
    public function isPawned($password);
}