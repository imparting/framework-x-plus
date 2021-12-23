<?php

namespace app\controller;

use React\Http\Message\ServerRequest;
use Respect\Validation\Validator as v;

class TestController extends BaseController
{
    public function index(ServerRequest $request)
    {
        return v::input($request->getQueryParams(), [
            'nickname' => v::length(1, 64)->setName('昵称'),
        ]);
    }
}