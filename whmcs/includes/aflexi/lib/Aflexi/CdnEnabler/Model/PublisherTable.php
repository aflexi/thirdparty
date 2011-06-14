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
 /**
 * 
 * Model to deal with publisher table
 * @author yingfan
 * @since V2.8, 20100907
 *
 */
class Aflexi_CdnEnabler_Model_PublisherTable extends Aflexi_CdnEnabler_Model_Table {    
    public function __construct() {
        $this->setTableName('mod_aflexicdn_publisher');
        parent::__construct();
    }
}
?>
