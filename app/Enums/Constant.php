<?php

namespace App\Enums;

class Constant
{
//    STATUS CODE
    const SUCCESS_CODE              = 200;
    const FALSE_CODE                = -1;
    const CREATED_CODE              = 201;
    const ACCEPTED_CODE             = 202;
    const NO_CONTENT_CODE           = 204;
    const BAD_REQUEST_CODE          = 400;
    const UNAUTHORIZED_CODE         = 401;
    const FORBIDDEN_CODE            = 403;
    const NOT_FOUND_CODE            = 404;
    const METHOD_NOT_ALLOWED_CODE   = 405;
    const HTTP_UNPROCESSABLE_ENTITY   = 422;
    const INTERNAL_SV_ERROR_CODE    = 500;

    const message = [
        '404' => 'not found',
    ];
    const DISTANCE_MAP_NOT_FOUND    = 'NOT_FOUND';

// ORDERING
    const ORDER_BY                  = 20;
    const PER_PAGE                  = 20;

// MAX SIZE FILE
    const MAX_SIZE_FILE             = 5242880;

    const TIME_CANCEL_TICKET = 1800; //second
// FIREBASE NOTIFY TYPE
    const FCM_FIREBASE_URI          = 'https://fcm.googleapis.com/fcm/send';

    //PATH FOLDER

    const PATH_UPLOAD               = 'uploads';
    const PATH_LIBRARY              = 'libraries';
    const PATH_EDITOR               = 'editor';
    const PATH_AVATAR               = 'avatar';

}
