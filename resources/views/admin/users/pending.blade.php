@extends('admin.layouts.app')

@section('title', 'الطلبات المعلقة')
@section('page-title', 'إدارة الطلبات المعلقة')

@section('content')
<div class="card">
    <div class="card-header bg-white py-3">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0 text-warning"><i class="fas fa-clock me-2"></i>طلبات بانتظار الموافقة</h5>
        </div>
    </div>
    <div class="card-body p-0">
        @if($users->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">المستخدم</th>
                            <th>النوع</th>
                            <th>تاريخ الطلب</th>
                            <th>الهوية</th>
                            <th class="text-end pe-4">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center gap-3">
                                        <img src="{{ $user->profile_image ? asset('storage/'.$user->profile_image) : 'https://ui-avatars.com/api/?name='.$user->first_name.'&background=random' }}"
                                             class="rounded-circle" width="45" height="45" style="object-fit: cover;">
                                        <div>
                                            <h6 class="mb-0 fw-bold">{{ $user->first_name }} {{ $user->last_name }}</h6>
                                            <small class="text-muted">{{ $user->email }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-info text-dark">
                                        {{ $user->user_type == 'owner' ? 'صاحب شقة' : 'مستأجر' }}
                                    </span>
                                </td>
                                <td>{{ $user->created_at->format('Y-m-d') }}</td>
                                <td>
                                    @if($user->id_image)
                                        <a href="{{ asset('storage/'.$user->id_image) }}" target="_blank" class="btn btn-sm btn-outline-secondary">
                                            <i class="fas fa-id-card me-1"></i> عرض
                                        </a>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-end pe-4">
                                    <div class="d-flex justify-content-end gap-2">
                                        <a href="{{ route('admin.users.details', $user->id) }}" class="btn btn-sm btn-light" title="تفاصيل">
                                            <i class="fas fa-eye"></i>
                                        </a>

                                        <form action="{{ route('admin.users.approve', $user->id) }}" method="POST" class="d-inline" id="approve-{{ $user->id }}">
                                            @csrf
                                            <button type="button" onclick="confirmAction('approve-{{ $user->id }}', 'هل أنت متأكد من قبول هذا المستخدم؟')" class="btn btn-sm btn-success text-white">
                                                <i class="fas fa-check"></i> قبول
                                            </button>
                                        </form>

                                        <form action="{{ route('admin.users.reject', $user->id) }}" method="POST" class="d-inline" id="reject-{{ $user->id }}">
                                            @csrf
                                            <button type="button" onclick="confirmAction('reject-{{ $user->id }}', 'سيتم رفض الطلب، هل أنت متأكد؟')" class="btn btn-sm btn-danger text-white">
                                                <i class="fas fa-times"></i> رفض
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="p-4">
                {{ $users->links('pagination::bootstrap-5') }}
            </div>
        @else
            <div class="text-center py-5">
                <img src="https://cdn-icons-png.flaticon.com/512/7486/7486747.png" width="120" class="mb-3 opacity-50">
                <h5 class="text-muted">لا توجد طلبات معلقة حالياً</h5>
            </div>
        @endif
    </div>
</div>
@endsection
