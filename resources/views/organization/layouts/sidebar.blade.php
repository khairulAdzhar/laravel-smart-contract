<aside class="app-sidebar sticky" id="sidebar">
    <!-- Start::main-sidebar-header -->
    <div class="main-sidebar-header">
        <a href="" class="header-logo">
            <h5 class="fw-bold text-light">xBUG*</h5>
        </a>
    </div>
    <!-- End::main-sidebar-header -->

    <!-- Start::main-sidebar -->
    <div class="main-sidebar" id="sidebar-scroll"
        data-intro="Hi, Welcome to xBUG Blockchain APP Dashboard Panel! This is the main part of your sidebar, containing the navigation menu."
        data-step="1">
        <!-- Start::nav -->
        <nav class="main-menu-container nav nav-pills flex-column sub-open">
            <div class="slide-left" id="slide-left">
                <svg xmlns="http://www.w3.org/2000/svg" fill="#7b8191" width="24" height="24"
                    viewBox="0 0 24 24">
                    <path d="M13.293 6.293 7.586 12l5.707 5.707 1.414-1.414L10.414 12l4.293-4.293z"></path>
                </svg>
            </div>

            <ul class="main-menu">
                <!-- Start::slide__category -->
                <li class="slide__category">
                    <span class="category-name">Main</span>
                </li>
                <!-- End::slide__category -->

                <!-- Start::slide (Dashboard)-->
                <li class="slide" data-intro="This menu directs you to the main Dashboard." data-step="2">
                    <a href="{{ route('showDashboardOrganization') }}" class="side-menu__item">
                        <i class="bx bx-home side-menu__icon"></i>
                        <span class="side-menu__label">Dashboard</span>
                    </a>
                </li>
                @if (Auth::user()->ekyc_status === 1)
                    <!-- End::slide (Dashboard) -->
                    <li class="slide__category">
                        <span class="category-name">WEB</span>
                    </li>
                    <li class="slide has-sub" data-intro="This menu displays your Smart Contract detail."
                        data-step="3">
                        <a href="javascript:void(0);" class="side-menu__item ">
                            <i class='bx bxs-wallet-alt side-menu__icon'></i>
                            <span class="side-menu__label">Smart Contract</span>
                            <i class="fe fe-chevron-right side-menu__angle"></i>
                        </a>
                        <!-- Child Menu Activity -->
                        <ul class="slide-menu child1">
                            <li class="slide">
                                <a href="{{ route('showContentBlockchainOrg') }}" class="side-menu__item">Deploy Smart
                                    Contract</a>
                            </li>
                        </ul>
                    </li>
                    <!-- End::Content Activity Menu -->
                    <li class="slide__category">
                        <span class="category-name">LOGGING</span>
                    </li>
                    <li class="slide has-sub"
                        data-intro="This menu displays your notification we send to you from xBUG." data-step="4">
                        <a href="javascript:void(0);" class="side-menu__item ">
                            <i class='bx bxs-notification side-menu__icon'></i>
                            <span class="side-menu__label">Notification</span>
                            <i class="fe fe-chevron-right side-menu__angle"></i>
                        </a>
                        <!-- Child Menu Activity -->
                        <ul class="slide-menu child1">
                            <li class="slide">
                                <a href="{{ route('showNotificationOrg') }}" class="side-menu__item">
                                    Notifications</a>
                            </li>
                        </ul>
                    </li>
                @endif
                <!-- End::xBUG Ai Menu -->

            </ul>

            <div class="slide-right" id="slide-right">
                <svg xmlns="http://www.w3.org/2000/svg" fill="#7b8191" width="24" height="24"
                    viewBox="0 0 24 24">
                    <path d="M10.707 17.707 16.414 12l-5.707-5.707-1.414 1.414L13.586 12l-4.293 4.293z"></path>
                </svg>
            </div>
        </nav>
        <!-- End::nav -->
    </div>
    <!-- End::main-sidebar -->
</aside>
