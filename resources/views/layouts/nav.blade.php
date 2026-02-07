<nav class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme"
     id="layout-navbar">
    <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
        <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
            <i class="mdi mdi-menu mdi-24px"></i>
        </a>
    </div>
    <div class="navbar-nav-right d-flex align-items-center justify-content-between" id="navbar-collapse">
        <div>
            <i class="mdi mdi-clock-outline"></i> <span id="ic-live-date-time"></span>
        </div>
        <ul class="navbar-nav flex-row align-items-center">
            <li class="nav-item navbar-dropdown dropdown-user dropdown">
                <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
                    <div class="avatar avatar-online ic-avatar">
                        <div class="d-flex align-items-center justify-content-center rounded-circle bg-white p-1">
                            <i class="mdi mdi-account-outline fs-3"></i>
                        </div>
                    </div>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <a class="dropdown-item" href="">
                            <div class="d-flex">
                                <div class="flex-shrink-0 me-3">
                                    <div class="avatar ic-avatar">
                                        <div
                                            class="d-flex align-items-center justify-content-center rounded-circle bg-white p-1">
                                            <i class="mdi mdi-account-outline fs-3"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <small class="text-muted">Super Admin</small>
                                </div>
                            </div>
                        </a>
                    </li>
                    <li>
                        <div class="dropdown-divider"></div>
                    </li>
                    <li>
                        <a class="dropdown-item" href="">
                            <i class="mdi mdi-account-outline me-2"></i>
                            <span class="align-middle">My Profile</span>
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="">
                            <i class="mdi mdi-logout me-2"></i>
                            <span class="align-middle">Log Out</span>
                        </a>
                    </li>
                </ul>
            </li>
        </ul>
    </div>
</nav>
