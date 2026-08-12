<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'GNHS-P BED - Elementary Teacher Portal')</title>

    <!-- Google Fonts & Font Awesome Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- jQuery & SweetAlert2 -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        :root {
            --primary: #0d9488;
            --primary-dark: #0f766e;
            --primary-light: #ccfbf1;
            --sidebar-bg: #064e3b;
            --sidebar-hover: #047857;
            --sidebar-active: #059669;
            --bg-main: #f8fafc;
            --card-bg: #ffffff;
            --text-main: #0f172a;
            --text-sub: #64748b;
            --border: #e2e8f0;
            --radius-sm: 0.375rem;
            --radius-md: 0.625rem;
            --radius-lg: 1rem;
            --shadow-sm: 0 1px 3px rgba(0,0,0,0.05);
            --shadow-md: 0 4px 12px rgba(0,0,0,0.08);
            --transition: all 0.3s ease;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        body {
            background-color: var(--bg-main);
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .app-wrapper {
            display: flex;
            width: 100%;
            min-height: 100vh;
        }

        /* Sidebar Styling */
        .sidebar {
            width: 260px;
            background: var(--sidebar-bg);
            color: #ffffff;
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 100;
            transition: var(--transition);
        }

        .sidebar.collapsed {
            margin-left: -260px !important;
        }

        .sidebar-header {
            padding: 1.25rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .sidebar-logo {
            width: 42px;
            height: 42px;
            object-fit: contain;
        }

        .brand-details h2 {
            font-size: 1.1rem;
            font-weight: 800;
            line-height: 1.1;
            letter-spacing: 0.5px;
            color: #ffffff;
        }

        .brand-details span {
            font-size: 0.725rem;
            color: #a7f3d0;
            font-weight: 600;
            text-transform: uppercase;
        }

        .sidebar-nav {
            padding: 1rem 0.75rem;
            display: flex;
            flex-direction: column;
            gap: 0.35rem;
            flex: 1;
        }

        .nav-section-label {
            font-size: 0.68rem;
            font-weight: 800;
            color: #6ee7b7;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 0.5rem 0.75rem;
            margin-top: 0.5rem;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 1rem;
            color: #d1fae5;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.875rem;
            border-radius: var(--radius-md);
            transition: var(--transition);
        }

        .nav-link:hover {
            color: #ffffff;
            background: var(--sidebar-hover);
        }

        .nav-link.active {
            color: #ffffff;
            background: var(--sidebar-active);
            box-shadow: 0 4px 12px rgba(5, 150, 105, 0.3);
        }

        .nav-link i {
            font-size: 1.1rem;
            width: 22px;
            text-align: center;
        }

        .sidebar-footer {
            padding: 1rem;
            border-top: 1px solid rgba(255,255,255,0.1);
        }

        .btn-logout {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.65rem 1rem;
            background: rgba(239, 68, 68, 0.15);
            color: #fca5a5;
            border: 1px solid rgba(239, 68, 68, 0.3);
            border-radius: var(--radius-md);
            font-weight: 700;
            font-size: 0.85rem;
            cursor: pointer;
            text-decoration: none;
            transition: var(--transition);
        }

        .btn-logout:hover {
            background: #ef4444;
            color: #ffffff;
            border-color: #ef4444;
        }

        /* Main Content Layout */
        .main-wrapper {
            flex: 1;
            margin-left: 260px;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            transition: var(--transition);
        }

        .main-wrapper.sidebar-collapsed {
            margin-left: 0 !important;
            width: 100% !important;
        }

        /* Header Bar */
        .top-header {
            height: 70px;
            background: var(--card-bg);
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 2rem;
            position: sticky;
            top: 0;
            z-index: 90;
        }

        .header-title-box {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .header-title-box h1 {
            font-size: 1.25rem;
            font-weight: 800;
            color: var(--text-main);
        }

        .level-badge {
            background: var(--primary-light);
            color: var(--primary-dark);
            font-size: 0.725rem;
            font-weight: 800;
            padding: 0.25rem 0.65rem;
            border-radius: 9999px;
            text-transform: uppercase;
        }

        .user-profile-menu {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--primary-light);
            color: var(--primary-dark);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 1rem;
        }

        .user-details .name {
            font-size: 0.875rem;
            font-weight: 700;
            color: var(--text-main);
            display: block;
        }

        .user-details .sub {
            font-size: 0.75rem;
            color: var(--text-sub);
            display: block;
        }

        .page-content {
            padding: 2rem;
            flex: 1;
        }

        @media (max-width: 992px) {
            .sidebar {
                margin-left: -260px;
            }
            .sidebar.mobile-show {
                margin-left: 0 !important;
            }
            .main-wrapper {
                margin-left: 0;
            }
        }
    </style>
    @stack('styles')
</head>

<body>
    <div class="app-wrapper">
        <!-- Sidebar Navigation -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <img src="{{ asset('assets/images/home/logo-school.png') }}" alt="GNHS Logo" class="sidebar-logo">
                <div class="brand-details">
                    <h2>GNHS-P BED</h2>
                    <span>Teacher Portal</span>
                </div>
            </div>

            <nav class="sidebar-nav">
                <div class="nav-section-label">Main Menu</div>

                @php
                    $routeName = Route::currentRouteName();
                @endphp

                <!-- Dashboard -->
                <a href="{{ route('elementary.dashboard.page') }}" class="nav-link {{ Str::startsWith($routeName, 'elementary.dashboard') ? 'active' : '' }}">
                    <i class="fa-solid fa-chart-pie"></i>
                    <span>Dashboard</span>
                </a>

                <!-- My Students -->
                <a href="{{ route('elementary.students.page') }}" class="nav-link {{ Str::startsWith($routeName, 'elementary.students') ? 'active' : '' }}">
                    <i class="fa-solid fa-graduation-cap"></i>
                    <span>My Students</span>
                </a>

                <!-- Enrollment -->
                <a href="{{ route('elementary.enrollment.page') }}" class="nav-link {{ Str::startsWith($routeName, 'elementary.enrollment') ? 'active' : '' }}">
                    <i class="fa-solid fa-user-plus"></i>
                    <span>Enrollment</span>
                </a>

                <!-- Class Record & Grades -->
                <a href="{{ route('elementary.grades.page') }}" class="nav-link {{ Str::startsWith($routeName, 'elementary.grades') ? 'active' : '' }}">
                    <i class="fa-solid fa-award"></i>
                    <span>Class Record & Grades</span>
                </a>
            </nav>

            <div class="sidebar-footer">
                <form action="{{ route('elementary.logout') }}" method="POST" id="logout-form">
                    @csrf
                    <button type="submit" class="btn-logout">
                        <i class="fa-solid fa-right-from-bracket"></i>
                        <span>Logout</span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="main-wrapper">
            <header class="top-header">
                <div class="header-title-box">
                    <button type="button" id="sidebarToggleBtn" onclick="toggleSidebar()"
                        style="background: #f1f5f9; border: 1.5px solid #cbd5e1; color: #064e3b; width: 38px; height: 38px; border-radius: 8px; display: inline-flex; align-items: center; justify-content: center; cursor: pointer; font-size: 1rem; transition: all 0.2s ease;"
                        title="Toggle Left Sidebar">
                        <i class="fa-solid fa-bars" id="sidebarToggleIcon"></i>
                    </button>
                    <h1>@yield('header_title', 'Basic Education Portal')</h1>
                    <span class="level-badge">Kinder - Grade 6</span>
                </div>

                <div class="header-actions">
                    @php
                        $teacherObj = isset($teacher) && $teacher ? $teacher : (Auth::check() && Auth::user()->teacher ? Auth::user()->teacher : null);
                    @endphp

                    @if ($teacherObj)
                        <div class="user-profile-menu">
                            <div class="user-avatar">
                                {{ strtoupper(substr($teacherObj->first_name ?? 'T', 0, 1) . substr($teacherObj->last_name ?? 'C', 0, 1)) }}
                            </div>
                            <div class="user-details">
                                <span class="name">{{ $teacherObj->first_name ?? '' }} {{ $teacherObj->last_name ?? 'Teacher' }}</span>
                                <span class="sub">ID: {{ $teacherObj->teacher_id ?? 'N/A' }}</span>
                            </div>
                        </div>
                    @endif
                </div>
            </header>

            <main class="page-content">
                <script>
                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3500,
                        timerProgressBar: true,
                        didOpen: (toast) => {
                            toast.addEventListener('mouseenter', Swal.stopTimer)
                            toast.addEventListener('mouseleave', Swal.resumeTimer)
                        }
                    });

                    function showToast(icon, title) {
                        Toast.fire({
                            icon: icon,
                            title: title
                        });
                    }
                </script>

                @if (session('success'))
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            showToast('success', "{{ session('success') }}");
                        });
                    </script>
                @endif

                @if (session('error'))
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            showToast('error', "{{ session('error') }}");
                        });
                    </script>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    <script>
        function toggleSidebar() {
            const sidebar = document.querySelector('.sidebar');
            const mainWrapper = document.querySelector('.main-wrapper');
            const toggleIcon = document.getElementById('sidebarToggleIcon');

            if (window.innerWidth <= 992) {
                sidebar.classList.toggle('mobile-show');
            } else {
                if (sidebar && mainWrapper) {
                    sidebar.classList.toggle('collapsed');
                    mainWrapper.classList.toggle('sidebar-collapsed');

                    if (sidebar.classList.contains('collapsed')) {
                        localStorage.setItem('elementary_sidebar_collapsed', 'true');
                        if (toggleIcon) toggleIcon.className = 'fa-solid fa-indent';
                    } else {
                        localStorage.setItem('elementary_sidebar_collapsed', 'false');
                        if (toggleIcon) toggleIcon.className = 'fa-solid fa-bars';
                    }
                }
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            if (window.innerWidth > 992 && localStorage.getItem('elementary_sidebar_collapsed') === 'true') {
                const sidebar = document.querySelector('.sidebar');
                const mainWrapper = document.querySelector('.main-wrapper');
                const toggleIcon = document.getElementById('sidebarToggleIcon');
                if (sidebar && mainWrapper) {
                    sidebar.classList.add('collapsed');
                    mainWrapper.classList.add('sidebar-collapsed');
                    if (toggleIcon) toggleIcon.className = 'fa-solid fa-indent';
                }
            }
        });
    </script>

    @stack('scripts')
</body>
</html>
