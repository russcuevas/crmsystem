<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>GNHS - Super Admin Login</title>

    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ asset('assets/images/home/logo-school.png') }}" type="image/x-icon">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/login.css') }}">
    <style>
        .alert {
            padding: 0.85rem 1.1rem;
            border-radius: 8px;
            font-size: 0.875rem;
            font-weight: 500;
            margin-bottom: 1.25rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .alert-error {
            background-color: #fef2f2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        .alert-success {
            background-color: #ecfdf5;
            color: #065f46;
            border: 1px solid #a7f3d0;
        }
    </style>
</head>

<body>
    <div class="login-wrapper">
        <!-- Left Banner Panel (Midnight Slate Theme) -->
        <div class="left-panel theme-superadmin">
            <div class="left-top">
                <img src="{{ asset('assets/images/home/logo-school.png') }}" alt="GNHS Logo" class="school-logo">
            </div>

            <div class="left-center">
                <span class="level-badge">Super Admin Portal</span>
                <h1 class="school-title">Guilhulugan National High School</h1>
                <p class="school-subtitle">
                    <strong>GNHS</strong> Class Record Management System
                </p>
            </div>

            <div class="left-footer">
                <span>&copy; {{ date('Y') }} GNHS</span>
            </div>
        </div>

        <!-- Right Login Form Panel -->
        <div class="right-panel">
            <a href="{{ url('/') }}" class="home-btn" title="Back to Home">
                <svg viewBox="0 0 24 24">
                    <path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z" />
                </svg>
            </a>

            <div class="form-container">
                <h2 class="form-title">Login</h2>
                <p class="form-subtitle">Super Admin Access Portal</p>

                @if (session('error'))
                    <div class="alert alert-error">
                        <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>{{ session('error') }}</span>
                    </div>
                @endif

                @if (session('success'))
                    <div class="alert alert-success">
                        <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                <form action="{{ route('superadmin.login.submit') }}" method="POST">
                    @csrf

                    <div class="form-group">
                        <label for="email" class="form-label">
                            Email Address <span class="required">*</span>
                        </label>
                        <input type="email" id="email" name="email" class="form-control"
                            placeholder="Email Address" value="{{ old('email') }}" required autofocus>
                        <span class="form-help">Please enter your email address.</span>
                    </div>

                    <div class="form-group">
                        <label for="password" class="form-label">
                            Password <span class="required">*</span>
                        </label>
                        <input type="password" id="password" name="password" class="form-control"
                            placeholder="Password" required>
                        <span class="form-help">Please enter your password.</span>
                    </div>

                    <button type="submit" class="btn-login">
                        <svg viewBox="0 0 24 24">
                            <path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z" />
                        </svg>
                        Login
                    </button>

                    <div class="form-links">
                        <a href="#" class="link-muted">Forgot password?</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>

</html>
