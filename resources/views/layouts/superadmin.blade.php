<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'GNHS - Super Admin Dashboard')</title>

    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ asset('assets/images/home/logo-school.png') }}" type="image/x-icon">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">

    <!-- Custom Super Admin CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/superadmin-dashboard.css') }}">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @stack('styles')
</head>

<body>
    <div class="app-container">
        <!-- Sidebar Navigation -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <img src="{{ asset('assets/images/home/logo-school.png') }}" alt="GNHS Logo" class="sidebar-logo">
                <div class="brand-info">
                    <span class="brand-title">GNHS</span>
                    <span class="brand-subtitle">Class Record System</span>
                </div>
            </div>

            <nav class="sidebar-nav">
                <div class="nav-section-label">System Overview</div>

                @php
                    $routeName = Route::currentRouteName();
                    $selectedLevel = request('level') ?? (isset($student) && isset($student->educationLevel) ? $student->educationLevel->code : (isset($student) && isset($student->gradeLevel->educationLevel) ? $student->gradeLevel->educationLevel->code : null));
                @endphp

                <!-- All Levels Overview -->
                <a href="{{ route('superadmin.dashboard.page') }}"
                    class="nav-link {{ $routeName == 'superadmin.dashboard.page' && empty($selectedLevel) ? 'active' : '' }}">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                    </svg>
                    <span>Dashboard</span>
                </a>

                <!-- School Year Management -->
                <a href="{{ route('superadmin.school_years.page') }}"
                    class="nav-link {{ $routeName == 'superadmin.school_years.page' ? 'active' : '' }}">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span>School Year</span>
                </a>

                <div class="nav-section-label" style="margin-top: 0.75rem;">Education Levels</div>

                @php
                    $levels = [
                        ['code' => 'BED', 'name' => 'Basic Education'],
                        ['code' => 'JHS', 'name' => 'Junior High School'],
                        ['code' => 'SHS', 'name' => 'Senior High School'],
                        ['code' => 'COLLEGE', 'name' => 'College Level'],
                    ];
                @endphp

                @foreach ($levels as $lvl)
                    @php
                        $isCurrentLevel = $selectedLevel == $lvl['code'];
                        $isSemestralLevel = in_array($lvl['code'], ['SHS', 'COLLEGE']);
                        $selectedSem = request('semester');
                        $currentRouteName = Route::currentRouteName();
                        $navRouteTarget = in_array($currentRouteName, ['superadmin.students.show'])
                            ? 'superadmin.students.page'
                            : $currentRouteName ?? 'superadmin.dashboard.page';
                    @endphp
                    <div class="nav-dropdown-group {{ $isCurrentLevel ? 'open' : '' }}"
                        id="dropdown-{{ strtolower($lvl['code']) }}">
                        <button type="button" class="nav-dropdown-toggle {{ $isCurrentLevel ? 'active open' : '' }}"
                            onclick="toggleSidebarDropdown('dropdown-{{ strtolower($lvl['code']) }}')">
                            <div style="display: flex; align-items: center; gap: 0.5rem; overflow: hidden;">
                                <span class="level-badge-tag">{{ $lvl['code'] }}</span>
                                <span
                                    style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $lvl['name'] }}</span>
                            </div>
                            <svg class="chevron" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <div class="nav-dropdown-menu">
                            @if ($isSemestralLevel)
                                <!-- Semester Filter Sub-Header ONLY for SHS & College -->
                                <div
                                    style="display: flex; gap: 4px; padding: 4px; background: rgba(255,255,255,0.05); border-radius: 8px; margin-bottom: 6px;">
                                    <a href="{{ route($navRouteTarget, array_filter(['level' => $lvl['code'], 'semester' => '1st Semester'])) }}"
                                        style="flex: 1; text-align: center; padding: 4px 0; font-size: 0.7rem; font-weight: 700; border-radius: 6px; text-decoration: none; color: {{ empty($selectedSem) || $selectedSem == '1st Semester' ? '#ffffff' : '#94a3b8' }}; background: {{ empty($selectedSem) || $selectedSem == '1st Semester' ? 'var(--primary-navy)' : 'transparent' }}; border: 1px solid {{ empty($selectedSem) || $selectedSem == '1st Semester' ? 'var(--accent-gold)' : 'transparent' }};">
                                        1st Sem
                                    </a>
                                    <a href="{{ route($navRouteTarget, array_filter(['level' => $lvl['code'], 'semester' => '2nd Semester'])) }}"
                                        style="flex: 1; text-align: center; padding: 4px 0; font-size: 0.7rem; font-weight: 700; border-radius: 6px; text-decoration: none; color: {{ $selectedSem == '2nd Semester' ? '#ffffff' : '#94a3b8' }}; background: {{ $selectedSem == '2nd Semester' ? 'var(--primary-navy)' : 'transparent' }}; border: 1px solid {{ $selectedSem == '2nd Semester' ? 'var(--accent-gold)' : 'transparent' }};">
                                        2nd Sem
                                    </a>
                                </div>
                            @else
                                <!-- Quarter Filter Sub-Header ONLY for BED & JHS -->
                                @php
                                    $selectedQtr = request('academic_period');
                                @endphp
                                <div
                                    style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 3px; padding: 4px; background: rgba(255,255,255,0.05); border-radius: 8px; margin-bottom: 6px;">
                                    <a href="{{ route($navRouteTarget, array_filter(['level' => $lvl['code'], 'academic_period' => '1st Quarter'])) }}"
                                        style="text-align: center; padding: 4px 0; font-size: 0.62rem; font-weight: 700; border-radius: 6px; text-decoration: none; color: {{ empty($selectedQtr) || $selectedQtr == '1st Quarter' ? '#ffffff' : '#94a3b8' }}; background: {{ empty($selectedQtr) || $selectedQtr == '1st Quarter' ? 'var(--primary-navy)' : 'transparent' }}; border: 1px solid {{ empty($selectedQtr) || $selectedQtr == '1st Quarter' ? 'var(--accent-gold)' : 'transparent' }};">
                                        1st Qtr
                                    </a>
                                    <a href="{{ route($navRouteTarget, array_filter(['level' => $lvl['code'], 'academic_period' => '2nd Quarter'])) }}"
                                        style="text-align: center; padding: 4px 0; font-size: 0.62rem; font-weight: 700; border-radius: 6px; text-decoration: none; color: {{ $selectedQtr == '2nd Quarter' ? '#ffffff' : '#94a3b8' }}; background: {{ $selectedQtr == '2nd Quarter' ? 'var(--primary-navy)' : 'transparent' }}; border: 1px solid {{ $selectedQtr == '2nd Quarter' ? 'var(--accent-gold)' : 'transparent' }};">
                                        2nd Qtr
                                    </a>
                                    <a href="{{ route($navRouteTarget, array_filter(['level' => $lvl['code'], 'academic_period' => '3rd Quarter'])) }}"
                                        style="text-align: center; padding: 4px 0; font-size: 0.62rem; font-weight: 700; border-radius: 6px; text-decoration: none; color: {{ $selectedQtr == '3rd Quarter' ? '#ffffff' : '#94a3b8' }}; background: {{ $selectedQtr == '3rd Quarter' ? 'var(--primary-navy)' : 'transparent' }}; border: 1px solid {{ $selectedQtr == '3rd Quarter' ? 'var(--accent-gold)' : 'transparent' }};">
                                        3rd Qtr
                                    </a>
                                    <a href="{{ route($navRouteTarget, array_filter(['level' => $lvl['code'], 'academic_period' => '4th Quarter'])) }}"
                                        style="text-align: center; padding: 4px 0; font-size: 0.62rem; font-weight: 700; border-radius: 6px; text-decoration: none; color: {{ $selectedQtr == '4th Quarter' ? '#ffffff' : '#94a3b8' }}; background: {{ $selectedQtr == '4th Quarter' ? 'var(--primary-navy)' : 'transparent' }}; border: 1px solid {{ $selectedQtr == '4th Quarter' ? 'var(--accent-gold)' : 'transparent' }};">
                                        4th Qtr
                                    </a>
                                </div>
                            @endif

                            <!-- Dashboard -->
                            <a href="{{ route('superadmin.dashboard.page', array_filter(['level' => $lvl['code'], 'semester' => $isSemestralLevel ? $selectedSem ?? '1st Semester' : null, 'academic_period' => !$isSemestralLevel ? $selectedQtr ?? '1st Quarter' : null])) }}"
                                class="sub-nav-link {{ $routeName == 'superadmin.dashboard.page' && $selectedLevel == $lvl['code'] ? 'active' : '' }}">
                                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                                </svg>
                                <span>Dashboard</span>
                            </a>

                            <!-- Accounts -->
                            <a href="{{ route('superadmin.accounts.page', array_filter(['level' => $lvl['code'], 'semester' => $isSemestralLevel ? $selectedSem ?? '1st Semester' : null, 'academic_period' => !$isSemestralLevel ? $selectedQtr ?? '1st Quarter' : null])) }}"
                                class="sub-nav-link {{ $routeName == 'superadmin.accounts.page' && $selectedLevel == $lvl['code'] ? 'active' : '' }}">
                                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                                <span>Accounts</span>
                            </a>

                            <!-- Subject List -->
                            <a href="{{ route('superadmin.subjects.page', array_filter(['level' => $lvl['code'], 'semester' => $isSemestralLevel ? $selectedSem ?? '1st Semester' : null, 'academic_period' => !$isSemestralLevel ? $selectedQtr ?? '1st Quarter' : null])) }}"
                                class="sub-nav-link {{ $routeName == 'superadmin.subjects.page' && $selectedLevel == $lvl['code'] ? 'active' : '' }}">
                                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                </svg>
                                <span>Subject List</span>
                            </a>

                            <!-- Faculty Information -->
                            <a href="{{ route('superadmin.faculty.page', array_filter(['level' => $lvl['code'], 'semester' => $isSemestralLevel ? $selectedSem ?? '1st Semester' : null, 'academic_period' => !$isSemestralLevel ? $selectedQtr ?? '1st Quarter' : null])) }}"
                                class="sub-nav-link {{ $routeName == 'superadmin.faculty.page' && $selectedLevel == $lvl['code'] ? 'active' : '' }}">
                                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                                </svg>
                                <span>Faculty Information</span>
                            </a>

                            <!-- Students -->
                            <a href="{{ route('superadmin.students.page', array_filter(['level' => $lvl['code'], 'semester' => $isSemestralLevel ? $selectedSem ?? '1st Semester' : null, 'academic_period' => !$isSemestralLevel ? $selectedQtr ?? '1st Quarter' : null])) }}"
                                class="sub-nav-link {{ ($routeName == 'superadmin.students.page' || $routeName == 'superadmin.students.show') && $selectedLevel == $lvl['code'] ? 'active' : '' }}">
                                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5z" />
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0112 20.055a11.952 11.952 0 01-6.824-2.998 12.078 12.078 0 01-6.824-2.998L12 14z" />
                                </svg>
                                <span>Students</span>
                            </a>

                            <!-- Manage Section -->
                            <a href="{{ route('superadmin.sections.page', array_filter(['level' => $lvl['code'], 'semester' => $isSemestralLevel ? $selectedSem ?? '1st Semester' : null, 'academic_period' => !$isSemestralLevel ? $selectedQtr ?? '1st Quarter' : null])) }}"
                                class="sub-nav-link {{ $routeName == 'superadmin.sections.page' && $selectedLevel == $lvl['code'] ? 'active' : '' }}">
                                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m0 0h4m-4 0V9a2 2 0 012-2h2a2 2 0 012 2v12" />
                                </svg>
                                <span>Manage Section</span>
                            </a>

                            <!-- Assigned Subjects -->
                            <a href="{{ route('superadmin.assigned_subjects.page', array_filter(['level' => $lvl['code'], 'semester' => $isSemestralLevel ? $selectedSem ?? '1st Semester' : null, 'academic_period' => !$isSemestralLevel ? $selectedQtr ?? '1st Quarter' : null])) }}"
                                class="sub-nav-link {{ $routeName == 'superadmin.assigned_subjects.page' && $selectedLevel == $lvl['code'] ? 'active' : '' }}">
                                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                </svg>
                                <span>Assigned Subjects</span>
                            </a>

                            <!-- Enroll Students -->
                            <a href="{{ route('superadmin.enrollment.page', array_filter(['level' => $lvl['code'], 'semester' => $isSemestralLevel ? $selectedSem ?? '1st Semester' : null, 'academic_period' => !$isSemestralLevel ? $selectedQtr ?? '1st Quarter' : null])) }}"
                                class="sub-nav-link {{ $routeName == 'superadmin.enrollment.page' && $selectedLevel == $lvl['code'] ? 'active' : '' }}">
                                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                                </svg>
                                <span>Enroll Students</span>
                            </a>

                            <!-- Class Record & Grades -->
                            <a href="{{ route('superadmin.grades.page', array_filter(['level' => $lvl['code'], 'semester' => $isSemestralLevel ? $selectedSem ?? '1st Semester' : null, 'academic_period' => !$isSemestralLevel ? $selectedQtr ?? '1st Quarter' : null])) }}"
                                class="sub-nav-link {{ $routeName == 'superadmin.grades.page' && $selectedLevel == $lvl['code'] ? 'active' : '' }}">
                                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002-2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                                <span>Class Record & Grades</span>
                            </a>
                        </div>
                    </div>
                @endforeach
            </nav>

            <div class="sidebar-footer">
                <div class="user-profile-card">
                    <div class="user-avatar">
                        {{ strtoupper(substr(Auth::user()->name ?? 'SA', 0, 2)) }}
                    </div>
                    <div class="user-details">
                        <div class="user-name">{{ Auth::user()->name ?? 'Super Admin' }}</div>
                        <div class="user-role">Super Admin</div>
                    </div>
                    <form action="{{ route('superadmin.logout') }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn-logout-icon" title="Logout">
                            <svg width="18" height="18" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- Main Workspace -->
        <main class="main-content">
            <!-- Top Navbar -->
            <header class="top-navbar">
                <div class="navbar-left">
                    <button class="menu-toggle" id="menuToggle">
                        <svg width="24" height="24" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                    <div class="search-box">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <input type="text" placeholder="Search accounts, subjects, faculty...">
                    </div>
                </div>

                <div class="navbar-right">
                    <form action="{{ route('superadmin.school_year.switch') }}" method="POST"
                        class="school-year-form" id="schoolYearForm">
                        @csrf
                        <div class="school-year-dropdown-wrapper" title="Select Active School Year">
                            <span class="status-dot"></span>
                            <span class="sy-prefix">S.Y.</span>
                            <select name="school_year_id" class="school-year-select"
                                onchange="document.getElementById('schoolYearForm').submit();">
                                @if (isset($allSchoolYears) && $allSchoolYears->count() > 0)
                                    @foreach ($allSchoolYears as $sy)
                                        <option value="{{ $sy->id }}" {{ $sy->is_active ? 'selected' : '' }}>
                                            {{ $sy->school_year }} {{ $sy->is_active ? '(Active)' : '' }}
                                        </option>
                                    @endforeach
                                @else
                                    <option value="">{{ $activeSchoolYear->school_year ?? '2024-2025' }}
                                        (Active)</option>
                                @endif
                            </select>
                            <svg class="dropdown-chevron" width="14" height="14" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                    </form>

                    <button class="nav-icon-btn" title="Notifications">
                        <svg width="20" height="20" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                        <span class="notification-dot"></span>
                    </button>
                </div>
            </header>

            <!-- Page Body -->
            <div class="page-container">
                @if (Route::currentRouteName() == 'superadmin.dashboard.page')
                    <!-- Welcome Hero Banner -->
                    <div class="welcome-banner">
                        <div class="welcome-text">
                            <h1>Welcome back, <span>{{ Auth::user()->name ?? 'Super Admin' }}</span>!</h1>
                            <p>Guilhulugan National High School (GNHS) -
                                {{ request('level') ? request('level') . ' Level Overview' : 'Class Record Management System Overview' }}
                            </p>
                        </div>

                        <div class="banner-quick-stats">
                            <div class="quick-stat-item">
                                <div class="quick-stat-value">{{ $totalFaculty ?? 0 }}</div>
                                <div class="quick-stat-label">Teachers</div>
                            </div>
                            <div class="quick-stat-item">
                                <div class="quick-stat-value">{{ $totalStudents ?? 0 }}</div>
                                <div class="quick-stat-label">Students</div>
                            </div>
                        </div>
                    </div>

                    <!-- Stats Cards Grid -->
                    <div class="stats-grid">
                        <!-- Faculty Info -->
                        <div class="stat-card">
                            <div class="stat-icon-wrapper blue">
                                <svg width="24" height="24" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                                </svg>
                            </div>
                            <div class="stat-info">
                                <span class="stat-number">{{ $totalFaculty ?? 0 }}</span>
                                <span class="stat-title">Teachers</span>
                            </div>
                        </div>

                        <!-- Students -->
                        <div class="stat-card">
                            <div class="stat-icon-wrapper emerald">
                                <svg width="24" height="24" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 14l9-5-9-5-9 5 9 5z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0112 20.055a11.952 11.952 0 01-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
                                </svg>
                            </div>
                            <div class="stat-info">
                                <span class="stat-number">{{ $totalStudents ?? 0 }}</span>
                                <span class="stat-title">Enrolled Students</span>
                            </div>
                        </div>

                        <!-- Subject List -->
                        <div class="stat-card">
                            <div class="stat-icon-wrapper purple">
                                <svg width="24" height="24" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                </svg>
                            </div>
                            <div class="stat-info">
                                <span class="stat-number">{{ $totalSubjects ?? 0 }}</span>
                                <span class="stat-title">Subject List</span>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Main Content Yield -->
                @yield('content')
            </div>
        </main>
    </div>

    <!-- JavaScript for Mobile Sidebar Toggle & Dropdown Accordions -->
    <script>
        function toggleSidebarDropdown(groupId) {
            const group = document.getElementById(groupId);
            if (!group) return;
            const toggle = group.querySelector('.nav-dropdown-toggle');
            group.classList.toggle('open');
            if (toggle) {
                toggle.classList.toggle('open');
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const menuToggle = document.getElementById('menuToggle');
            const sidebar = document.getElementById('sidebar');

            if (menuToggle && sidebar) {
                menuToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('open');
                });
            }
        });

        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer);
                toast.addEventListener('mouseleave', Swal.resumeTimer);
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
                showToast('success', @json(session('success')));
            });
        </script>
    @endif
    @if (session('error'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                showToast('error', @json(session('error')));
            });
        </script>
    @endif
    @if (session('warning'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                showToast('warning', @json(session('warning')));
            });
        </script>
    @endif
    @if (session('info'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                showToast('info', @json(session('info')));
            });
        </script>
    @endif

    @stack('scripts')
</body>

</html>
