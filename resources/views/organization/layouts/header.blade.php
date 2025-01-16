<header class="app-header ">
    <!-- Start::main-header-container -->
    <div class="main-header-container container-fluid">
        <!-- Start::header-content-left -->
        <div class="header-content-left " data-intro="Click this to show or hide the sidebar." data-step="9">
            <div class="header-element">
                <!-- Start::header-link -->
                <a aria-label="Hide Sidebar"
                    class="sidemenu-toggle header-link animated-arrow hor-toggle horizontal-navtoggle"
                    data-bs-toggle="sidebar" href="javascript:void(0);"><span></span></a>
                <!-- End::header-link -->
            </div>
        </div>
        <!-- End::header-content-left -->

        <div class="header-content-right">
            <div class="header-element" data-intro="This section shows your Authorization status." data-step="13">
                <a href="javascript:void(0);" class="header-link dropdown-toggle">
               
                        <div class="d-flex align-items-center justify-content-center">
                            <div class="rounded d-flex align-items-center p-2">
                                <i class="ri-checkbox-circle-line text-success fs-5 me-1"></i>
                                <span class="fw-bold text-success">User Authorized</span>
                            </div>
                        </div>
                
                </a>
            </div>
            <div class="header-element" data-intro="Manage your roles and access different xBug APP dashboards here."
                data-step="15">
                <!-- Start::header-link|dropdown-toggle -->
                <a href="javascript:void(0);" class="header-link dropdown-toggle" data-bs-toggle="dropdown"
                    data-bs-auto-close="outside" aria-expanded="false">
                    <div class="d-flex align-items-center">
                        <i class="ms-2 bx bx-grid-alt header-link-icon"></i>
                    </div>
                </a>
                <!-- End::header-link|dropdown-toggle -->
                <div class="main-header-dropdown dropdown-menu dropdown-menu-end" data-popper-placement="none">
                    <div class="p-3 d-flex">
                        <div class="d-flex align-items-center justify-content-between">
                            <p class="mb-0 fs-17 fw-semibold">App</p>
                        </div>
                    </div>
                    <div>
                        <hr class="dropdown-divider">
                    </div>

                    <ul class="list-unstyled mb-0" id="header-notification-scroll">
                        <li class="dropdown-item">
                            <div class="d-flex align-items-center">
                                <div class="pe-2">
                                    <span class="avatar avatar-md text-success avatar-rounded">
                                        <i class="bx bxs-user-circle fs-34"></i>
                                    </span>
                                </div>
                                <div class="flex-grow-1 d-flex align-items-center justify-content-between p-2">
                                    <div>
                                        <span class="mb-0 fw-semibold p-2">
                                            <a
                                                href="{{ env('XBUG_URL') .'/'.'organization/dashboard' }}">xBUG WEB</a>
                                        </span>
                                    </div>
                                    <div>
                                        <a  href="{{ env('XBUG_URL') .'/'.'organization/dashboard' }}"
                                            class="min-w-fit-content text-muted me-1 dropdown-item-close1">
                                            <i class="bx bx-right-arrow-alt fs-22"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </li>
                        <li class="dropdown-item">
                            <div class="d-flex align-items-center">
                                <div class="pe-2">
                                    <span class="avatar avatar-md text-dark avatar-rounded">
                                        <i class="bx bxs-user-circle fs-34"></i>
                                    </span>
                                </div>
                                <div class="flex-grow-1 d-flex align-items-center justify-content-between p-2">
                                    <div>
                                        <span class="mb-0 fw-semibold p-2">
                                            <a
                                                href="/protected/dashboard">xBUG Blockchain WEB</a>
                                        </span>
                                    </div>
                                    <div>
                                        <a  href="/protected/dashboard"
                                            class="min-w-fit-content text-muted me-1 dropdown-item-close1">
                                            <i class="bx bx-right-arrow-alt fs-22"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </li>
                    </ul>
                    <div class="p-2 empty-header-item border-top"></div>
                </div>
            </div>

            <div class="header-element header-fullscreen">
                <!-- Start::header-link -->
                <a onclick="toggleFullscreen();" href="javascript:void(0);" class="header-link">
                    <i class="bx bx-fullscreen full-screen-open header-link-icon"></i>
                    <i class="bx bx-exit-fullscreen full-screen-close header-link-icon d-none"></i>
                </a>
                <!-- End::header-link -->
            </div>
            <!-- End::header-element -->

            <div class="header-element" data-intro="Access your profile and logout options here." data-step="17">
                <!-- Start::header-link|dropdown-toggle -->
                <a href="javascript:void(0);" class="header-link dropdown-toggle" id="mainHeaderProfile"
                    data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
                    <div class="d-flex align-items-center">
                        <div class="me-sm-2 me-0">
                            <img src="../assets/images/user/avatar-1.jpg" alt="img" width="32" height="32"
                                class="rounded-circle">
                        </div>
                        <div class="d-sm-block d-none">
                            <p class="fw-bold mb-0 lh-1">
                                {{ implode(' ', array_slice(explode(' ', Auth::user()->name), 0, 2)) }}</p>
                            <span class="op-7 fw-semibold d-block fs-11">Organization</span>
                        </div>
                    </div>
                </a>
                <!-- End::header-link|dropdown-toggle -->
                <ul class="main-header-dropdown dropdown-menu pt-0 overflow-hidden header-profile-dropdown dropdown-menu-end"
                    aria-labelledby="mainHeaderProfile">
                    <li><a class="dropdown-item d-flex" href="{{ env('XBUG_URL') }}/organization/profile"><i
                                class="ti ti-user-circle fs-18 me-2 op-7"></i>Profile</a></li>
                    <li>
                        <a class="dropdown-item d-flex" href="#"
                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="ti ti-logout fs-18 me-2 op-7"></i>
                            LogOut
                        </a>

                        <!-- Form Logout -->
                        <form id="logout-form" action="{{route('organization.logout')}}" method="POST" style="display: none;">
                            @csrf
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <!-- End::main-header-container -->
</header>
