@if (Auth::user()->isImpersonated())
    <li class="dropdown">
        <a class="dropdown-toggle dropdown-header-name" style="padding-right: 10px"
           href="{{ route('users.leave_impersonation') }}">
            <i class="fas fa-user-ninja" style="color: #e7505a;"></i>
            <span class="d-none d-sm-inline"
                  style="color: #e7505a;">{{ trans('plugins/impersonate::impersonate.leave_impersonation') }}</span>
        </a>
    </li>
@endif
