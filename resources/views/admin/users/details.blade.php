@extends('admin.layouts.app')

@section('title', 'ملف المستخدم')
@section('page-title', 'تفاصيل الحساب')

@section('content')
<div class="row">
    <div class="col-md-4 mb-4">
        <div class="card h-100 text-center p-4">
            <div class="card-body">
                <img src="{{ $user->profile_image ? asset('storage/'.$user->profile_image) : 'https://ui-avatars.com/api/?name='.$user->first_name.'&background=random' }}"
                     class="rounded-circle mb-3 shadow-sm" width="150" height="150" style="object-fit: cover; border: 4px solid #f8f9fa;">

                <h4 class="fw-bold">{{ $user->first_name }} {{ $user->last_name }}</h4>
                <p class="text-muted">{{ $user->email }}</p>

                <div class="d-flex justify-content-center gap-2 mt-3">
                    @if($user->status == 'pending')
                        <span class="badge bg-warning fs-6">معلق</span>
                    @elseif($user->status == 'approved')
                        <span class="badge bg-success fs-6">نشط</span>
                    @else
                        <span class="badge bg-danger fs-6">مرفوض</span>
                    @endif

                    <span class="badge bg-secondary fs-6">{{ $user->user_type == 'owner' ? 'مالك' : 'مستأجر' }}</span>
                </div>

                <hr class="my-4">

                <div class="d-grid gap-2">
                    @if($user->status == 'pending')
                        <form action="{{ route('admin.users.approve', $user->id) }}" method="POST">@csrf <button class="btn btn-success w-100">قبول الطلب</button></form>
                        <form action="{{ route('admin.users.reject', $user->id) }}" method="POST">@csrf <button class="btn btn-outline-danger w-100">رفض الطلب</button></form>
                    @endif
                    <form action="{{ route('admin.users.delete', $user->id) }}" method="POST" id="del-profile">
                        @csrf @method('DELETE')
                        <button type="button" onclick="confirmAction('del-profile', 'حذف المستخدم نهائياً؟')" class="btn btn-danger w-100"><i class="fas fa-trash me-2"></i>حذف الحساب</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-8 mb-4">
        <div class="card h-100">
            <div class="card-header bg-white">
                <h5 class="mb-0">بيانات الحساب والوثائق</h5>
            </div>
            <div class="card-body">
                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="text-muted small">الاسم الأول</label>
                        <div class="fw-bold fs-5">{{ $user->first_name }}</div>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small">الاسم الأخير</label>
                        <div class="fw-bold fs-5">{{ $user->last_name }}</div>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small">رقم الهاتف</label>
                        <div class="fw-bold fs-5">{{ $user->phone ?? 'غير متوفر' }}</div>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small">تاريخ الانضمام</label>
                        <div class="fw-bold fs-5">{{ $user->created_at->format('Y-m-d') }}</div>
                    </div>

                    <div class="col-12 mt-4">
                        <label class="text-muted small mb-2 d-block">صورة الهوية / الوثائق</label>
                        @if($user->id_image)
                            <div class="p-3 border rounded bg-light text-center">
                                <img src="{{ asset('storage/'.$user->id_image) }}" class="img-fluid rounded mb-2" style="max-height: 300px;">
                                <br>
                                <a href="{{ asset('storage/'.$user->id_image) }}" target="_blank" class="btn btn-sm btn-primary mt-2">
                                    <i class="fas fa-search-plus me-1"></i> عرض بالحجم الكامل
                                </a>
                            </div>
                        @else
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle me-2"></i> لم يتم رفع صورة للهوية
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
