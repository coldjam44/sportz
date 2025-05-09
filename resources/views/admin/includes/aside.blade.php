<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
<div class="app-brand demo">
    <a href="index.html" class="app-brand-link">
    <span class="app-brand-logo demo">
        <svg width="32" height="22" viewBox="0 0 32 22" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path
            fill-rule="evenodd"
            clip-rule="evenodd"
            d="M0.00172773 0V6.85398C0.00172773 6.85398 -0.133178 9.01207 1.98092 10.8388L13.6912 21.9964L19.7809 21.9181L18.8042 9.88248L16.4951 7.17289L9.23799 0H0.00172773Z"
            fill="#7367F0" />
        <path
            opacity="0.06"
            fill-rule="evenodd"
            clip-rule="evenodd"
            d="M7.69824 16.4364L12.5199 3.23696L16.5541 7.25596L7.69824 16.4364Z"
            fill="#161616" />
        <path
            opacity="0.06"
            fill-rule="evenodd"
            clip-rule="evenodd"
            d="M8.07751 15.9175L13.9419 4.63989L16.5849 7.28475L8.07751 15.9175Z"
            fill="#161616" />
        <path
            fill-rule="evenodd"
            clip-rule="evenodd"
            d="M7.77295 16.3566L23.6563 0H32V6.88383C32 6.88383 31.8262 9.17836 30.6591 10.4057L19.7824 22H13.6938L7.77295 16.3566Z"
            fill="#7367F0" />
        </svg>
    </span>
    <span class="app-brand-text demo menu-text fw-bold">SportZ</span>
    </a>

    <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
    <i class="ti menu-toggle-icon d-none d-xl-block ti-sm align-middle"></i>
    <i class="ti ti-x d-block d-xl-none ti-sm align-middle"></i>
    </a>
</div>

<div class="menu-inner-shadow"></div>

<ul class="menu-inner py-1">
    <!-- Page -->
    <li class="menu-item active">
    <a href="{{ route('admin.home') }}" class="menu-link">
        <i class="menu-icon tf-icons ti ti-smart-home"></i>
        <div data-i18n="Home">Home</div>
    </a>
    </li>
    <br>
    <li class="menu-item active">
    <a href="{{ route('sportsusers.index') }}" class="menu-link">
        <i class="menu-icon tf-icons ti ti-smart-home"></i>
        <div data-i18n="Home">{{ trans('main_trans.sportsuser') }}</div>
    </a>
    </li>
    <br>
    <li class="menu-item">
        <a href="#" class="menu-link dropdown-toggle d-flex align-items-center justify-content-between" data-bs-toggle="collapse" data-bs-target="#providerMenu">
            <div class="d-flex align-items-center">
                <i class="menu-icon tf-icons ti ti-user me-2"></i>
                <span data-i18n="Provider">{{ trans('main_trans.provider') }}</span>
            </div>
            <i class="ti ti-chevron-down"></i> <!-- أيقونة السهم -->
        </a>
        <ul class="collapse list-unstyled menu-dropdown" id="providerMenu">
            <li><a href="{{ route('avilableservices.index') }}" class="dropdown-item {{ request()->routeIs('avilableservices.index') ? 'active' : '' }}">{{ trans('main_trans.avilableservices') }}</a></li>
            <li><a href="{{ route('createstadiums.index') }}" class="dropdown-item {{ request()->routeIs('createstadiums.index') ? 'active' : '' }}">{{ trans('main_trans.createstadiums') }}</a></li>
            <li><a href="{{ route('providerrates.index') }}" class="dropdown-item {{ request()->routeIs('providerrates.index') ? 'active' : '' }}">{{ trans('main_trans.providerrates') }}</a></li>
            <li><a href="{{ route('sections.index') }}" class="dropdown-item {{ request()->routeIs('sections.index') ? 'active' : '' }}">{{ trans('main_trans.sections') }}</a></li>
            <li><a href="{{ route('addproducts.index') }}" class="dropdown-item {{ request()->routeIs('addproducts.index') ? 'active' : '' }}">{{ trans('main_trans.addproducts') }}</a></li>
            <li><a href="{{ route('contactuss.index') }}" class="dropdown-item {{ request()->routeIs('contactuss.index') ? 'active' : '' }}">{{ trans('main_trans.contactuss') }}</a></li>
            <li><a href="{{ route('aboutuss.index') }}" class="dropdown-item {{ request()->routeIs('aboutuss.index') ? 'active' : '' }}">{{ trans('main_trans.aboutuss') }}</a></li>
            <li><a href="{{ route('createstores.index') }}" class="dropdown-item {{ request()->routeIs('createstores.index') ? 'active' : '' }}">{{ trans('main_trans.createstores') }}</a></li>
        </ul>
    </li>

    @push('styles')
    <style>
        /* تحسين القائمة المنسدلة */
        .menu-dropdown {
            background: #f8f9fa; /* لون الخلفية */
            border-radius: 8px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            padding: 8px 0;
            margin-top: 5px;
        }

        /* تحسين عناصر القائمة */
        .dropdown-item {
            padding: 12px 15px;
            font-size: 14px;
            color: #333;
            transition: all 0.3s ease-in-out;
            display: block;
            text-decoration: none;
        }

        /* تمييز العنصر النشط */
        .dropdown-item.active {
            background-color: #007bff !important;
            color: #fff !important;
            font-weight: bold;
            border-radius: 5px;
        }

        /* تأثير عند التحويم */
        .dropdown-item:hover {
            background-color: #007bff;
            color: #fff;
            border-radius: 5px;
            padding-left: 20px;
        }

        /* تحسين أيقونة السهم */
        .menu-link .ti-chevron-down {
            transition: transform 0.3s ease;
        }

        /* عند فتح القائمة، يتم تدوير السهم */
        .menu-link[aria-expanded="true"] .ti-chevron-down {
            transform: rotate(180deg);
        }
    </style>
    @endpush


    <br>
    <li class="menu-item active">
    <a href="{{ route('userstores.index') }}" class="menu-link">
        <i class="menu-icon tf-icons ti ti-smart-home"></i>
        <div data-i18n="Home">{{ trans('main_trans.userstores') }}</div>
    </a>
    </li>
    <br>
    <li class="menu-item">
    <a href="javascript::void(0);" onclick="$('#logout_form').submit();" class="menu-link">
        <i class="menu-icon tf-icons ti ti-app-window"></i>
        <div data-i18n="Page 2">Logout</div>
    </a>
    </li>
</ul>
</aside>
