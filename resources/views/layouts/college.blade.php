<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'College Faculty Portal')</title>

    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ asset('assets/images/home/logo-school.png') }}" type="image/x-icon">

    <!-- Fonts & FontAwesome -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        :root {
            --college-primary: #0f172a;
            --college-primary-dark: #020617;
            --college-primary-light: #1e293b;
            --college-accent: #38bdf8;
            --college-accent-hover: #0284c7;
            --sidebar-width: 260px;
            --bg-canvas: #f8fafc;
            --card-bg: #ffffff;
            --text-main: #0f172a;
            --text-muted: #64748b;
            --border-color: #e2e8f0;
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg-canvas);
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
        }

        /* Layout Wrapper */
        .app-wrapper {
            display: flex;
            width: 100%;
            min-height: 100vh;
        }

        /* Sidebar Styling */
        .sidebar {
            width: var(--sidebar-width);
            background: linear-gradient(180deg, #020617 0%, #0f172a 60%, #1e293b 100%);
            color: #ffffff;
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 1000;
            box-shadow: 4px 0 15px rgba(0, 0, 0, 0.15);
            transition: transform 0.3s ease;
        }

        .sidebar.collapsed {
            transform: translateX(-100%);
        }

        .sidebar-header {
            padding: 1.5rem 1.25rem;
            display: flex;
            align-items: center;
            gap: 0.85rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.12);
        }

        .sidebar-logo {
            width: 42px;
            height: 42px;
            object-fit: contain;
        }

        .brand-details h2 {
            font-size: 1.1rem;
            font-weight: 800;
            color: #ffffff;
            line-height: 1.2;
        }

        .brand-details span {
            font-size: 0.725rem;
            color: #38bdf8;
            font-weight: 700;
            letter-spacing: 0.04em;
            text-transform: uppercase;
        }

        .sidebar-nav {
            padding: 1.25rem 0.85rem;
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 0.4rem;
            overflow-y: auto;
        }

        .nav-section-label {
            font-size: 0.68rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #94a3b8;
            padding: 0.75rem 0.75rem 0.35rem 0.75rem;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 0.85rem;
            padding: 0.8rem 1rem;
            color: #cbd5e1;
            text-decoration: none;
            border-radius: 0.625rem;
            font-size: 0.875rem;
            font-weight: 600;
            transition: all 0.2s ease;
        }

        .nav-item i {
            font-size: 1.1rem;
            width: 22px;
            text-align: center;
            transition: transform 0.2s ease;
        }

        .nav-item:hover {
            background: rgba(255, 255, 255, 0.12);
            color: #ffffff;
        }

        .nav-item:hover i {
            transform: translateX(2px);
        }

        .nav-item.active {
            background: #38bdf8;
            color: #020617;
            font-weight: 800;
            box-shadow: 0 4px 10px rgba(56, 189, 248, 0.35);
        }

        .nav-item.active i {
            color: #020617;
        }

        .sidebar-footer {
            padding: 1.25rem;
            border-top: 1px solid rgba(255, 255, 255, 0.12);
            background: rgba(0, 0, 0, 0.2);
        }

        /* Main Content Wrapper */
        .main-wrapper {
            margin-left: var(--sidebar-width);
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            width: calc(100% - var(--sidebar-width));
            transition: margin-left 0.3s ease, width 0.3s ease;
        }

        .main-wrapper.sidebar-collapsed {
            margin-left: 0 !important;
            width: 100% !important;
        }

        /* Header Bar */
        .top-header {
            background: #ffffff;
            border-bottom: 1px solid var(--border-color);
            padding: 1rem 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: var(--shadow-sm);
        }

        .menu-toggle {
            background: #f8fafc;
            border: 1px solid var(--border-color);
            color: var(--text-main);
            width: 38px;
            height: 38px;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .menu-toggle:hover {
            background: #f1f5f9;
            color: var(--college-accent-hover);
            border-color: #cbd5e1;
            transform: translateY(-1px);
            box-shadow: var(--shadow-sm);
        }

        .menu-toggle:active {
            transform: translateY(0);
        }

        .header-title-area h1 {
            font-size: 1.25rem;
            font-weight: 800;
            color: var(--college-primary);
        }

        .header-user-profile {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--college-primary);
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 1rem;
            box-shadow: 0 2px 6px rgba(15, 23, 42, 0.25);
        }

        .user-info {
            display: flex;
            flex-direction: column;
        }

        .user-name {
            font-size: 0.875rem;
            font-weight: 700;
            color: var(--text-main);
        }

        .user-role {
            font-size: 0.725rem;
            color: var(--college-accent-hover);
            font-weight: 600;
        }

        /* Page Body */
        .content-area {
            padding: 2rem;
            flex: 1;
        }
    </style>
    @stack('styles')
