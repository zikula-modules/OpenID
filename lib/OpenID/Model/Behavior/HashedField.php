<?php
/**
 * Copyright Zikula Foundation 2011 - Zikula Application Framework
 *
 * This work is contributed to the Zikula Foundation under one or more
 * Contributor Agreements and licensed to You under the following license:
 *
 * @license GNU/LGPv3 (or at your option any later version).
 * @package OpenID
 *
 * Please see the NOTICE file distributed with this source code for further
 * information regarding copyright and licensing.
 */

/**
 * OpenID_HashedKey behavior.
 *
 */
class OpenID_Model_Behavior_HashedField extends Doctrine_Template
{
    protected function _hashValue($value)
    {
        return hash('sha256', $value);
    }

    protected function _hashFieldInto($field, $hashedField)
    {
        $invoker = $this->getInvoker();
        $invoker->$hashedField = $this->_hashValue($invoker->$field);
    }

    public function hashFieldInto($field, $hashedField)
    {
        return $this->_hashFieldInto($field, $hashedField);
    }

    public function hashFieldIntoTableProxy($field, $hashedField)
    {
        return $this->_hashFieldInto($field, $hashedField);
    }

    public function hashValue($value)
    {
        return $this->_hashValue($value);
    }

    public function hashValueTableProxy($value)
    {
        return $this->_hashValue($value);
    }

}