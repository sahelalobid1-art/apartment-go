<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'لوحة التحكم') | الإدارة</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <style>
        :root {
            --primary-color: #4361ee;
            --secondary-color: #3f37c9;
            --sidebar-bg: #fff;
            --sidebar-width: 260px;
            --bg-color: #f3f4f6;
            --text-color: #333;
        }

        body {
            font-family: 'Cairo', sans-serif;
            background-color: var(--bg-color);
            overflow-x: hidden;
        }

        /* Sidebar Styling */
        .sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            position: fixed;
            top: 0;
            right: 0;
            background: var(--sidebar-bg);
            box-shadow: -5px 0 15px rgba(0,0,0,0.05);
            z-index: 1000;
            transition: all 0.3s;
            padding-top: 1rem;
        }

        .sidebar-brand {
            padding: 0 1.5rem 1.5rem;
            border-bottom: 1px solid #f0f0f0;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .sidebar-brand h4 {
            font-weight: 700;
            color: var(--primary-color);
            margin: 0;
            font-size: 1.2rem;
        }

        .nav-link {
            color: #666;
            padding: 0.8rem 1.5rem;
            display: flex;
            align-items: center;
            gap: 12px;
            transition: all 0.3s;
            font-weight: 600;
            border-right: 4px solid transparent;
        }

        .nav-link:hover, .nav-link.active {
            color: var(--primary-color);
            background: #f8f9fa;
            border-right-color: var(--primary-color);
        }

        .nav-link i {
            width: 20px;
            text-align: center;
        }

        /* Main Content */
        .main-content {
            margin-right: var(--sidebar-width);
            padding: 2rem;
            transition: all 0.3s;
        }

        /* Navbar */
        .top-navbar {
            background: #fff;
            padding: 1rem 2rem;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.02);
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }

        /* Cards */
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.02);
            background: #fff;
            margin-bottom: 1.5rem;
        }

        .card-header {
            background: transparent;
            border-bottom: 1px solid #f0f0f0;
            padding: 1.25rem 1.5rem;
            font-weight: 700;
            color: #444;
        }

        /* Buttons & Badges */
        .btn {
            border-radius: 8px;
            padding: 0.5rem 1rem;
        }

        .badge {
            padding: 0.5em 0.8em;
            font-weight: 500;
            border-radius: 6px;
        }

        /* Mobile Responsive */
        @media (max-width: 991px) {
            .sidebar {
                margin-right: calc(var(--sidebar-width) * -1);
            }
            .sidebar.show {
                margin-right: 0;
            }
            .main-content {
                margin-right: 0;
            }
        }
    </style>
</head>
<body>

    <nav class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <i class="fas fa-building fa-lg text-primary"></i>
            <h4>إدارة العقارات</h4>
        </div>

        <div class="nav flex-column">
            <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="fas fa-th-large"></i>
                <span>لوحة التحكم</span>
            </a>

            <div class="text-muted small fw-bold px-4 mt-3 mb-2">المستخدمين</div>

            <a href="{{ route('admin.pending.users') }}" class="nav-link {{ request()->routeIs('admin.pending.users') ? 'active' : '' }}">
                <i class="fas fa-user-clock"></i>
                <span>طلبات معلقة</span>
                @php $pendingCount = \App\Models\User::where('status', 'pending')->count(); @endphp
                @if($pendingCount > 0)
                    <span class="badge bg-danger ms-auto rounded-pill">{{ $pendingCount }}</span>
                @endif
            </a>

            <a href="{{ route('admin.all.users') }}" class="nav-link {{ request()->routeIs('admin.all.users') ? 'active' : '' }}">
                <i class="fas fa-users"></i>
                <span>جميع المستخدمين</span>
            </a>

            <div class="text-muted small fw-bold px-4 mt-3 mb-2">النظام</div>

            <form action="{{ route('admin.logout') }}" method="POST" id="logout-form">
                @csrf
                <button type="submit" class="nav-link btn btn-link w-100 text-decoration-none text-start text-danger">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>تسجيل الخروج</span>
                </button>
            </form>
        </div>
    </nav>

    <main class="main-content" id="main-content">
        <header class="top-navbar">
            <div class="d-flex align-items-center">
                <button class="btn btn-light d-lg-none me-2" id="sidebar-toggle">
                    <i class="fas fa-bars"></i>
                </button>
                <h5 class="m-0 text-muted">@yield('page-title')</h5>
            </div>

            <div class="d-flex align-items-center gap-3">
                <div class="dropdown">
                    <button class="btn btn-light dropdown-toggle d-flex align-items-center gap-2" type="button" data-bs-toggle="dropdown">
                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                            {{ substr(Auth::guard('admin')->user()->first_name ?? 'A', 0, 1) }}
                        </div>
                        <span class="d-none d-md-block">{{ Auth::guard('admin')->user()->first_name ?? 'المدير' }}</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><button class="dropdown-item text-danger" onclick="document.getElementById('logout-form').submit()">تسجيل خروج</button></li>
                    </ul>
                </div>
            </div>
        </header>

        @yield('content')
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        // Toggle Sidebar on Mobile
        document.getElementById('sidebar-toggle').addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('show');
        });

        // Global Confirmation with SweetAlert
        function confirmAction(formId, message = 'هل أنت متأكد من هذا الإجراء؟') {
            Swal.fire({
                title: 'تأكيد',
                text: message,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'نعم، نفذ',
                cancelButtonText: 'إلغاء'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById(formId).submit();
                }
            });
        }

        // Show Success Messages
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'تم بنجاح!',
                text: "{{ session('success') }}",
                timer: 3000,
                showConfirmButton: false
            });
        @endif
    </script>
    @stack('scripts')
</body>
</html>
