

            @php
                $totaluser = App\Models\User::where('view', 1)->count();
                $totalmembership = App\Models\Membership::where('view', 1)->count();
                $total = $totaluser + $totalmembership;
            @endphp


<nav class="sidebar sidebar-offcanvas" id="sidebar">
    <ul class="nav">
        <li class="nav-item {{ areActiveRoutes(['home'])}}">
            <a class="nav-link" href="{{ route('home') }}">
                <i class="mdi mdi-grid-large menu-icon"></i>
                <span class="menu-title">Dashboard</span>
            </a>
        </li>
        <li class="nav-item nav-category">Navigations</li>
        <li class="nav-item {{ areActiveRoutes(['users.index'])}}">
            <a class="nav-link" href="{{ route('users.index') }}">
                <i class="mdi mdi-account menu-icon"></i>
                <span class="menu-title">Users  @if($totaluser > 0)<span style="margin-left:20px" class="text-danger"> <b> ({{ $totaluser }}) </b></span> @endif </span>
            </a>
        </li>
        <li class="nav-item {{ areActiveRoutes(['packages.index', 'packages.edit'])}}">
            <a class="nav-link" href="{{ route('packages.index') }}">
                <i class="mdi mdi-package-variant menu-icon"></i>
                <span class="menu-title">Package</span>
            </a>
        </li>
        <li class="nav-item {{ areActiveRoutes(['transections.index'])}}">
            <a class="nav-link" href="{{ route('transections.index') }}">
                <i class="mdi mdi-layers menu-icon"></i>
                <span class="menu-title">Transection</span>
            </a>
        </li>
        <li class="nav-item {{ areActiveRoutes(['notifications.index'])}}">
            <a class="nav-link" href="{{ route('notifications.index') }}">
                <i class="mdi mdi-bell menu-icon"></i>
                <span class="menu-title">Notification @if($totalmembership > 0)<span style="margin-left:20px" class="text-danger"> <b> ({{ $totalmembership }}) </b></span> @endif </span>
            </a>
        </li>
        <li class="nav-item {{ areActiveRoutes(['send-notifications.index', 'send-notifications.create'])}}">
            <a class="nav-link" href="{{ route('send-notifications.index') }}">
                <i class="mdi mdi-notification-clear-all  menu-icon"></i>
                <span class="menu-title">Send Notification </span>
            </a>
        </li>
        <li class="nav-item {{ areActiveRoutes(['abusives.index', 'abusives.create', 'abusives.edit'])}}">
            <a class="nav-link" href="{{ route('abusives.index') }}">
                <i class="mdi mdi-layers menu-icon"></i>
                <span class="menu-title">Abusive</span>
            </a>
        </li>
        <li class="nav-item {{ areActiveRoutes(['user.report'])}}">
            <a class="nav-link " data-bs-toggle="collapse" href="#ui-advanced" aria-expanded="false" aria-controls="ui-advanced">
              <i class="menu-icon mdi mdi-arrow-down-drop-circle-outline"></i>
              <span class="menu-title">Reports</span>
                <i class="mdi mdi-arrow-down-box"></i>
            </a>
            <div class="collapse" id="ui-advanced">
              <ul class="nav flex-column sub-menu">
                <li class="nav-item"> <a class="nav-link {{ areActiveRoutes(['user.report'])}}" href="{{ route('user.report') }}">User Report</a></li>
                <li class="nav-item"> <a class="nav-link {{ areActiveRoutes(['transection.report'])}}" href="{{ route('transection.report') }}">Transaction Report</a></li>
                <li class="nav-item"> <a class="nav-link {{ areActiveRoutes(['money-got-report'])}}" href="{{ route('money-got-report') }}">Money Got & Give Report</a></li>
                <li class="nav-item"> <a class="nav-link {{ areActiveRoutes(['call-sms-report'])}}" href="{{ route('call-sms-report') }}">Reminder Report</a></li>
                <li class="nav-item"> <a class="nav-link {{ areActiveRoutes(['user-activity-report'])}}" href="{{ route('user-activity-report') }}">Daily Active Users Report</a></li>
                <li class="nav-item"> <a class="nav-link {{ areActiveRoutes(['monthly-user-activity-report'])}}" href="{{ route('monthly-user-activity-report') }}">Monthly Active Users Report</a></li>
                <li class="nav-item"> <a class="nav-link {{ areActiveRoutes(['device-user-report'])}}" href="{{ route('device-user-report') }}">iOS & Android Users Report</a></li>
              </ul>
            </div>
          </li>
        <li class="nav-item {{ areActiveRoutes(['general_settings.index'])}}">
            <a class="nav-link" href="{{ route('general_settings.index') }}">
                <i class="mdi mdi-package-variant menu-icon"></i>
                <span class="menu-title">General Settings</span>
            </a>
        </li>
        <li class="nav-item {{ areActiveRoutes(['faqs.index', 'faqs.create', 'faqs.edit'])}}">
            <a class="nav-link" href="{{ route('faqs.index') }}">
                <i class="mdi mdi-layers menu-icon"></i>
                <span class="menu-title">Faqs</span>
            </a>
        </li>
        <li class="nav-item {{ areActiveRoutes(['version.showData'])}}">
            <a class="nav-link" href="{{ route('version.showData') }}">
                <i class="mdi mdi-layers menu-icon"></i>
                <span class="menu-title">version</span>
            </a>
        </li>
    </ul>
</nav>
