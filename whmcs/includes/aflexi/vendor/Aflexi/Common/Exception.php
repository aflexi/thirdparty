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
 
# namespace Aflexi\Common;

/**
 * Root exception, defined under the domain of Aflexi, for the domain.
 * 
 * DEVELOPER NOTE: Run tools/stripexception.sh and manually update this file.
 *
 * @author yclian
 * @since 1.0
 * @version 2.5.20100628
 */
class Aflexi_Common_Exception extends Exception{

    // 0 -----------------------------------------------------------------------

    /**
     * General fault code, matters such as availability, communication, or protocol.
     * 
     * @since 1.5
     */
    const BASE_GENERAL_FAULT_CODE = 0;

    const GENERAL_UNKNOWN_ERROR = 1;

    /**
     * Unsupported service type. As of 1.5, we support only XML-RPC and JSON. Refer back to the remote module or its documentation for instructions of how you
     * can access to these services.
     * 
     * @since 1.5
     */
    const GENERAL_UNSUPPORTED_SERVICE_TYPE = 2;

    /**
     * The requested service is currently unavailable, could be due to maintenance. Not used but reserved.
     * 
     * @since 1.5
     */
    const GENERAL_SERVICE_UNAVAILABLE = 3;

    /**
     * Wrong service or method provided.
     * 
     * @see RemoteServiceNotFoundException
     * @see RemoteMethodNotFoundException
     * @since 1.5
     */
    const GENERAL_UNKNOWN_SERVICE_OR_METHOD = 4;

    /**
     * Service and method name matched but argument length or types mismatched.
     * 
     * @since 1.5
     */
    const GENERAL_METHOD_ARGUMENT_MISMATCHED = 5;

    // 11 ----------------------------------------------------------------------

    /**
     * @see RemoteAccessDeniedException
     * @since 1.5
     */
    const GENERAL_CLIENT_ACCESS_DENIED = 11;

    /**
     * 
     * @see UnauthorizedClientAddressException
     * @since 1.5
     */
    const GENERAL_UNAUTHORIZED_CLIENT_IP = 12;

    // 100 ---------------------------------------------------------------------

    /**
     * <p>
     * Error at the server side, matters such as database connectivity, bugs, etc. We try to stick close with the definition of Sender/Receiver SOAP fault
     * codes.
     * </p>
     * 
     * <p>
     * If it is caused by client's inputs, that means our code has to be improved to handle it.
     * </p>
     * 
     * @see <a href="http://www.w3.org/TR/soap12-part1/#soapfault">SOAP Fault Codes</a>
     * @see BASE_CLIENT_FAULT_CODE
     * @since 1.5
     */
    const BASE_SERVER_FAULT_CODE = 100;

    /**
     * 
     * @since 1.5
     */
    const SERVER_INTERNAL_ERROR = 101;

    // 111 ---------------------------------------------------------------------

    /**
     * Process aborted during data access. There could be a problem with database connection or data conversion. Client may try to adjust your inputs but
     * there's no guarantee.
     * 
     * @since 1.5
     */
    const SERVER_DATA_ACCESS_EXCEPTION = 111;

    // 1000 --------------------------------------------------------------------

    /**
     * <p>
     * requests to other methods before re-sending the same request.
     * </p>
     * 
     * <p>
     * If adjustments have been made and error still persists, it is very likely that there is a bug with the server. Please report!
     * </p>
     * 
     * @see <a href="http://www.w3.org/TR/soap12-part1/#soapfault">SOAP Fault Codes</a>
     * @since 1.5
     */
    const BASE_CLIENT_FAULT_CODE = 1000;

    /**
     * 
     * @see SerializationException
     * @since 1.5
     */
    const CLIENT_SERIALIZATION_EXCEPTION = 1001;

    /**
     * 
     * @since 1.5
     */
    const CLIENT_INVALID_ARGUMENT = 1002;

    // 1011 --------------------------------------------------------------------

    /**
     * The username provided for registration or authentication is rejected as it is not in the right format.
     * 
     * @see InvalidUsernameFormatException
     * @since 1.5
     */
    const CLIENT_INVALID_USERNAME_FORMAT = 1011;

    /**
     * The username is not found in the database or the password is incorrect.
     * 
     * @see Aflexi_Common_Security_AuthenticationException
     * @since 1.5
     */
    const CLIENT_INVALID_USERNAME_OR_PASSWORD = 1012;

    /**
     * User is authenticated but the request cannot be completed due to insufficient permission.
     * 
     * @see PermissionDeniedException
     * @since 1.5
     */
    const CLIENT_PERMISSION_DENIED = 1013;

    /**
     * User is authenticated but suspended.
     * 
     * @see UserSuspendedException
     * @since 1.5
     */
    const CLIENT_USER_SUSPENDED = 1014;

    /**
     * User is authenticated but is restricted to perform only certain requests.
     * 
     * @see UserRestrictedException
     * @since 1.5
     */
    const CLIENT_USER_RESTRICTED = 1015;

    /**
     * User is trying to perform an action on themselves or others with expected role mismatched.
     * 
     * @see UserRoleMismatchedException
     * @since 2.1
     */
    const CLIENT_USER_ROLE_MISMATCHED = 1016;

    // 1101 --------------------------------------------------------------------

    /**
     * 
     * @see Aflexi_Common_Model_EntityNotFoundException
     * @since 1.5
     */
    const CLIENT_ENTITY_NOT_FOUND = 1101;

    /**
     * 
     * @see EntityAlreadyExistsException
     * @since 1.5
     */
    const CLIENT_ENTITY_ALREADY_EXISTS = 1102;

    /**
     * 
     * @see EntityConstraintViolationException
     * @since 1.5
     */
    const CLIENT_ENTITY_CONSTRAINTS_VIOLATED = 1103;

    // 1111 --------------------------------------------------------------------

    /**
     * 
     * @see EntityLimitExceededException
     * @since 1.5
     */
    const CLIENT_ENTITY_LIMIT_EXCEEDED = 1111;

    /**
     * 
     * @since 1.5
     */
    const CLIENT_PUBLISHER_LINK_IS_EXCLUSIVE = 1112;

    // 1201 --------------------------------------------------------------------

    /**
     * Request is aborted as it does not fulfill or violate a business constraint. Read the exception message for more details.
     */
    const CLIENT_BUSINESS_CONSTRAINT_VIOLATION = 1201;
}

?>