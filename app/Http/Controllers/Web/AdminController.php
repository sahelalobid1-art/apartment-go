<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function showLoginForm()
    {
        return view('admin.auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::guard('admin')->attempt($credentials, $request->remember)) {
            $user = Auth::guard('admin')->user();

            if ($user->user_type !== 'admin') {
                Auth::guard('admin')->logout();
                return redirect()->back()->withErrors(['email' => 'غير مصرح بالدخول']);
            }

            if ($user->status !== 'approved') {
                Auth::guard('admin')->logout();
                return redirect()->back()->withErrors(['email' => 'الحساب غير مفعل']);
            }

            return redirect()->route('admin.dashboard');
        }

        return redirect()->back()->withErrors(['email' => 'بيانات الدخول غير صحيحة']);
    }

    public function dashboard()
    {
        $stats = [
            'total_users' => User::where('user_type', '!=', 'admin')->count(),
            'pending' => User::where('status', 'pending')->count(),
            'approved' => User::where('status', 'approved')->where('user_type', '!=', 'admin')->count(),
            'rejected' => User::where('status', 'rejected')->count(),
            'owners' => User::where('user_type', 'owner')->where('status', 'approved')->count(),
            'tenants' => User::where('user_type', 'tenant')->where('status', 'approved')->count(),
        ];

        $recentUsers = User::where('user_type', '!=', 'admin')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $registrationData = $this->getRegistrationStats(7);

        return view('admin.dashboard', compact('stats', 'recentUsers', 'registrationData'));
    }

    public function pendingUsers()
    {
        $users = User::where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.users.pending', compact('users'));
    }

    public function allUsers(Request $request)
    {
        $query = User::where('user_type', '!=', 'admin');

        // تطبيق الفلاتر
        if ($request->has('type') && $request->type != '') {
            $query->where('user_type', $request->type);
        }
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }
        if ($request->has('search') && $request->search != '') {
            $query->where(function($q) use ($request) {
                $q->where('first_name', 'like', '%' . $request->search . '%')
                  ->orWhere('last_name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(10);

        $stats = [
            'total' => User::where('user_type', '!=', 'admin')->count(),
            'owners' => User::where('user_type', 'owner')->count(),
            'tenants' => User::where('user_type', 'tenant')->count(),
        ];

        return view('admin.users.all', compact('users', 'stats'));
    }

    public function userDetails($id)
    {
        $user = User::findOrFail($id);
        return view('admin.users.details', compact('user'));
    }

    public function approveUser($id)
    {
        $user = User::findOrFail($id);
        $user->update(['status' => 'approved']);
        return redirect()->back()->with('success', 'تم قبول المستخدم بنجاح');
    }

    public function rejectUser($id)
    {
        $user = User::findOrFail($id);
        $user->update(['status' => 'rejected']);
        return redirect()->back()->with('success', 'تم رفض المستخدم');
    }

    public function deleteUser($id)
    {
        $user = User::findOrFail($id);

        if ($user->profile_image) Storage::disk('public')->delete($user->profile_image);
        if ($user->id_image) Storage::disk('public')->delete($user->id_image);

        $user->delete();
        return redirect()->route('admin.all.users')->with('success', 'تم حذف المستخدم نهائياً');
    }

    public function logout()
    {
        Auth::guard('admin')->logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect()->route('admin.login');
    }

    private function getRegistrationStats($days)
    {
        $labels = [];
        $data = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $labels[] = $date->format('Y-m-d');
            $data[] = User::whereDate('created_at', $date->format('Y-m-d'))
                          ->where('user_type', '!=', 'admin')
                          ->count();
        }

        return ['labels' => $labels, 'data' => $data];
    }
}
