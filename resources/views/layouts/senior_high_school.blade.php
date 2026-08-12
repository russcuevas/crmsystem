<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'GNHS - SHS Teacher Portal')</title>

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
            --shs-primary: #7f1d1d;
            --shs-primary-dark: #450a0a;
            --shs-primary-light: #991b1b;
            --shs-accent: #f59e0b;
            --shs-accent-hover: #d97706;
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
            background: linear-gradient(180deg, #450a0a 0%, #7f1d1d 60%, #991b1b 100%);
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
            color: #f59e0b;
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
            color: #fca5a5;
            padding: 0.75rem 0.75rem 0.35rem 0.75rem;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 0.85rem;
            padding: 0.8rem 1rem;
            color: #fecdd3;
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
            background: #f59e0b;
            color: #450a0a;
            font-weight: 800;
            box-shadow: 0 4px 10px rgba(245, 158, 11, 0.35);
        }

        .nav-item.active i {
            color: #450a0a;
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

        .header-title {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .header-title h1 {
            font-size: 1.25rem;
            font-weight: 800;
            color: var(--text-main);
        }

        .level-badge {
            background: #fee2e2;
            color: #991b1b;
            padding: 0.25rem 0.65rem;
            border-radius: 0.375rem;
            font-size: 0.75rem;
            font-weight: 800;
            border: 1px solid #fca5a5;
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: 1.25rem;
        }

        .sy-indicator {
            background: #fef3c7;
            color: #92400e;
            padding: 0.4rem 0.85rem;
            border-radius: 9999px;
            font-size: 0.8rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            border: 1px solid #fde68a;
        }

        .user-profile-badge {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .avatar-circle {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: #7f1d1d;
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.9rem;
        }

        .user-details {
            display: flex;
            flex-direction: column;
        }

        .user-details .name {
            font-size: 0.85rem;
            font-weight: 700;
            color: var(--text-main);
        }

        .user-details .sub {
            font-size: 0.725rem;
            color: var(--text-muted);
        }

        .btn-logout {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #f87171;
            padding: 0.45rem 0.85rem;
            border-radius: 0.5rem;
            font-size: 0.8rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            gap: 0.4rem;
        }

        .btn-logout:hover {
            background: #ef4444;
            color: #ffffff;
            border-color: #dc2626;
        }

        /* Page Content Body */
        .content-body {
            padding: 2rem;
            flex: 1;
        }

        /* Footer */
        .app-footer {
            padding: 1.25rem 2rem;
            background: #ffffff;
            border-top: 1px solid var(--border-color);
            font-size: 0.8rem;
            color: var(--text-muted);
            text-align: center;
        }

        @media (max-width: 992px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.mobile-show {
                transform: translateX(0) !important;
            }

            .main-wrapper {
                margin-left: 0 !important;
                width: 100% !important;
            }

            .top-header {
                padding: 0.85rem 1.25rem;
            }
        }

        @media (max-width: 768px) {
            .top-header {
                padding: 0.75rem 1rem;
                flex-wrap: wrap;
                gap: 0.75rem;
            }

            .header-title {
                flex-wrap: wrap;
                gap: 0.5rem;
            }

            .header-title h1 {
                font-size: 1.05rem;
            }

            .header-actions {
                gap: 0.6rem;
                flex-wrap: wrap;
            }

            .user-details {
                display: none;
            }

            .content-body {
                padding: 1rem 0.75rem;
            }
        }
    </style>
    @stack('styles')
</head>

<body>
    <div class="app-wrapper">
        <!-- Sidebar Navigation -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <img src="{{ asset('assets/images/home/logo-school.png') }}" alt="GNHS Logo" class="sidebar-logo">
                <div class="brand-details">
                    <h2>GNHS-P SHS</h2>
                    <span>Teacher Portal</span>
                </div>
            </div>

            <nav class="sidebar-nav">
                <div class="nav-section-label">Main Menu</div>

                @php
                    $routeName = Route::currentRouteName();
                @endphp

                <!-- Dashboard -->
                <a href="{{ route('senior_high_school.dashboard.page') }}"
                    class="nav-item {{ $routeName == 'senior_high_school.dashboard.page' ? 'active' : '' }}">
                    <i class="fa-solid fa-gauge-high"></i>
                    <span>Dashboard</span>
                </a>

                <!-- Handled Students -->
                <a href="{{ route('senior_high_school.students.page') }}"
                    class="nav-item {{ str_contains($routeName, 'senior_high_school.students') ? 'active' : '' }}">
                    <i class="fa-solid fa-user-graduate"></i>
                    <span>My Students</span>
                </a>

                <!-- Enrollment -->
                <a href="{{ route('senior_high_school.enrollment.page') }}"
                    class="nav-item {{ str_contains($routeName, 'senior_high_school.enrollment') ? 'active' : '' }}">
                    <i class="fa-solid fa-user-plus"></i>
                    <span>Enrollment</span>
                </a>

                <!-- Class Records & Grades -->
                <a href="{{ route('senior_high_school.grades.page') }}"
                    class="nav-item {{ str_contains($routeName, 'senior_high_school.grades') ? 'active' : '' }}">
                    <i class="fa-solid fa-award"></i>
                    <span>Class Record & Grades</span>
                </a>
            </nav>

            <div class="sidebar-footer">
                <form action="{{ route('senior_high_school.logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn-logout" style="width: 100%; justify-content: center;">
                        <i class="fa-solid fa-right-from-bracket"></i>
                        <span>Logout</span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Wrapper -->
        <div class="main-wrapper">
            <!-- Top Header Bar -->
            <header class="top-header">
                <div class="header-title" style="display: flex; align-items: center; gap: 0.75rem;">
                    <button type="button" id="sidebarToggleBtn" onclick="toggleSidebar()"
                        style="background: #f1f5f9; border: 1.5px solid #cbd5e1; color: #7f1d1d; width: 38px; height: 38px; border-radius: 8px; display: inline-flex; align-items: center; justify-content: center; cursor: pointer; font-size: 1rem; transition: all 0.2s ease;"
                        title="Toggle Left Sidebar">
                        <i class="fa-solid fa-bars" id="sidebarToggleIcon"></i>
                    </button>
                    <h1>@yield('header_title', 'Senior High School Portal')</h1>
                </div>

                <div class="header-actions">
                    @php
                        $teacherObj =
                            isset($teacher) && $teacher
                                ? $teacher
                                : (Auth::check() && Auth::user()->teacher
                                    ? Auth::user()->teacher
                                    : null);
                    @endphp

                    @if ($teacherObj)
                        <div class="user-profile-badge">
                            <div class="avatar-circle">
                                {{ strtoupper(substr($teacherObj->first_name ?? 'T', 0, 1) . substr($teacherObj->last_name ?? 'C', 0, 1)) }}
                            </div>
                            <div class="user-details">
                                <span class="name">{{ $teacherObj->first_name ?? '' }}
                                    {{ $teacherObj->last_name ?? 'Teacher' }}</span>
                                <span class="sub">ID: {{ $teacherObj->teacher_id ?? 'N/A' }}</span>
                            </div>
                        </div>
                    @endif
                </div>
            </header>

            <!-- Main Content Area -->
            <main class="content-body">
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
                        localStorage.setItem('shs_sidebar_collapsed', 'true');
                        if (toggleIcon) toggleIcon.className = 'fa-solid fa-indent';
                    } else {
                        localStorage.setItem('shs_sidebar_collapsed', 'false');
                        if (toggleIcon) toggleIcon.className = 'fa-solid fa-bars';
                    }
                }
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            if (window.innerWidth > 992 && localStorage.getItem('shs_sidebar_collapsed') === 'true') {
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
