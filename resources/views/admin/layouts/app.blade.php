<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="The Indonesian Press - Admin Dashboard" />
    <meta name="author" content="The Indonesian Press" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', 'Admin') - The Indonesian Press</title>
    
    <!-- Styles -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    
    <!-- Custom styles -->
    <style>
        :root {
            --sidebar-width: 260px;
            --topnav-height: 56px;
            --primary-color: #243c82;
            --secondary-color: #e63946;
            --success-color: #28a745;
            --warning-color: #ffc107;
            --info-color: #17a2b8;
        }
        
        body {
            margin: 0;
            font-family: 'Nunito', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background-color: #f5f7fb;
        }
        
        #wrapper {
            display: flex;
        }
        
        #sidebar-wrapper {
            position: fixed;
            top: 0;
            left: 0;
            width: var(--sidebar-width);
            height: 100vh;
            z-index: 999;
            background-color: var(--primary-color);
            color: rgba(255, 255, 255, 0.85);
            transition: all 0.3s;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(33, 40, 50, 0.15);
            overflow-y: auto;
        }
        
        #sidebar-wrapper .sidebar-heading {
            padding: 1rem;
            font-size: 1.2rem;
            font-weight: 700;
            text-align: center;
            color: white;
            background-color: rgba(0, 0, 0, 0.1);
        }
        
        #sidebar-wrapper .list-group {
            width: var(--sidebar-width);
            padding-top: 1rem;
        }
        
        #sidebar-wrapper .list-group-item {
            border: none;
            padding: 0.8rem 1.25rem;
            background-color: transparent;
            color: rgba(255, 255, 255, 0.85);
            font-size: 0.9rem;
        }
        
        #sidebar-wrapper .list-group-item:hover, 
        #sidebar-wrapper .list-group-item.active {
            background-color: rgba(255, 255, 255, 0.1);
            color: white;
        }
        
        #sidebar-wrapper .list-group-item i {
            margin-right: 0.5rem;
            width: 1.25rem;
            text-align: center;
        }
        
        #sidebar-wrapper .sidebar-submenu {
            padding-left: 1rem;
        }
        
        #sidebar-wrapper .sidebar-submenu .list-group-item {
            padding: 0.5rem 1.25rem;
        }
        
        .sidebar-divider {
            height: 0;
            margin: 1rem 0;
            border-top: 1px solid rgba(255, 255, 255, 0.15);
        }
        
        .sidebar-heading {
            padding: 0 1rem;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.75rem;
            color: rgba(255, 255, 255, 0.6);
        }
        
        #content-wrapper {
            width: 100%;
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        #topbar {
            height: var(--topnav-height);
            background-color: white;
            padding: 0 1.5rem;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(33, 40, 50, 0.15);
            z-index: 100;
            position: fixed;
            top: 0;
            right: 0;
            left: var(--sidebar-width);
        }
        
        #main-content {
            margin-top: var(--topnav-height);
            padding: 1.5rem;
            flex: 1 0 auto;
        }
        
        .btn-toggle-sidebar {
            color: var(--primary-color);
            background: transparent;
            border: none;
            padding: 0.5rem;
        }
        
        .navbar-nav .dropdown-menu {
            position: absolute;
        }
        
        .dropdown-user .dropdown-menu {
            min-width: 13rem;
        }
        
        .dropdown-user .dropdown-header {
            font-weight: 700;
        }
        
        .dropdown-user .dropdown-item i {
            width: 1.25rem;
            text-align: center;
            margin-right: 0.5rem;
        }
        
        footer.footer {
            padding: 1rem;
            font-size: 0.85rem;
            background-color: white;
            border-top: 1px solid #dee2e6;
        }
        
        @media (max-width: 992px) {
            #sidebar-wrapper {
                margin-left: -var(--sidebar-width);
            }
            
            #sidebar-wrapper.active {
                margin-left: 0;
            }
            
            #content-wrapper {
                margin-left: 0;
            }
            
            #topbar {
                left: 0;
            }
            
            #sidebar-wrapper.active + #content-wrapper #topbar {
                left: var(--sidebar-width);
            }
        }
        
        .card {
            box-shadow: 0 0.15rem 1.75rem 0 rgba(33, 40, 50, 0.15);
            border: none;
        }
        
        .card-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid rgba(33, 40, 50, 0.125);
            font-weight: 600;
        }
        
        .table > :not(caption) > * > * {
            padding: 0.75rem;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .btn-primary:hover {
            background-color: #1b2e63;
            border-color: #1b2e63;
        }
        
        .bg-primary {
            background-color: var(--primary-color) !important;
        }

        #page-loader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 9999;
            background-color: rgba(255, 255, 255, 0.7);
            display: flex;
            justify-content: center;
            align-items: center;
        }
        
        .notification-badge {
            position: absolute;
            top: 0.25rem;
            right: 0.25rem;
            font-size: 0.7rem;
        }
    </style>
    
    @yield('styles')
