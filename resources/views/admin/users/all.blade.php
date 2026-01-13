@extends('admin.layouts.app')

@section('title', 'جميع المستخدمين')
@section('page-title', 'سجل المستخدمين')

@section('content')
<div class="card mb-4">
    <div class="card-body">
        <form action="{{ route('admin.all.users') }}" method="GET" class="row g-3 align-items-center">
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control border-start-0 ps-0" placeholder="بحث بالاسم أو البريد..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-3">
                <select name="type" class="form-select">
                    <option value="">كل الأنواع</option>
                    <option value="owner" {{ request('type') == 'owner' ? 'selected' : '' }}>أصحاب الشقق</option>
                    <option value="tenant" {{ request('type') == 'tenant' ? 'selected' : '' }}>مستأجرين</option>
                </select>
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">كل الحالات</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>نشط (Approved)</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>معلق (Pending)</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>مرفوض (Rejected)</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">بحث وتصفية</button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">المستخدم</th>
                        <th>الدور</th>
                        <th>الحالة</th>
                        <th>تاريخ التسجيل</th>
                        <th class="text-end pe-4">خيارات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center gap-3">
                                    <img src="{{ $user->profile_image ? asset('storage/'.$user->profile_image) : 'https://ui-avatars.com/api/?name='.$user->first_name.'&background=random' }}"
                                         class="rounded-circle" width="40" height="40" style="object-fit: cover;">
                                    <div>
                                        <div class="fw-bold">{{ $user->first_name }} {{ $user->last_name }}</div>
                                        <div class="small text-muted">{{ $user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge {{ $user->user_type == 'owner' ? 'bg-primary' : 'bg-info' }}">
                                    {{ $user->user_type == 'owner' ? 'مالك' : 'مستأجر' }}
                                </span>
                            </td>
                            <td>
                                @if($user->status == 'approved')
                                    <span class="badge bg-success-subtle text-success border border-success-subtle">نشط</span>
                                @elseif($user->status == 'pending')
                                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle">قيد المراجعة</span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle">مرفوض</span>
                                @endif
                            </td>
                            <td class="text-muted">{{ $user->created_at->format('Y/m/d') }}</td>
                            <td class="text-end pe-4">
                                <div class="dropdown">
                                    <button class="btn btn-light btn-sm" type="button" data-bs-toggle="dropdown">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end border-0 shadow">
                                        <li><a class="dropdown-item" href="{{ route('admin.users.details', $user->id) }}"><i class="fas fa-eye me-2 text-primary"></i> تفاصيل</a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <form action="{{ route('admin.users.delete', $user->id) }}" method="POST" id="del-{{ $user->id }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" onclick="confirmAction('del-{{ $user->id }}', 'هذا الإجراء لا يمكن التراجع عنه!')" class="dropdown-item text-danger">
                                                    <i class="fas fa-trash-alt me-2"></i> حذف نهائي
                                                </button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="p-4">
            {{ $users->withQueryString()->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
@endsection