</head>

<body>
    <div class="app-wrapper">
        <!-- Sidebar Navigation -->
        <aside class="sidebar" id="appSidebar">
            <div class="sidebar-header">
                <img src="{{ asset('assets/images/home/logo-school.png') }}" alt="School Logo" class="sidebar-logo">
                <div class="brand-details">
                    <h2>NAAP College</h2>
                    <span>Tertiary Faculty Portal</span>
                </div>
            </div>

            <nav class="sidebar-nav">
                <div class="nav-section-label">Main Navigation</div>

                <a href="{{ route('college.dashboard.page') }}"
                    class="nav-item {{ request()->routeIs('college.dashboard.page') ? 'active' : '' }}">
                    <i class="fa-solid fa-gauge-high"></i>
                    <span>Dashboard</span>
                </a>

                <a href="{{ route('college.grades.page') }}"
                    class="nav-item {{ request()->routeIs('college.grades.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-pen-to-square"></i>
                    <span>Grading & Records</span>
                </a>

                <div class="nav-section-label">Student Management</div>

                <a href="{{ route('college.students.page') }}"
                    class="nav-item {{ request()->routeIs('college.students.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-user-graduate"></i>
                    <span>Students</span>
                </a>

                <a href="{{ route('college.enrollment.page') }}"
                    class="nav-item {{ request()->routeIs('college.enrollment.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-user-plus"></i>
                    <span>Enroll Students</span>
                </a>
            </nav>

            <div class="sidebar-footer">
                <form action="{{ route('college.logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="nav-item"
                        style="width: 100%; border: none; background: rgba(239, 68, 68, 0.15); color: #fca5a5; cursor: pointer; text-align: left;">
                        <i class="fa-solid fa-right-from-bracket" style="color: #fca5a5;"></i>
                        <span>Logout</span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content Wrapper -->
        <div class="main-wrapper" id="mainWrapper">
            <!-- Top Header Bar -->
            <header class="top-header">
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <button type="button" class="menu-toggle" id="menuToggle" onclick="toggleSidebar()"
                        title="Toggle Sidebar">
                        <svg width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                    <div class="header-title-area">
                        <h1>@yield('header_title', 'College Faculty Portal')</h1>
                    </div>
                </div>

                <div class="header-user-profile">
                    @php
                        $user = Auth::user();
                        $teacher = $user->teacher ?? null;
                        $initials = $teacher
                            ? strtoupper(substr($teacher->first_name, 0, 1) . substr($teacher->last_name, 0, 1))
                            : 'CP';
                        $fullName = $teacher
                            ? $teacher->first_name . ' ' . $teacher->last_name
                            : $user->name ?? 'College Instructor';
                    @endphp
                    <div class="user-avatar">{{ $initials }}</div>
                    <div class="user-info">
                        <span class="user-name">Prof. {{ $fullName }}</span>
                        <span class="user-role">College Faculty</span>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="content-area">
                @yield('content')
            </main>
        </div>
    </div>

    <script>
        // Sidebar collapse toggle with localStorage persistence
        function toggleSidebar() {
            const sidebar = document.getElementById('appSidebar');
            const mainWrapper = document.getElementById('mainWrapper');
            sidebar.classList.toggle('collapsed');
            mainWrapper.classList.toggle('sidebar-collapsed');

            const isCollapsed = sidebar.classList.contains('collapsed');
            localStorage.setItem('college_sidebar_collapsed', isCollapsed ? 'true' : 'false');
        }

        document.addEventListener('DOMContentLoaded', function() {
            if (localStorage.getItem('college_sidebar_collapsed') === 'true') {
                const sidebar = document.getElementById('appSidebar');
                const mainWrapper = document.getElementById('mainWrapper');
                if (sidebar && mainWrapper) {
                    sidebar.classList.add('collapsed');
                    mainWrapper.classList.add('sidebar-collapsed');
                }
            }
        });

        // SweetAlert2 Toast Mixin (Top-Right End)
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3500,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer);
                toast.addEventListener('mouseleave', Swal.resumeTimer);
            }
        });

        @if (session('success'))
            Toast.fire({
                icon: 'success',
                title: @json(session('success'))
            });
        @endif

        @if (session('error'))
            Toast.fire({
                icon: 'error',
                title: @json(session('error'))
            });
        @endif
    </script>
    @stack('scripts')
</body>

</html>
