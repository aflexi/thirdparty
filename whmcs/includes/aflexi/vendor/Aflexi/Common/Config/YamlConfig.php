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
 
# namespace Aflexi\CdnEnabler;

/**
 * Aflexi_Common_Config_Config with YAML support.
 * 
 * @author yclian
 * @since 2.8
 * @version 2.8.20100908
 */
class Aflexi_Common_Config_YamlConfig extends Aflexi_Common_Config_AbstractConfig{
    
    protected function doRead($source){
        return Aflexi_Common_Yaml_YamlUtils::read($source);
    }
    
    protected function doWrite($destination, array $data){
        return Aflexi_Common_Yaml_YamlUtils::write($destination, $data);
    }
}

?>