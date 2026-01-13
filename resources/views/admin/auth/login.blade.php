<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل دخول المدير - إدارة العقارات</title>

    <!-- الخطوط -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap & Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            /* درجات الأزرق الخاصة بالداشبورد */
            --primary-color: #2563eb;       /* أزرق ساطع للأزرار */
            --primary-dark: #1e40af;        /* أزرق داكن عند التحويم */
            --overlay-color: rgba(30, 58, 138, 0.85); /* طبقة زرقاء داكنة شفافة للخلفية */
            --glass-bg: rgba(255, 255, 255, 0.95);
            --text-main: #1e293b;
            --text-muted: #64748b;
        }

        body {
            /* صورة خلفية تعبر عن العقارات/الشقق */
            background: url('https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?q=80&w=2070&auto=format&fit=crop') no-repeat center center fixed;
            background-size: cover;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Cairo', sans-serif;
            position: relative;
        }

        /* الطبقة الزرقاء فوق الصورة */
        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: var(--overlay-color);
            z-index: 0;
        }

        .login-card {
            background: var(--glass-bg);
            backdrop-filter: blur(8px); /* تغبيش الخلفية */
            border-radius: 20px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            width: 100%;
            max-width: 420px;
            padding: 40px;
            position: relative; /* لتكون فوق الطبقة الزرقاء */
            z-index: 1;
            border: 1px solid rgba(255, 255, 255, 0.5);
        }

        .brand-logo {
            width: 80px;
            height: 80px;
            background: #eff6ff;
            color: var(--primary-color);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 36px;
            margin: 0 auto 20px;
            border: 4px solid white;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .login-title {
            color: var(--text-main);
            font-weight: 700;
            font-size: 1.5rem;
        }

        .login-subtitle {
            color: var(--text-muted);
            font-size: 0.9rem;
            margin-bottom: 30px;
        }

        /* --- إصلاح مشكلة تداخل الأيقونة --- */
        .form-floating > .form-control {
            border-radius: 10px;
            border: 1px solid #e2e8f0;
            height: calc(3.5rem + 2px);
            /* الحشوة اليسرى 45px لإفساح المجال للأيقونة */
            padding: 1rem 0.75rem 1rem 45px;
            font-size: 0.95rem;
        }

        .form-floating > .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
        }

        .form-floating > label {
            padding-right: 1rem;
            color: var(--text-muted);
        }

        /* تصغير الليبل عند الكتابة */
        .form-floating > .form-control:focus ~ label,
        .form-floating > .form-control:not(:placeholder-shown) ~ label {
            color: var(--primary-color);
            transform: scale(0.85) translateY(-0.5rem) translateX(0.15rem);
        }

        /* تموضع الأيقونة */
        .input-icon {
            position: absolute;
            left: 15px; /* ثابتة على اليسار */
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 1.1rem;
            transition: color 0.3s;
            pointer-events: none; /* حتى لا تعيق النقر على الحقل */
        }

        .form-control:focus ~ .input-icon {
            color: var(--primary-color);
        }

        /* زر الدخول */
        .btn-login {
            background-color: var(--primary-color);
            border: none;
            border-radius: 10px;
            padding: 12px;
            color: white;
            font-weight: 700;
            width: 100%;
            margin-top: 15px;
            transition: all 0.3s;
            box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.3);
        }

        .btn-login:hover {
            background-color: var(--primary-dark);
            transform: translateY(-1px);
            box-shadow: 0 10px 15px -3px rgba(37, 99, 235, 0.4);
        }

        /* تحسين شكل التنبيهات */
        .alert {
            border-radius: 10px;
            font-size: 0.85rem;
            padding: 1rem;
        }

        /* زر التبديل */
        .form-check-input:checked {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .footer-text {
            margin-top: 30px;
            text-align: center;
            font-size: 0.8rem;
            color: #94a3b8;
        }
    </style>
</head>

<body>

    <div class="login-card">

        <!-- أيقونة تعبر عن العقارات -->
        <div class="brand-logo">
            <i class="fas fa-city"></i>
        </div>

        <div class="text-center mb-4">
            <h1 class="login-title">إدارة العقارات</h1>
            <p class="login-subtitle">مرحباً بك، يرجى تسجيل الدخول للمتابعة</p>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <ul class="mb-0 list-unstyled">
                    @foreach ($errors->all() as $error)
                        <li><i class="fas fa-circle-exclamation me-1"></i> {{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.login') }}">
            @csrf

            <!-- البريد الإلكتروني -->
            <div class="form-floating mb-3 position-relative">
                <!-- أضفنا dir="ltr" للإميل ليكون الكتابة صحيحة، أو يمكنك إزالته إذا أردت الكتابة من اليمين -->
                <input type="email" class="form-control" id="email" name="email"
                       value="{{ old('email') }}" required placeholder="name@example.com">
                <label for="email">البريد الإلكتروني</label>
                <!-- الأيقونة -->
                <i class="fas fa-envelope input-icon"></i>
            </div>

            <!-- كلمة المرور -->
            <div class="form-floating mb-4 position-relative">
                <input type="password" class="form-control" id="password" name="password"
                       required placeholder="Password">
                <label for="password">كلمة المرور</label>
                <i class="fas fa-lock input-icon"></i>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember"
                           {{ old('remember') ? 'checked' : '' }}>
                    <label class="form-check-label me-2" for="remember">تذكرني</label>
                </div>
                <!-- رابط نسيت كلمة المرور (اختياري) -->
                <!-- <a href="#" class="text-decoration-none small text-primary">نسيت كلمة المرور؟</a> -->
            </div>

            <button type="submit" class="btn btn-login">
                تسجيل الدخول
            </button>
        </form>

        <div class="footer-text">
            نظام إدارة الشقق السكنية © {{ date('Y') }}
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
