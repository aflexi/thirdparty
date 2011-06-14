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
 * This class contains the common interfaces for all CRUD operations,
 * similar to the ones on Web Service.
 *
 * @author yclian
 * @since 2.3
 * @version 2.3.20100409
 */
interface Aflexi_Common_Model_CrudDao extends Aflexi_Common_Model_Dao{

    /**
     * Create an entity with the given object.
     *
     * @param $obj
     *          An array, of the entity.
     * @return Unique value of the created entity.
     */
    function create($obj);

    /**
     * Update an entity with the given object.
     *
     * @param $obj
     *          An array, of the entity.
     * @return The updated entity object.
     */
    function update($obj);

    /**
     * Delete an entity with the given unique id.
     *
     * @param $id
     *          Unique identifier of the entity.
     * @return true if successful.
     */
    function delete($id);

    /**
     * Get an entity matching by the unique id.
     * @param $id
     *          Unique identifier of the entity.
     * @return The matched entity object.
     */
    function get($id);

    /**
     * Get a collection of entities.
     * 
     * NOTE [yclian 20100412] We use find() but not list() here as 'list' reserved by PHP. :-(
     *
     * @param array $params
     *          A key-value pair array, based on the struct of the entity, for the criterias.
     * @param array $options
     *          An array with the listing options, such as pagination, maximum results, etc.
     *          Will be replaced by application's default if not specified.
     * @return An array of entity objects.
     */
    function find(array $params, $options = array());
}

?>