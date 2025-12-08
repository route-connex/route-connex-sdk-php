<?php

namespace RouteConnex\RouteConnexSdkPhp;

enum HttpMethod: string
{
    const GET_VALUE = 'get';

    const POST_VALUE = 'post';

    const PUT_VALUE = 'put';

    const PATCH_VALUE = 'patch';

    const DELETE_VALUE = 'delete';

    case GET = self::GET_VALUE;
    case POST = self::POST_VALUE;
    case PUT = self::PUT_VALUE;
    case PATCH = self::PATCH_VALUE;
    case DELETE = self::DELETE_VALUE;
}
