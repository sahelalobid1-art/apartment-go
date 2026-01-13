@extends('admin.layouts.app')

@section('title', 'الرئيسية')
@section('page-title', 'نظرة عامة')

@section('content')
<style>
    /* ... (CSS Styles from previous response, unchanged) ... */
    .stat-card { transition: transform 0.3s; }
    .stat-card:hover { transform: translateY(-5px); }
    .icon-box { width: 50px; height: 50px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; }
    .bg-soft-primary { background: rgba(67, 97, 238, 0.1); color: #4361ee; }
    .bg-soft-warning { background: rgba(247, 185, 36, 0.1); color: #f7b924; }
    .bg-soft-success { background: rgba(16, 185, 129, 0.1); color: #10b981; }
    .bg-soft-danger { background: rgba(239, 68, 68, 0.1); color: #ef4444; }
</style>

<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card stat-card h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="icon-box bg-soft-primary">
                    <i class="fas fa-users"></i>
                </div>
                <div>
                    <h6 class="text-muted mb-1">إجمالي المستخدمين</h6>
                    <h4 class="mb-0 fw-bold">{{ $stats['total_users'] }}</h4>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card stat-card h-100 border-warning border-bottom border-3">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="icon-box bg-soft-warning">
                    <i class="fas fa-clock"></i>
                </div>
                <div>
                    <h6 class="text-muted mb-1">طلبات معلقة</h6>
                    <h4 class="mb-0 fw-bold">{{ $stats['pending'] }}</h4>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card stat-card h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="icon-box bg-soft-success">
                    <i class="fas fa-key"></i>
                </div>
                <div>
                    <h6 class="text-muted mb-1">أصحاب شقق</h6>
                    <h4 class="mb-0 fw-bold">{{ $stats['owners'] }}</h4>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card stat-card h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="icon-box bg-soft-primary">
                    <i class="fas fa-home"></i>
                </div>
                <div>
                    <h6 class="text-muted mb-1">مستأجرون</h6>
                    <h4 class="mb-0 fw-bold">{{ $stats['tenants'] }}</h4>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="fas fa-chart-line me-2"></i>إحصائيات التسجيل (آخر 7 أيام)</span>
            </div>
            <div class="card-body">
                <div style="position: relative; height: 350px;">
                    <canvas id="registrationChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="fas fa-user-plus me-2"></i>أحدث المسجلين</span>
                <a href="{{ route('admin.all.users') }}" class="btn btn-sm btn-light">عرض الكل</a>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    @forelse($recentUsers as $user)
                        <li class="list-group-item d-flex align-items-center gap-3 py-3">
                            <img src="{{ $user->profile_image ? asset('storage/'.$user->profile_image) : 'https://ui-avatars.com/api/?name='.$user->first_name.'&background=random' }}"
                                 class="rounded-circle" width="40" height="40" alt="avatar">
                            <div class="flex-grow-1">
                                <h6 class="mb-0 text-dark">{{ $user->first_name }} {{ $user->last_name }}</h6>
                                <small class="text-muted">{{ $user->created_at->diffForHumans() }}</small>
                            </div>
                            <span class="badge {{ $user->status == 'pending' ? 'bg-warning' : ($user->status == 'approved' ? 'bg-success' : 'bg-danger') }}">
                                {{ $user->status == 'pending' ? 'معلق' : ($user->status == 'approved' ? 'نشط' : 'مرفوض') }}
                            </span>
                        </li>
                    @empty
                        <li class="list-group-item text-center py-4 text-muted">لا يوجد مستخدمين جدد</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    const ctx = document.getElementById('registrationChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: @json($registrationData['labels']),
            datasets: [{
                label: 'تسجيلات جديدة',
                data: @json($registrationData['data']),
                borderColor: '#4361ee',
                backgroundColor: 'rgba(67, 97, 238, 0.1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false, // يحافظ على الارتفاع المخصص للحاوية
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
        }
    });
</script>
@endpush
@endsection
