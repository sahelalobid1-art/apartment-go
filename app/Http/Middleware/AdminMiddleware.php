<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // تصحيح 1: يجب فحص الـ guard الخاص بالأدمن تحديداً
        if (!Auth::guard('admin')->check()) {
            return redirect()->route('admin.login');
        }

        // تصحيح 2: جلب المستخدم من الـ guard الصحيح
        $user = Auth::guard('admin')->user();

        if ($user->user_type !== 'admin') {
            // تسجيل الخروج من الأدمن فقط
            Auth::guard('admin')->logout();
            return redirect()->route('admin.login')->withErrors([
                'email' => 'غير مصرح بالدخول إلى لوحة التحكم'
            ]);
        }

        if ($user->status !== 'approved') {
            Auth::guard('admin')->logout();
            return redirect()->route('admin.login')->withErrors([
                'email' => 'الحساب غير مفعل'
            ]);
        }

        return $next($request);
    }
}
