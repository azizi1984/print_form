<div class="topbar-wrapper">
    <!-- Topbar Header -->
    <nav class="topbar">
        <div class="container-fluid">
            <a href="{{ route('dashboard') }}" class="logo">
                <div class="logo-badge">
                    <i class="bi bi-printer-fill"></i>
                </div>
                <span>PRINT <span style="color: #818cf8 !important;">FORM</span></span>
            </a>

            <div class="topbar-menu">
                <a href="{{ route('dashboard') }}" class="topbar-icon-btn" title="Dashboard">
                    <i class="bi bi-grid-fill"></i>
                </a>

                <div class="dropdown">
                    <div class="user-profile" data-bs-toggle="dropdown" aria-expanded="false" role="button">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->username ?? 'User') }}&background=4f46e5&color=fff&bold=true" alt="User">
                        <span class="username-text">{{ Auth::user()->username ?? 'User' }}</span>
                        <i class="bi bi-chevron-down ms-1 text-white-50" style="font-size: 11px;"></i>
                    </div>
                    <ul class="dropdown-menu dropdown-menu-end mt-2">
                        <li class="px-3 py-2 border-bottom mb-1">
                            <div class="fw-bold text-dark" style="font-size: 13px;">{{ Auth::user()->name ?? Auth::user()->username }}</div>
                            <small class="text-muted" style="font-size: 11.5px;">{{ Auth::user()->email ?? 'Active User' }}</small>
                        </li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center" href="{{ route('users.index') }}">
                                <i class="bi bi-person me-2 text-muted"></i> Users Management
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center" href="{{ route('roles.index') }}">
                                <i class="bi bi-shield-lock me-2 text-muted"></i> Roles & Permissions
                            </a>
                        </li>
                        <li><hr class="dropdown-divider my-1"></li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center text-danger" href="{{ route('logout') }}">
                                <i class="bi bi-box-arrow-right me-2"></i> Logout
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <!-- Secondary Navigation Tabs -->
    <div class="nav-tabs-custom">
        <ul class="nav">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle {{ request()->is('ex-declaration*') ? 'active' : '' }}" href="#" id="exportDeclarationDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-file-earmark-text"></i> Export Declaration
                </a>

                <ul class="dropdown-menu" aria-labelledby="exportDeclarationDropdown">
                    <li>
                        <a class="dropdown-item d-flex align-items-center {{ request()->routeIs('profile-template*') ? 'active' : '' }}" href="{{ route('profile-template') }}">
                            <i class="bi bi-file-earmark-person me-2 text-muted"></i> Profile Template
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item d-flex align-items-center {{ request()->routeIs('header-template*') ? 'active' : '' }}" href="{{ route('header-template') }}">
                            <i class="bi bi-layout-text-window me-2 text-muted"></i> Header Template
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item d-flex align-items-center {{ request()->routeIs('detail-template*') ? 'active' : '' }}" href="{{ route('detail-template') }}">
                            <i class="bi bi-table me-2 text-muted"></i> Detail Template
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item d-flex align-items-center {{ request()->routeIs('footer-template*') ? 'active' : '' }}" href="{{ route('footer-template') }}">
                            <i class="bi bi-layout-text-window-reverse me-2 text-muted"></i> Footer Template
                        </a>
                    </li>
                </ul>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle {{ request()->is('users*') || request()->is('roles*') || request()->is('permissions*') ? 'active' : '' }}" href="#" id="systemSettingsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-gear"></i> System Settings
                </a>

                <ul class="dropdown-menu" aria-labelledby="systemSettingsDropdown">
                    <li>
                        <a class="dropdown-item d-flex align-items-center {{ request()->is('users*') ? 'active' : '' }}" href="{{ route('users.index') }}">
                            <i class="bi bi-people me-2 text-muted"></i> Users List
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item d-flex align-items-center {{ request()->is('roles*') || request()->is('permissions*') ? 'active' : '' }}" href="{{ route('roles.index') }}">
                            <i class="bi bi-shield-check me-2 text-muted"></i> Roles & Permissions
                        </a>
                    </li>
                </ul>
            </li>
        </ul>
    </div>
</div>