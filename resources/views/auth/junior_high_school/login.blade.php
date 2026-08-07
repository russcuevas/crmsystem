<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Class Record Management System</title>

    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ asset('assets/images/home/logo.png') }}" type="image/x-icon">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/login.css') }}">
</head>

<body>
    <div class="login-wrapper">
        <!-- Left Banner Panel (Royal Purple Theme) -->
        <div class="left-panel theme-jhs">
            <div class="left-top">
                <img src="{{ asset('assets/images/home/logo.png') }}" alt="NAAP Logo" class="school-logo">
            </div>

            <div class="left-center">
                <span class="level-badge">Junior High School (Grade 7 - Grade 10)</span>
                <h1 class="school-title">National Aviation Academy of the Philippines</h1>
                <p class="school-subtitle">
                    <strong>WELCOME</strong> to the <strong>HOME</strong> of the <strong>AVIATORS</strong>
                </p>
            </div>

            <div class="left-footer">
                <span>&copy; {{ date('Y') }}</span>
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
                <p class="form-subtitle">Junior High School Portal (Grade 7 - Grade 10)</p>

                <form action="#" method="POST">
                    @csrf

                    <div class="form-group">
                        <label for="email" class="form-label">
                            Email Address <span class="required">*</span>
                        </label>
                        <input type="email" id="email" name="email" class="form-control"
                            placeholder="Email Address" required autofocus>
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
