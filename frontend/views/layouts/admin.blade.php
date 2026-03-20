<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Automated Timetable Generator</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root{
            /* Minimal purple accents (LIGHT) */
            --app-bg: #f6f7fb;
            --app-bg-2: #eef1ff;
            --surface: rgba(255,255,255,.92);
            --surface-2: rgba(255,255,255,.78);
            --text: #0f172a;
            --muted: #64748b;
            --border: rgba(15,23,42,.10);
            --primary: #6d28d9;        /* purple */
            --primary-ink: #4c1d95;    /* deep purple */
            --primary-soft: rgba(109,40,217,.10);
            --shadow: 0 18px 55px rgba(2,6,23,.16);
            --shadow-sm: 0 10px 26px rgba(2,6,23,.12);
            --radius: 16px;
            --sidebar-bg: #0b0f1f;
            --sidebar-bg-2: #0f1632;
            --sidebar-ink: rgba(255,255,255,.84);
        }

        /* DARK THEME */
        [data-theme="dark"]{
            --app-bg: #070813;
            --app-bg-2: #0b0f24;
            --surface: rgba(15,17,35,.82);
            --surface-2: rgba(15,17,35,.68);
            --text: #eef2ff;
            --muted: rgba(226,232,240,.70);
            --border: rgba(255,255,255,.10);
            --primary: #a78bfa;
            --primary-ink: #c4b5fd;
            --primary-soft: rgba(167,139,250,.16);
            --shadow: 0 18px 55px rgba(0,0,0,.55);
            --shadow-sm: 0 10px 26px rgba(0,0,0,.40);
            --sidebar-bg: #050613;
            --sidebar-bg-2: #090b1f;
            --sidebar-ink: rgba(255,255,255,.86);
        }

        body {
            font-family: 'Inter', sans-serif;
            background:
                radial-gradient(1100px 650px at 12% 12%, var(--primary-soft), transparent 58%),
                radial-gradient(900px 600px at 88% 18%, rgba(2,6,23,.05), transparent 60%),
                linear-gradient(180deg, var(--app-bg-2) 0%, var(--app-bg) 48%, var(--app-bg) 100%);
            color: var(--text);
        }

        /* Ensure text color matches theme everywhere */
        html, body { color: var(--text); }
        .topbar, .main-content, .card, .table, .dropdown-menu { color: var(--text); }
        label, .form-label, .form-check-label { color: var(--text); }
        .form-control, .form-select, .input-group-text { color: var(--text); }

        [data-theme="dark"] .input-group-text{
            background: rgba(255,255,255,.06) !important;
            border-color: var(--border) !important;
            color: var(--text) !important;
        }
        [data-theme="dark"] .form-control,
        [data-theme="dark"] .form-select{
            background: rgba(255,255,255,.06) !important;
            border-color: var(--border) !important;
            color: var(--text) !important;
        }
        [data-theme="dark"] .form-control::placeholder{ color: rgba(226,232,240,.55); }
        /* Native select dropdown menu is OS-controlled; keep options readable */
        [data-theme="dark"] .form-select option{ color: #0f172a; }
        .wrapper {
            display: flex;
            width: 100%;
            align-items: stretch;
            min-height: 100vh;
        }
        #sidebar {
            min-width: 250px;
            max-width: 250px;
            background:
                radial-gradient(800px 500px at 30% 10%, rgba(255,255,255,.12), transparent 55%),
                linear-gradient(180deg, var(--sidebar-bg-2) 0%, var(--sidebar-bg) 60%, var(--sidebar-bg) 100%);
            color: #fff;
            transition: all 0.3s;
            box-shadow: var(--shadow-sm);
            z-index: 1000;
            border-right: 1px solid rgba(255,255,255,.08);
        }
        #sidebar .sidebar-header {
            padding: 20px;
            background: rgba(255, 255, 255, 0.06);
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,.08);
        }
        #sidebar .sidebar-header h4{
            letter-spacing: .2px;
            font-weight: 700;
        }
        #sidebar ul.components {
            padding: 20px 0;
        }
        #sidebar ul li a {
            padding: 12px 20px;
            font-size: 1.1em;
            display: block;
            color: var(--sidebar-ink);
            text-decoration: none;
            transition: all 0.3s;
            border-left: 4px solid transparent;
        }
        #sidebar ul li a:hover, #sidebar ul li.active > a {
            color: #fff;
            background: rgba(109,40,217,.26);
            border-left: 4px solid var(--primary);
        }
        #sidebar ul li a i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
            opacity: .95;
        }
        #content {
            width: 100%;
            padding: 0;
            min-height: 100vh;
            transition: all 0.3s;
            display: flex;
            flex-direction: column;
        }
        .topbar {
            background: var(--surface-2);
            backdrop-filter: blur(12px);
            padding: 15px 30px;
            box-shadow: 0 10px 30px rgba(2,6,23,.08);
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid var(--border);
        }
        .main-content {
            padding: 30px;
            flex-grow: 1;
        }
        .card {
            border: none;
            border-radius: var(--radius);
            box-shadow: var(--shadow-sm);
            transition: transform 0.3s ease;
            background: var(--surface);
            border: 1px solid var(--border);
        }
        .card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow);
        }
        .btn-primary {
            background: linear-gradient(135deg, var(--primary) 0%, #7c3aed 55%, #4c1d95 140%);
            border: none;
            box-shadow: 0 12px 28px rgba(109,40,217,.28);
        }
        .btn-primary:hover {
            filter: brightness(0.98);
            transform: translateY(-1px);
            box-shadow: 0 16px 34px rgba(109,40,217,.36);
        }
        .btn-outline-secondary{
            border-color: rgba(15,23,42,.18);
        }
        .btn-outline-secondary:hover{
            background: rgba(15,23,42,.06);
            border-color: rgba(15,23,42,.25);
        }
        .table{
            --bs-table-bg: transparent;
        }
        .table thead.table-light{
            --bs-table-bg: rgba(255,255,255,.55);
            border-bottom: 1px solid var(--border);
        }
        .table-hover tbody tr:hover{
            background: rgba(109,40,217,.08);
        }
        .badge{
            border-radius: 999px;
        }
        .alert{
            border: 1px solid var(--border);
            border-radius: 14px;
            box-shadow: 0 8px 18px rgba(2,6,23,.06);
        }
        .form-control, .form-select{
            border-radius: 12px;
            border-color: rgba(15,23,42,.12);
        }
        .form-control:focus, .form-select:focus{
            border-color: rgba(109,40,217,.60);
            box-shadow: 0 0 0 .25rem rgba(109,40,217,.18);
        }

        .theme-toggle{
            border: 1px solid rgba(15,23,42,.14);
            background: rgba(255,255,255,.55);
        }
        [data-theme="dark"] .theme-toggle{
            border-color: rgba(255,255,255,.14);
            background: rgba(255,255,255,.06);
            color: var(--text);
        }

        /* Bootstrap utility color fixes for dark theme */
        [data-theme="dark"] .text-muted{ color: var(--muted) !important; }
        [data-theme="dark"] .text-secondary{ color: var(--muted) !important; }
        [data-theme="dark"] .text-dark{ color: var(--text) !important; }
        [data-theme="dark"] .text-primary{ color: var(--primary-ink) !important; }
        [data-theme="dark"] .bg-white{ background-color: rgba(15,17,35,.85) !important; }
        [data-theme="dark"] .border-top,
        [data-theme="dark"] .border-bottom,
        [data-theme="dark"] .border{ border-color: var(--border) !important; }
        [data-theme="dark"] .table{ color: var(--text) !important; }
        [data-theme="dark"] .table-light{
            --bs-table-bg: rgba(255,255,255,.06) !important;
            --bs-table-border-color: var(--border) !important;
        }
        [data-theme="dark"] .btn-outline-secondary{
            color: var(--text) !important;
            border-color: rgba(255,255,255,.18) !important;
        }
        [data-theme="dark"] .btn-outline-secondary:hover{
            background: rgba(255,255,255,.08) !important;
            border-color: rgba(255,255,255,.26) !important;
        }
        [data-theme="dark"] a{ color: var(--primary-ink); }
        [data-theme="dark"] a:hover{ color: var(--primary); }
        @media (max-width: 768px) {
            #sidebar {
                margin-left: -250px;
                position: fixed;
                height: 100vh;
            }
            #sidebar.active {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <!-- Sidebar  -->
        <nav id="sidebar">
            <div class="sidebar-header">
                <i class="fas fa-calendar-alt fa-3x mb-2"></i>
                <h4>ATG System</h4>
            </div>

            <ul class="list-unstyled components">
                <li class="{{ request()->is('/') ? 'active' : '' }}">
                    <a href="{{ route('admin.dashboard') }}"><i class="fas fa-home"></i> Dashboard</a>
                </li>
                <li class="{{ request()->is('courses*') ? 'active' : '' }}">
                    <a href="{{ route('courses.index') }}"><i class="fas fa-graduation-cap"></i> Courses</a>
                </li>
                <li class="{{ request()->is('subjects*') ? 'active' : '' }}">
                    <a href="{{ route('subjects.index') }}"><i class="fas fa-book"></i> Subjects</a>
                </li>
                <li class="{{ request()->is('teachers*') ? 'active' : '' }}">
                    <a href="{{ route('teachers.index') }}"><i class="fas fa-chalkboard-teacher"></i> Teachers</a>
                </li>
                <li class="{{ request()->is('classrooms*') ? 'active' : '' }}">
                    <a href="{{ route('classrooms.index') }}"><i class="fas fa-door-open"></i> Classrooms</a>
                </li>
                <li class="{{ request()->is('timeslots*') ? 'active' : '' }}">
                    <a href="{{ route('timeslots.index') }}"><i class="fas fa-clock"></i> Timeslots</a>
                </li>
                <li class="{{ request()->is('faculty-availabilities*') ? 'active' : '' }}">
                    <a href="{{ route('faculty-availabilities.index') }}"><i class="fas fa-user-clock"></i> Faculty Availability</a>
                </li>
                <li class="{{ request()->is('timetable*') ? 'active' : '' }}">
                    <a href="{{ route('timetable.index') }}"><i class="fas fa-table"></i> Generate Timetable</a>
                </li>
            </ul>
        </nav>

        <!-- Page Content  -->
        <div id="content">
            <div class="topbar">
                <button type="button" id="sidebarCollapse" class="btn btn-primary d-md-none">
                    <i class="fas fa-align-left"></i>
                </button>
                <h4 class="m-0 text-muted">@yield('title', 'Admin Dashboard')</h4>
                <div class="d-flex align-items-center gap-2">
                    <button type="button" id="themeToggle" class="btn btn-sm theme-toggle" title="Toggle theme">
                        <i class="fas fa-moon"></i>
                    </button>
                    <span class="text-muted"><i class="fas fa-user-circle fa-lg"></i> {{ auth()->user()->name }}</span>
                    <form action="{{ route('logout') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-outline-secondary btn-sm">Logout</button>
                    </form>
                </div>
            </div>

            <div class="main-content">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                        <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i> {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @yield('content')
            </div>
            
            <!-- Footer -->
            <footer class="text-center p-3 text-muted mt-auto bg-white border-top">
                <small>&copy; {{ date('Y') }} Automated Timetable Generator. All Rights Reserved.</small>
            </footer>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        (function () {
            const saved = localStorage.getItem('atg_theme');
            if (saved === 'dark' || saved === 'light') {
                document.documentElement.setAttribute('data-theme', saved);
            } else if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
                document.documentElement.setAttribute('data-theme', 'dark');
            }
        })();

        document.addEventListener("DOMContentLoaded", function () {
            document.getElementById('sidebarCollapse').addEventListener('click', function () {
                document.getElementById('sidebar').classList.toggle('active');
            });

            const toggle = document.getElementById('themeToggle');
            if (toggle) {
                const updateIcon = () => {
                    const theme = document.documentElement.getAttribute('data-theme') || 'light';
                    toggle.innerHTML = theme === 'dark' ? '<i class="fas fa-sun"></i>' : '<i class="fas fa-moon"></i>';
                };
                updateIcon();

                toggle.addEventListener('click', () => {
                    const current = document.documentElement.getAttribute('data-theme') || 'light';
                    const next = current === 'dark' ? 'light' : 'dark';
                    document.documentElement.setAttribute('data-theme', next);
                    localStorage.setItem('atg_theme', next);
                    updateIcon();
                });
            }
        });
    </script>
    @yield('scripts')
</body>
</html>
