<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MINIBLE - Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #556ee6;
            --sidebar-bg: #2a3042;
            --topbar-bg: #556ee6;
            --card-shadow: 0 0.75rem 1.5rem rgba(18,38,63,.03);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8f9fa;
            font-size: 14px;
        }

        /* Topbar */
        .topbar {
            background: var(--topbar-bg);
            padding: 0;
            height: 70px;
            position: fixed;
            top: 0;
            right: 0;
            left: 0;
            z-index: 1000;
            box-shadow: 0 2px 4px rgba(0,0,0,0.08);
        }

        .topbar .container-fluid {
            height: 100%;
            display: flex;
            align-items: center;
            padding: 0 24px;
        }

        .logo {
            color: white;
            font-size: 20px;
            font-weight: 700;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .logo i {
            font-size: 24px;
        }

        .search-box {
            max-width: 300px;
            margin-left: 30px;
            position: relative;
        }

        .search-box input {
            background: rgba(255,255,255,0.1);
            border: 1px solid rgba(255,255,255,0.2);
            color: white;
            padding: 8px 15px 8px 40px;
            border-radius: 4px;
            width: 100%;
        }

        .search-box input::placeholder {
            color: rgba(255,255,255,0.7);
        }

        .search-box i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(255,255,255,0.7);
        }

        .topbar-menu {
            margin-left: auto;
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .topbar-menu a {
            color: white;
            font-size: 18px;
            text-decoration: none;
            position: relative;
        }

        .notification-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background: #f46a6a;
            color: white;
            font-size: 10px;
            padding: 2px 5px;
            border-radius: 10px;
        }

        .user-profile {
            display: flex;
            align-items: center;
            gap: 10px;
            color: white;
            cursor: pointer;
        }

        .user-profile img {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            border: 2px solid rgba(255,255,255,0.3);
        }

        /* Navigation Tabs */
        .nav-tabs-custom {
            background: white;
            padding: 15px 24px 0;
            margin-top: 70px;
            border-bottom: 1px solid #e9ecef;
        }

        .nav-tabs-custom .nav-link {
            color: #74788d;
            border: none;
            padding: 12px 20px;
            border-bottom: 2px solid transparent;
            font-weight: 500;
        }

        .nav-tabs-custom .nav-link.active {
            color: var(--primary-color);
            border-bottom-color: var(--primary-color);
            background: transparent;
        }

        /* Main Content */
        .main-content {
            padding: 24px;
            margin-top: 0;
        }

        /* Stats Cards */
        .stats-card {
            background: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: var(--card-shadow);
            margin-bottom: 24px;
            position: relative;
        }

        .stats-card h3 {
            font-size: 28px;
            font-weight: 600;
            margin-bottom: 5px;
            color: #495057;
        }

        .stats-card p {
            color: #74788d;
            font-size: 13px;
            margin-bottom: 10px;
        }

        .stats-change {
            font-size: 13px;
            font-weight: 500;
        }

        .stats-change.positive {
            color: #34c38f;
        }

        .stats-change.negative {
            color: #f46a6a;
        }

        .mini-chart {
            position: absolute;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
            width: 100px;
            height: 50px;
        }

        .progress-circle {
            width: 60px;
            height: 60px;
            position: absolute;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
        }

        /* Sales Analytics */
        .analytics-card {
            background: white;
            border-radius: 8px;
            padding: 24px;
            box-shadow: var(--card-shadow);
            margin-bottom: 24px;
        }

        .analytics-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .analytics-header h5 {
            font-size: 16px;
            font-weight: 600;
            margin: 0;
        }

        .analytics-stats {
            display: flex;
            gap: 30px;
            margin-bottom: 20px;
        }

        .stat-item h4 {
            color: var(--primary-color);
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 2px;
        }

        .stat-item p {
            color: #74788d;
            font-size: 13px;
            margin: 0;
        }

        .chart-container {
            height: 300px;
            background: linear-gradient(to bottom, #f8f9fa 0%, white 100%);
            border-radius: 8px;
            padding: 20px;
            position: relative;
        }

        /* Top Users */
        .user-item {
            display: flex;
            align-items: center;
            padding: 15px;
            border-bottom: 1px solid #f8f9fa;
        }

        .user-item:last-child {
            border-bottom: none;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 15px;
        }

        .user-info {
            flex: 1;
        }

        .user-info h6 {
            font-size: 14px;
            font-weight: 600;
            margin: 0 0 3px 0;
        }

        .user-info p {
            font-size: 12px;
            color: #74788d;
            margin: 0;
        }

        .user-badge {
            padding: 4px 12px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 500;
            margin-right: 15px;
        }

        .badge-general { background: #fef5e4; color: #f1b44c; }
        .badge-success { background: #d4f4e2; color: #34c38f; }
        .badge-active { background: #dfe6f7; color: var(--primary-color); }
        .badge-pending { background: #fef5e4; color: #f1b44c; }

        .user-amount {
            font-weight: 600;
            color: #495057;
        }

        /* Recent Activity */
        .activity-item {
            display: flex;
            padding: 15px;
            border-bottom: 1px solid #f8f9fa;
        }

        .activity-item:last-child {
            border-bottom: none;
        }

        .activity-icon {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            border: 2px solid var(--primary-color);
            margin-right: 15px;
            margin-top: 5px;
            flex-shrink: 0;
        }

        .activity-content {
            flex: 1;
        }

        .activity-time {
            font-size: 12px;
            color: #74788d;
            margin-bottom: 5px;
        }

        .activity-text {
            font-size: 13px;
            color: #495057;
        }

        .activity-text a {
            color: var(--primary-color);
            text-decoration: none;
        }

        /* Promo Banner */
        .promo-banner {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 8px;
            padding: 30px;
            color: white;
            text-align: center;
            margin-bottom: 24px;
            position: relative;
            overflow: hidden;
        }

        .promo-banner h5 {
            font-size: 16px;
            margin-bottom: 15px;
        }

        .promo-banner .btn {
            background: #50c878;
            border: none;
            padding: 8px 20px;
            border-radius: 4px;
            color: white;
            font-weight: 500;
        }

        .promo-illustration {
            position: absolute;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
            opacity: 0.3;
            font-size: 80px;
        }

        /* Top Selling Products */
        .product-item {
            display: flex;
            align-items: center;
            padding: 12px 0;
        }

        .product-name {
            flex: 1;
            font-size: 13px;
            color: #495057;
        }

        .product-bar {
            flex: 2;
            height: 6px;
            background: #f8f9fa;
            border-radius: 3px;
            overflow: hidden;
            margin: 0 15px;
        }

        .product-bar-fill {
            height: 100%;
            border-radius: 3px;
        }

        /* Social Source */
        .social-item {
            text-align: center;
            padding: 20px;
        }

        .social-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            font-size: 24px;
        }

        .social-icon.facebook { background: #e7f3ff; color: #3b5998; }
        .social-icon.twitter { background: #e5f4fd; color: #00acee; }
        .social-icon.instagram { background: #fce4ec; color: #e1306c; }

        .social-name {
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .social-sales {
            color: #74788d;
            font-size: 13px;
        }

        /* Dropdowns */
        .dropdown-select {
            border: 1px solid #e9ecef;
            padding: 6px 12px;
            border-radius: 4px;
            font-size: 13px;
            color: #74788d;
            background: white;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .search-box {
                display: none;
            }

            .topbar-menu {
                gap: 15px;
            }

            .user-profile span {
                display: none;
            }
        }
    </style>
</head>
<body>
    <!-- Topbar -->
    <nav class="topbar">
        <div class="container-fluid">
            <a href="#" class="logo">
                <i class="fas fa-layer-group"></i> MINIBLE
            </a>

            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" class="form-control" placeholder="Search...">
            </div>

            <div class="topbar-menu">
                <a href="#"><i class="fas fa-flag"></i></a>
                <a href="#"><i class="fas fa-th"></i></a>
                <a href="#"><i class="far fa-clone"></i></a>
                <a href="#">
                    <i class="far fa-bell"></i>
                    <span class="notification-badge">3</span>
                </a>
                <div class="user-profile">
                    <img src="https://ui-avatars.com/api/?name=Marcus&background=667eea&color=fff" alt="User">
                    <span>Marcus <i class="fas fa-chevron-down" style="font-size: 12px;"></i></span>
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
            <li class="nav-item">
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
            </li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="container-fluid">
            <!-- Stats Cards Row -->
            <div class="row">
                <div class="col-xl-3 col-md-6">
                    <div class="stats-card">
                        <h3>$34,152</h3>
                        <p>Total Revenue</p>
                        <span class="stats-change positive">
                            <i class="fas fa-arrow-up"></i> 2.65% <span style="color: #74788d;">since last week</span>
                        </span>
                        <div class="mini-chart">
                            <svg width="100" height="50" style="opacity: 0.3;">
                                <polyline points="0,40 20,30 40,35 60,20 80,25 100,15"
                                    fill="none" stroke="#34c38f" stroke-width="2"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6">
                    <div class="stats-card">
                        <h3>5,643</h3>
                        <p>Orders</p>
                        <span class="stats-change negative">
                            <i class="fas fa-arrow-down"></i> 0.82% <span style="color: #74788d;">since last week</span>
                        </span>
                        <div class="progress-circle">
                            <svg width="60" height="60">
                                <circle cx="30" cy="30" r="25" fill="none" stroke="#e9ecef" stroke-width="6"/>
                                <circle cx="30" cy="30" r="25" fill="none" stroke="#50c878" stroke-width="6"
                                    stroke-dasharray="157" stroke-dashoffset="40"
                                    transform="rotate(-90 30 30)"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6">
                    <div class="stats-card">
                        <h3>45,254</h3>
                        <p>Customers</p>
                        <span class="stats-change positive">
                            <i class="fas fa-arrow-up"></i> 6.24% <span style="color: #74788d;">since last week</span>
                        </span>
                        <div class="progress-circle">
                            <svg width="60" height="60">
                                <circle cx="30" cy="30" r="25" fill="none" stroke="#e9ecef" stroke-width="6"/>
                                <circle cx="30" cy="30" r="25" fill="none" stroke="#556ee6" stroke-width="6"
                                    stroke-dasharray="157" stroke-dashoffset="50"
                                    transform="rotate(-90 30 30)"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6">
                    <div class="stats-card">
                        <h3>₹ 12.36</h3>
                        <p>Growth</p>
                        <span class="stats-change positive">
                            <i class="fas fa-arrow-up"></i> 10.51% <span style="color: #74788d;">since last week</span>
                        </span>
                        <div class="mini-chart">
                            <svg width="100" height="50" style="opacity: 0.3;">
                                <polyline points="0,35 20,25 40,30 60,18 80,22 100,12"
                                    fill="none" stroke="#f1b44c" stroke-width="2"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sales Analytics and Promo Row -->
            <div class="row">
                <div class="col-xl-8">
                    <div class="analytics-card">
                        <div class="analytics-header">
                            <h5>Sales Analytics</h5>
                            <select class="dropdown-select">
                                <option>Yearly</option>
                                <option>Monthly</option>
                                <option>Weekly</option>
                            </select>
                        </div>

                        <div class="analytics-stats">
                            <div class="stat-item">
                                <h4>$2,371</h4>
                                <p>Income</p>
                            </div>
                            <div class="stat-item">
                                <h4>258</h4>
                                <p>Sales</p>
                            </div>
                            <div class="stat-item">
                                <h4>3.6%</h4>
                                <p>Conversion Ratio</p>
                            </div>
                        </div>

                        <div class="chart-container">
                            <svg width="100%" height="100%">
                                <!-- Chart bars -->
                                <rect x="40" y="180" width="35" height="90" fill="#556ee6" opacity="0.8"/>
                                <rect x="120" y="210" width="35" height="60" fill="#556ee6" opacity="0.8"/>
                                <rect x="200" y="160" width="35" height="110" fill="#556ee6" opacity="0.8"/>
                                <rect x="280" y="150" width="35" height="120" fill="#556ee6" opacity="0.8"/>
                                <rect x="360" y="165" width="35" height="105" fill="#556ee6" opacity="0.8"/>
                                <rect x="440" y="130" width="35" height="140" fill="#556ee6" opacity="0.8"/>
                                <rect x="520" y="170" width="35" height="100" fill="#556ee6" opacity="0.8"/>
                                <rect x="600" y="140" width="35" height="130" fill="#556ee6" opacity="0.8"/>
                                <rect x="680" y="155" width="35" height="115" fill="#556ee6" opacity="0.8"/>
                                <rect x="760" y="150" width="35" height="120" fill="#556ee6" opacity="0.8"/>

                                <!-- Lines -->
                                <polyline points="40,100 120,110 200,90 280,120 360,100 440,95 520,110 600,85 680,100 760,110"
                                    fill="none" stroke="#f1b44c" stroke-width="2"/>
                                <polyline points="40,140 120,150 200,130 280,155 360,145 440,135 520,150 600,125 680,145 760,155"
                                    fill="none" stroke="#e9ecef" stroke-width="2"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="col-xl-4">
                    <div class="promo-banner">
                        <i class="fas fa-rocket promo-illustration"></i>
                        <h5>Enhance your Campaign for better outreach →</h5>
                        <button class="btn">Upgrade Account!</button>
                    </div>

                    <div class="analytics-card">
                        <div class="analytics-header">
                            <h5>Top Selling Products</h5>
                            <select class="dropdown-select">
                                <option>Yearly</option>
                            </select>
                        </div>

                        <div class="product-item">
                            <span class="product-name">Desktops</span>
                            <div class="product-bar">
                                <div class="product-bar-fill" style="width: 85%; background: #556ee6;"></div>
                            </div>
                        </div>

                        <div class="product-item">
                            <span class="product-name">iPhones</span>
                            <div class="product-bar">
                                <div class="product-bar-fill" style="width: 70%; background: #556ee6;"></div>
                            </div>
                        </div>

                        <div class="product-item">
                            <span class="product-name">Android</span>
                            <div class="product-bar">
                                <div class="product-bar-fill" style="width: 65%; background: #34c38f;"></div>
                            </div>
                        </div>

                        <div class="product-item">
                            <span class="product-name">Tablets</span>
                            <div class="product-bar">
                                <div class="product-bar-fill" style="width: 90%; background: #f1b44c;"></div>
                            </div>
                        </div>

                        <div class="product-item">
                            <span class="product-name">Cables</span>
                            <div class="product-bar">
                                <div class="product-bar-fill" style="width: 75%; background: #7367f0;"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bottom Row -->
            <div class="row">
                <div class="col-xl-4">
                    <div class="analytics-card">
                        <div class="analytics-header">
                            <h5>Top Users</h5>
                            <select class="dropdown-select">
                                <option>All Members</option>
                            </select>
                        </div>

                        <div class="user-item">
                            <img src="https://ui-avatars.com/api/?name=Glenn+Holden&background=667eea&color=fff" alt="User" class="user-avatar">
                            <div class="user-info">
                                <h6>Glenn Holden</h6>
                                <p><i class="fas fa-map-marker-alt"></i> Nevada</p>
                            </div>
                            <span class="user-badge badge-general">General</span>
                            <span class="user-amount">$250.00</span>
                        </div>

                        <div class="user-item">
                            <img src="https://ui-avatars.com/api/?name=Loita+Hamill&background=34c38f&color=fff" alt="User" class="user-avatar">
                            <div class="user-info">
                                <h6>Loita Hamill</h6>
                                <p><i class="fas fa-map-marker-alt"></i> Texas</p>
                            </div>
                            <span class="user-badge badge-success">Success</span>
                            <span class="user-amount">$110.00</span>
                        </div>

                        <div class="user-item">
                            <img src="https://ui-avatars.com/api/?name=Robert+Mercer&background=f46a6a&color=fff" alt="User" class="user-avatar">
                            <div class="user-info">
                                <h6>Robert Mercer</h6>
                                <p><i class="fas fa-map-marker-alt"></i> California</p>
                            </div>
                            <span class="user-badge badge-active">Active</span>
                            <span class="user-amount">$420.00</span>
                        </div>

                        <div class="user-item">
                            <img src="https://ui-avatars.com/api/?name=Marie+Kim&background=f1b44c&color=fff" alt="User" class="user-avatar">
                            <div class="user-info">
                                <h6>Marie Kim</h6>
                                <p><i class="fas fa-map-marker-alt"></i> Montana</p>
                            </div>
                            <span class="user-badge badge-pending">Pending</span>
                            <span class="user-amount">$120.00</span>
                        </div>

                        <div class="user-item">
                            <img src="https://ui-avatars.com/api/?name=Sohya+Henshaw&background=556ee6&color=fff" alt="User" class="user-avatar">
                            <div class="user-info">
                                <h6>Sohya Henshaw</h6>
                                <p><i class="fas fa-map-marker-alt"></i> Colorado</p>
                            </div>
                            <span class="user-badge badge-active">Active</span>
                            <span class="user-amount">$112.00</span>
                        </div>
                    </div>
                </div>

                <div class="col-xl-4">
                    <div class="analytics-card">
                        <div class="analytics-header">
                            <h5>Recent Activity</h5>
                            <select class="dropdown-select">
                                <option>Recent</option>
                            </select>
                        </div>

                        <div class="activity-item">
                            <div class="activity-icon"></div>
                            <div class="activity-content">
                                <div class="activity-time">Today, 12:20 pm</div>
                                <div class="activity-text">
                                    Andrei Coman magna sed porta finibus, risus posted a new article: <a href="#">Forget UX Rowland</a>
                                </div>
                            </div>
                        </div>

                        <div class="activity-item">
                            <div class="activity-icon"></div>
                            <div class="activity-content">
                                <div class="activity-time">22 Jul, 2020 12:36 pm</div>
                                <div class="activity-text">
                                    Andrei Coman posted a new article: <a href="#">Designer Alex</a>
                                </div>
                            </div>
                        </div>

                        <div class="activity-item">
                            <div class="activity-icon"></div>
                            <div class="activity-content">
                                <div class="activity-time">18 Jul, 2020 07:56 pm</div>
                                <div class="activity-text">
                                    Zack Wetass, sed porta finibus, risus Chris Wallace Commented <a href="#">Developer Moreno</a>
                                </div>
                            </div>
                        </div>

                        <div class="activity-item">
                            <div class="activity-icon"></div>
                            <div class="activity-content">
                                <div class="activity-time">10 Jul, 2020 08:42 pm</div>
                                <div class="activity-text">
                                    Zack Wetass, Chris combined Commented <a href="#">UX Murphy</a>
                                </div>
                            </div>
                        </div>

                        <div class="activity-item">
                            <div class="activity-icon"></div>
                            <div class="activity-content">
                                <div class="activity-time">23 Jun, 2020 12:22 pm</div>
                                <div class="activity-text">
                                    Zack Wetass, sed porta finibus, risus Chris Wallace Commented <a href="#">Developer Moreno</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-4">
                    <div class="analytics-card">
                        <div class="analytics-header">
                            <h5>Social Source</h5>
                            <select class="dropdown-select">
                                <option>Monthly</option>
                            </select>
                        </div>

                        <div class="row">
                            <div class="col-4">
                                <div class="social-item">
                                    <div class="social-icon facebook">
                                        <i class="fab fa-facebook-f"></i>
                                    </div>
                                    <div class="social-name">Facebook - 125 sales</div>
                                    <div class="social-sales">Maecenas nec odio et ante tincidunt tempus. Donec vitae sapien ut libero venenatis faucibus tincidunt.</div>
                                    <a href="#" style="font-size: 13px; color: var(--primary-color); text-decoration: none; margin-top: 10px; display: inline-block;">Learn more →</a>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="social-item">
                                    <div class="social-icon twitter">
                                        <i class="fab fa-twitter"></i>
                                    </div>
                                    <div class="social-name">Twitter</div>
                                    <div class="social-sales">112 sales</div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="social-item">
                                    <div class="social-icon instagram">
                                        <i class="fab fa-instagram"></i>
                                    </div>
                                    <div class="social-name">Instagram</div>
                                    <div class="social-sales">104 sales</div>
                                </div>
                            </div>
                        </div>

                        <div class="text-center mt-3">
                            <a href="#" style="font-size: 13px; color: var(--primary-color); text-decoration: none;">View All Sources →</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
