<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Class Record Management System</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800;900&family=Fredoka:wght@600;700;800&display=swap"
        rel="stylesheet">
    <link rel="shortcut icon" href="{{ asset('assets/images/home/logo.png') }}" type="image/x-icon">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            min-height: 100vh;
            background-image: url("{{ asset('assets/images/home/background-home.jpg') }}");
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            align-items: center;
            padding: 2.5rem 1rem;
            color: #fff;
        }

        .header-nav {
            position: absolute;
            top: 20px;
            right: 30px;
            display: flex;
            gap: 12px;
            z-index: 10;
        }

        .header-nav a {
            color: #0c1c38;
            font-weight: 700;
            text-decoration: none;
            font-size: 0.9rem;
            padding: 8px 18px;
            border-radius: 6px;
            background: rgba(255, 255, 255, 0.5);
            backdrop-filter: blur(8px);
            transition: all 0.2s ease;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .header-nav a:hover {
            background: rgba(255, 255, 255, 0.9);
            transform: translateY(-1px);
        }

        .main-container {
            width: 100%;
            max-width: 500px;
            display: flex;
            flex-direction: column;
            align-items: center;
            margin: auto;
            text-align: center;
        }

        .brand-logo {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 4.8rem;
            font-weight: 900;
            color: #091333;
            letter-spacing: -2px;
            margin-bottom: 2.2rem;
            line-height: 1;
            user-select: none;
        }

        .buttons-group {
            display: flex;
            flex-direction: column;
            gap: 12px;
            width: 100%;
            max-width: 360px;
        }

        .school-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            background-color: #183153;
            color: rgb(245, 245, 245);
            font-weight: 800;
            font-size: 0.88rem;
            letter-spacing: 0.8px;
            text-transform: uppercase;
            padding: 13px 20px;
            border-radius: 6px;
            text-decoration: none;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .school-btn:hover {
            background-color: #72d9e3;
            transform: translateY(-2px) scale(1.02);
            box-shadow: 0 8px 22px rgba(0, 0, 0, 0.15);
            color: #051b28;
        }

        .school-btn svg {
            width: 20px;
            height: 20px;
            flex-shrink: 0;
            fill: currentColor;
        }

        .badge-footer {
            margin-top: 2.5rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }

        .celebration-badge {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            padding: 16px 26px;
            border-radius: 14px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            max-width: 300px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.12);
        }

        .badge-sub {
            font-size: 0.75rem;
            font-weight: 800;
            letter-spacing: 2px;
            color: #0a192f;
            text-transform: uppercase;
        }

        .badge-title {
            font-family: 'Fredoka', 'Plus Jakarta Sans', sans-serif;
            font-size: 1.55rem;
            font-weight: 800;
            line-height: 1.15;
            margin: 4px 0;
            color: #091333;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .badge-years {
            display: inline-block;
            background: #ef4444;
            color: #fff;
            font-size: 0.72rem;
            font-weight: 800;
            padding: 3px 10px;
            border-radius: 4px;
            margin-top: 4px;
            letter-spacing: 1px;
        }

        .badge-tag {
            font-size: 0.72rem;
            font-weight: 800;
            color: #091333;
            margin-top: 6px;
            letter-spacing: 1px;
            text-transform: uppercase;
        }
    </style>
</head>

<body>
    @if (Route::has('login'))
        <div class="header-nav">
            @auth
                <a href="{{ url('/home') }}">Home</a>
            @else
                <a href="{{ route('login') }}">Log in</a>
                @if (Route::has('register'))
                    <a href="{{ route('register') }}">Register</a>
                @endif
            @endauth
        </div>
    @endif

    <div class="main-container">

        <!-- School Level Buttons -->
        <div class="buttons-group">
            <a href="{{ route('elementary.login.page') }}" class="school-btn">
                <svg viewBox="0 0 24 24">
                    <path
                        d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-7 3c1.93 0 3.5 1.57 3.5 3.5S13.93 13 12 13s-3.5-1.57-3.5-3.5S10.07 6 12 6zm7 13H5v-.78c0-2.33 4.67-3.5 7-3.5s7 1.17 7 3.5v.78z" />
                </svg>
                Basic Education School <br> (Kinder - Grade 6)
            </a>

            <a href="{{ route('junior_high_school.login.page') }}" class="school-btn">
                <svg viewBox="0 0 24 24">
                    <path d="M12 3L1 9l11 6l9-4.91V17h2V9L12 3z M5 13.18v4L12 21l7-3.82v-4L12 17L5 13.18z" />
                </svg>
                Junior High School <br> (Grade 7 - Grade 10)
            </a>

            <a href="{{ route('senior_high_school.login.page') }}" class="school-btn">
                <svg viewBox="0 0 24 24">
                    <path d="M12 3L1 9l11 6l9-4.91V17h2V9L12 3z M5 13.18v4L12 21l7-3.82v-4L12 17L5 13.18z" />
                </svg>
                Senior High School <br> (Grade 11 - Grade 12)
            </a>

            <a href="{{ route('college.login.page') }}" class="school-btn">
                <svg viewBox="0 0 24 24">
                    <path
                        d="M4 6H2v14c0 1.1.9 2 2 2h14v-2H4V6zm16-4H8c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-1 9H9V9h10v2zm-4 4H9v-2h6v2zm4-8H9V5h10v2z" />
                </svg>
                Tertiary School <br> (First Year - Fifth Year)
            </a>
        </div>
    </div>
</body>

</html>
