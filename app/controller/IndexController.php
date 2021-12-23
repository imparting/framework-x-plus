<?php

namespace app\controller;

use app\model\UserModel;
use DI\Annotation\Inject;
use React\Http\Message\ServerRequest;

class IndexController extends BaseController
{
    /**
     * @Inject()
     * @var UserModel $userModel
     */
    public UserModel $userModel;

    public function __invoke(ServerRequest $request): ?array
    {
        return $this->userModel->getUser();
    }
}