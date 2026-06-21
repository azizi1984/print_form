    <!-- Topbar -->
    <nav class="topbar">
        <div class="container-fluid">
            <a href="#" class="logo">
                <i class="fas fa-layer-group"></i> PRINT FORM
            </a>

            <!-- <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" class="form-control" placeholder="Search...">
            </div> -->

            <div class="topbar-menu">
                <a href="#"><i class="fas fa-flag"></i></a>
                <a href="#"><i class="fas fa-th"></i></a>
                <a href="#"><i class="far fa-clone"></i></a>
                <!-- <a href="#">
                    <i class="far fa-bell"></i>
                    <span class="notification-badge">3</span>
                </a> -->
                <div class="dropdown">
                    <div class="user-profile py-1" data-bs-toggle="dropdown" aria-expanded="false" role="button">
                        <img src="https://ui-avatars.com/api/?name={{ Auth::user()->username }}&background=667eea&color=fff" alt="User">
                        <span>{{ Auth::user()->username }} <i class="fas fa-chevron-down ms-1" style="font-size: 12px;"></i></span>
                    </div>
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2" style="border-radius: 8px; min-width: 150px; padding: 0.5rem 0;">
                        <li>
                            <a class="dropdown-item d-flex align-items-center" href="#" style="font-size: 14px; padding: 8px 16px; color: #495057 !important;">
                                <i class="bi bi-person me-2 text-muted" style="font-size: 16px;"></i> Profile
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center" href="#" style="font-size: 14px; padding: 8px 16px; color: #495057 !important;">
                                <i class="bi bi-gear me-2 text-muted" style="font-size: 16px;"></i> Settings
                            </a>
                        </li>
                        <li><hr class="dropdown-divider my-1"></li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center text-danger" href="{{ route('logout') }}" style="font-size: 14px; padding: 8px 16px;">
                                <i class="bi bi-box-arrow-right me-2" style="font-size: 16px;"></i> Logout
                            </a>
                        </li>
                    </ul>
                </div>
                <a href="#"><i class="fas fa-cog"></i></a>
            </div>
        </div>
    </nav>

    <!-- Navigation Tabs -->
    <div class="nav-tabs-custom">
        <ul class="nav nav-tabs">
            <li class="nav-item">
                <a class="nav-link active" href="#"><i class="fas fa-home"></i> Dashboard</a>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="{{ route('roles.index') }}" id="managementDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    Export Declaration
                </a>

                <ul class="dropdown-menu" aria-labelledby="managementDropdown">
                    <li>
                        <a class="dropdown-item" href="{{ route('profile-template') }}">
                            Profile Template
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="{{ route('header-template') }}">
                            Header Template
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="{{ route('detail-template') }}">
                            Detail Template
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="{{ route('footer-template') }}">
                            Footer Template
                        </a>
                    </li>
                </ul>
            </li>
            <!-- <li class="nav-item">
                <a class="nav-link" href="#">UI Elements</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">Apps</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">Components</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">Extra pages</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">Layouts</a>
            </li> -->
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="{{ route('roles.index') }}" id="managementDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-gear-fill me-1"></i>
                    Setting
                </a>

                <ul class="dropdown-menu" aria-labelledby="managementDropdown">

                    <li>
                        <a class="dropdown-item" href="{{ route('roles.index') }}">
                            <i class="bi bi-people me-2 text-muted"></i> Users List
                        </a>
                    </li>

                    <li>
                        <a class="dropdown-item" href="#">
                            <i class="bi bi-shield-lock me-2 text-muted"></i> Roles & Permissions
                        </a>
                    </li>

                    <li><hr class="dropdown-divider"></li>

                    <li>
                        <a class="dropdown-item" href="#">
                            <i class="bi bi-sliders me-2 text-muted"></i> System Settings
                        </a>
                    </li>

                </ul>
            </li>
        </ul>
    </div>