</head>

<body>
    <!-- Page Loader -->
    <div id="page-loader">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>
    
    <div id="wrapper">
        <!-- Sidebar -->
        <div id="sidebar-wrapper">
            <div class="sidebar-heading">
                <img src="{{ asset('images/logo.png') }}" alt="The Indonesian Press" height="40">
                <div>The Indonesian Press</div>
            </div>
            
            <div class="list-group list-group-flush">
                <a href="{{ route('admin.dashboard') }}" class="list-group-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
                
                <div class="sidebar-divider"></div>
                <div class="sidebar-heading">Content Management</div>
                
                <a href="{{ route('admin.articles.index') }}" class="list-group-item {{ request()->routeIs('admin.articles.*') ? 'active' : '' }}">
                    <i class="bi bi-file-earmark-text"></i> Articles
                </a>
                
                <a href="{{ route('admin.articles.moderation') }}" class="list-group-item {{ request()->routeIs('admin.articles.moderation') ? 'active' : '' }}">
                    <i class="bi bi-shield-check"></i> Moderation
                    @if($pendingModeration ?? 0 > 0)
                    <span class="badge bg-danger rounded-pill float-end">{{ $pendingModeration }}</span>
                    @endif
                </a>
                
                <a href="{{ route('admin.categories.index') }}" class="list-group-item {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                    <i class="bi bi-folder"></i> Categories
                </a>
                
                <a href="{{ route('admin.tags.index') }}" class="list-group-item {{ request()->routeIs('admin.tags.*') ? 'active' : '' }}">
                    <i class="bi bi-tags"></i> Tags
                </a>
                
                <a href="{{ route('admin.comments.index') }}" class="list-group-item {{ request()->routeIs('admin.comments.*') ? 'active' : '' }}">
                    <i class="bi bi-chat-left-text"></i> Comments
                </a>
                
                <div class="sidebar-divider"></div>
                <div class="sidebar-heading">Analytics</div>
                
                <a href="{{ route('admin.article.analytics') }}" class="list-group-item {{ request()->routeIs('admin.article.analytics') ? 'active' : '' }}">
                    <i class="bi bi-graph-up"></i> Article Analytics
                </a>
                
                <div class="sidebar-divider"></div>
                <div class="sidebar-heading">User Management</div>
                
                <a href="{{ route('admin.users.index') }}" class="list-group-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                    <i class="bi bi-people"></i> Users
                </a>
                
                <div class="sidebar-divider"></div>
                <div class="sidebar-heading">System</div>
                
                <a href="{{ route('admin.activity.logs') }}" class="list-group-item {{ request()->routeIs('admin.activity.logs') ? 'active' : '' }}">
                    <i class="bi bi-list-check"></i> Activity Logs
                </a>
                
                <a href="{{ route('admin.error.logs') }}" class="list-group-item {{ request()->routeIs('admin.error.logs') ? 'active' : '' }}">
                    <i class="bi bi-exclamation-triangle"></i> Error Logs
                </a>
            </div>
        </div>
        
        <!-- Content Wrapper -->
        <div id="content-wrapper">
            <!-- Top Navigation -->
            <nav id="topbar" class="navbar navbar-expand navbar-light bg-white">
                <!-- Sidebar Toggle Button -->
                <button class="btn btn-toggle-sidebar me-2" id="sidebarToggle">
                    <i class="bi bi-list fs-5"></i>
                </button>
                
                <!-- Search Form -->
                <form class="d-none d-md-inline-block form-inline ms-auto me-0 me-md-3 my-2 my-md-0">
                    <div class="input-group">
                        <input class="form-control" type="text" placeholder="Search for..." aria-label="Search for..." aria-describedby="btnNavbarSearch" />
                        <button class="btn btn-primary" id="btnNavbarSearch" type="button"><i class="bi bi-search"></i></button>
                    </div>
                </form>
                
                <!-- Top Navbar -->
                <ul class="navbar-nav ms-auto ms-md-0 me-3 me-lg-4">
                    <!-- Notifications Dropdown -->
                    <li class="nav-item dropdown mx-1">
                        <a class="nav-link dropdown-toggle" id="alertsDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-bell fs-5"></i>
                            <span class="badge bg-danger notification-badge">3</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="alertsDropdown">
                            <li><h6 class="dropdown-header">Notifications Center</h6></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="#">
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <div class="bg-primary text-white rounded-circle p-2"><i class="bi bi-file-earmark-text"></i></div>
                                    </div>
                                    <div>
                                        <div class="small text-muted">Today</div>
                                        New article pending review
                                    </div>
                                </div>
                            </a></li>
                            <li><a class="dropdown-item" href="#">
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <div class="bg-success text-white rounded-circle p-2"><i class="bi bi-chat-left-text"></i></div>
                                    </div>
                                    <div>
                                        <div class="small text-muted">Today</div>
                                        5 new comments pending approval
                                    </div>
                                </div>
                            </a></li>
                            <li><a class="dropdown-item" href="#">
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <div class="bg-warning text-white rounded-circle p-2"><i class="bi bi-exclamation-triangle"></i></div>
                                    </div>
                                    <div>
                                        <div class="small text-muted">Yesterday</div>
                                        System update completed
                                    </div>
                                </div>
                            </a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-center small text-muted" href="#">View All Notifications</a></li>
                        </ul>
                    </li>
                    
                    <!-- User Dropdown -->
                    <li class="nav-item dropdown dropdown-user">
                        <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            @if(Auth::user()->profile_picture)
                                <img src="{{ asset('storage/' . Auth::user()->profile_picture) }}" alt="Profile" class="rounded-circle" width="32" height="32">
                            @else
                                <i class="bi bi-person-circle fs-5"></i>
                            @endif
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                            <li>
                                <div class="dropdown-header">
                                    <div class="fw-bold">{{ Auth::user()->name }}</div>
                                    <div class="small text-muted">Administrator</div>
                                </div>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="{{ route('admin.profile') }}"><i class="bi bi-person"></i> Profile</a></li>
                            <li><a class="dropdown-item" href="{{ route('admin.password.form') }}"><i class="bi bi-shield-lock"></i> Change Password</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form action="{{ route('admin.logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="dropdown-item"><i class="bi bi-box-arrow-right"></i> Logout</button>
                                </form>
                            </li>
                        </ul>
                    </li>
                </ul>
            </nav>
            
            <!-- Main Content -->
            <main id="main-content">
                @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif
                
                @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif
                
                @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif
                
                @yield('content')
            </main>
            
            <!-- Footer -->
            <footer class="footer mt-auto">
                <div class="container-fluid">
                    <div class="d-flex align-items-center justify-content-between small">
                        <div class="text-muted">&copy; The Indonesian Press {{ date('Y') }}</div>
                        <div>
                            <a href="#">Privacy Policy</a>
                            &middot;
                            <a href="#">Terms &amp; Conditions</a>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>
    
    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    
    <script>
        // Page loader
        window.addEventListener('load', function() {
            document.getElementById('page-loader').style.display = 'none';
        });
        
        // Toggle sidebar
        document.getElementById('sidebarToggle').addEventListener('click', function() {
            document.getElementById('sidebar-wrapper').classList.toggle('active');
        });
        
        $(document).ready(function() {
            $('.datatable').DataTable({
                responsive: true
            });
            
            setTimeout(function() {
                $('.alert-dismissible').alert('close');
            }, 5000);
        });
    </script>
    
    @yield('scripts')
</body>
</html>