<?php

namespace Botble\Impersonate\Models;

use Botble\ACL\Models\User as BaseUser;
use Lab404\Impersonate\Models\Impersonate;

class User extends BaseUser
{
    use Impersonate;
}
