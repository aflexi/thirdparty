<?php

/*
 * LICENSE AGREEMENT
 * -----------------------------------------------------------------------------
 * Copyright (c) 2010 Aflexi Sdn. Bhd.
 * 
 * This file is part of Aflexi_Common.
 * 
 * Aflexi_Common is published under the terms of the Open Software License 
 * ("OSL") v. 3.0. For the full copyright and license information, please view 
 * the LICENSE file that was distributed with this source code.
 * -----------------------------------------------------------------------------
 */
 
# namespace Aflexi\Common\Model;

/**
 * Thrown if a provided Entity doesn't have a correct reference ID or unique
 * key.
 *
 * @since 2.3
 * @version 2.3.20100505
 * @author yclian
 */
class Aflexi_Common_Model_InvalidEntityReferenceException extends InvalidArgumentException{

	function __construct($type = NULL, $message = '', $code = Aflexi_Common_Exception::CLIENT_INVALID_ARGUMENT){
		if(empty($message)){
			parent::__construct(sprintf("Entity%s does not have a valid key", empty($type) ? '' : " '$type'"), $code);
		} else{
			parent::__construct($message, $code);
		}
	}

	/**
	 * Given an Entity (currently supporting only struct), seek for the 'id'
	 * key and evaluate its value.
	 *
	 * @param $entity
	 * @return bool
	 */
	static function isValid(&$entity){

		if(is_array($entity)){
			
			if(isset($entity['id'])){

				if(is_numeric($entity['id'])){
					if($entity['id'] <= 0){
						return FALSE;
					} else{
						return TRUE;
					}
				}
				
				if(is_string($entity['id'])){
					if(empty($entity['id'])){
						return FALSE;
					} else{
						return TRUE;
					}
				}
			}
		}

		// NOTE [yclian 20100505] Instead of returning TRUE, we throw an 
		// exception here. When we hit into this, we will think about how we
		// shall improve the support.
		throw new Aflexi_Common_Lang_UnsupportedOperationException();
	}
}

?>