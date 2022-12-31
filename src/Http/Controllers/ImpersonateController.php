<?php

namespace Botble\Impersonate\Http\Controllers;

use Botble\ACL\Repositories\Interfaces\UserInterface;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Supports\Helper;
use Illuminate\Http\Request;

class ImpersonateController extends BaseController
{
    protected UserInterface $userRepository;

    public function __construct(UserInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function getImpersonate(int $id, Request $request)
    {
        $user = $this->userRepository->findOrFail($id);
        $request->user()->impersonate($user);

        Helper::clearCache();

        return redirect()->route('dashboard.index');
    }

    public function leaveImpersonation(Request $request)
    {
        $request->user()->leaveImpersonation();

        return redirect()->route('users.index');
    }
}
