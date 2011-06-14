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
 
# namespace Aflexi\Common\DAO;

/**
 * Data Access Object, a common design pattern. DAO  in our term can also be
 * an RPC call.
 * 
 * @see <a href="http://en.wikipedia.org/wiki/Data_access_object">Data access object (Wikipedia)</a>
 * @author yclian
 * @since 2.3
 * @version 2.3.20100409
 */
interface Aflexi_Common_Dao_Dao{

    /**
     * Execute a callback. Parameters depend on the underlying Dao implementation.
     * 
     * NOTE [yclian 20100511] Unlike the LegacyDao, thish as an argument less, 
     * which is still compatible. Introducing $repository and $command is not
     * though.
     * 
     * @param $params Parameters required for making a call to the underlying
     * persistence.
     * @param $callback
     * @return Result of the callback, usually data. NULL otherwise.
     */
    function execute(array $params, $callback = NULL);
}

?>