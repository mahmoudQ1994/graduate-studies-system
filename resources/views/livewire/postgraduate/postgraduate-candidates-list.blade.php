<div class="container-fluid py-2">
    <!-- عنوان الشاشة -->
    <div class="d-flex align-items-center justify-content-between mb-3">
        <div>
            <h5 class="fw-bold mb-1 text-primary"><i class="bi bi-people-fill me-2"></i>قائمة المرشحين بالدراسات العليا</h5>
            <p class="text-muted small mb-0">استعراض ومتابعة الكادر الطبي المقيد والمرشح للدراسات العليا طبقا للبيانات المسجلة</p>
        </div>
        <div>
            <a href="{{ route('postgraduate.register') }}" class="btn btn-primary btn-sm rounded-3">
                <i class="bi bi-person-plus-fill me-1"></i> تسجيل مرشح جديد
            </a>
        </div>
    </div>

    <!-- شريط الفلاتر والبحث -->
    <div class="card border-0 shadow-sm rounded-3 mb-3">
        <div class="card-body p-3">
            <div class="row g-2">
                <!-- البحث بالاسم -->
                <div class="col-md-3">
                    <input type="text" wire:model.live.debounce.300ms="searchName" class="form-control form-control-sm" placeholder="ابحث بالاسم...">
                </div>
                <!-- البحث بالرقم القومي -->
                <div class="col-md-3">
                    <input type="text" wire:model.live.debounce.300ms="searchNationalId" class="form-control form-control-sm" placeholder="ابحث بالرقم القومي...">
                </div>
                <!-- نوع الدراسة (مستند من required_degree) -->
                <div class="col-md-2">
                    <select wire:model.live="filterDegree" class="form-select form-select-sm">
                        <option value="">-- نوع الدراسة --</option>
                        <option value="دبلوم">دبلوم</option>
                        <option value="ماجستير">ماجستير</option>
                        <option value="دكتوراه">دكتوراه</option>
                        <option value="زمالة">زمالة</option>
                    </select>
                </div>
                <!-- نوع الترشيح (مستند من sponsorship_type) -->
                <div class="col-md-3">
                    <select wire:model.live="filterSponsorship" class="form-select form-select-sm">
                        <option value="">-- نوع الترشيح --</option>
                        <option value="وزاري">وزاري</option>
                        <option value="نفقته الخاصة">على نفقته الخاصة</option>
                        <option value="منحة">منحة</option>
                    </select>
                </div>
                <!-- زر إعادة الضبط -->
                <div class="col-md-1 d-grid">
                    <button wire:click="resetFilters" class="btn btn-outline-secondary btn-sm" title="إعادة ضبط">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- جدول العرض -->
    <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 small">
                    <thead class="table-light text-secondary">
                        <tr>
                            <th class="py-2 px-3">اسم المرشح</th>
                            <th class="py-2">الرقم القومي</th>
                            <th class="py-2">الوظيفة</th>
                            <th class="py-2">جهة العمل الأصلية</th>
                            <th class="py-2">نوع الدراسة</th>
                            <th class="py-2">نوع الترشح</th>
                            <th class="py-2 text-center">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($candidates as $candidate)
                            <tr>
                                <td class="px-3 fw-bold text-dark">
                                    {{ $candidate->healthProfessional->name ?? '---' }}
                                </td>
                                <td>
                                    {{ $candidate->healthProfessional->national_id ?? '---' }}
                                </td>
                                <td>
                                    <!-- الوظيفة من جدول الكادر الطبي profession -->
                                    <span class="badge bg-info bg-opacity-10 text-dark">
                                        {{ $candidate->healthProfessional->profession ?? 'غير محدد' }}
                                    </span>
                                </td>
                                <td>
                                    <!-- جهة العمل الأصلية من علاقة المنشأة -->
                                    {{ $candidate->healthProfessional->facility->name ?? 'غير محدد' }}
                                </td>
                                <td>
                                    <!-- نوع الدراسة من required_degree -->
                                    <span class="badge bg-primary bg-opacity-10 text-primary fw-bold">
                                        {{ $candidate->required_degree ?? '---' }}
                                    </span>
                                </td>
                                <td>
                                    <!-- نوع الترشيح من sponsorship_type -->
                                    <span class="badge bg-secondary bg-opacity-10 text-dark">
                                        {{ $candidate->sponsorship_type ?? '---' }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('postgraduate.edit', $candidate->id) }}" class="btn btn-outline-primary py-1 px-2" title="تعديل">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        <button wire:click="deleteCandidate({{ $candidate->id }})" class="btn btn-outline-danger py-1 px-2" title="حذف">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">
                                    <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                                    لا توجد بيانات مرشحين مسجلة حتى الآن.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- الترقيم -->
        @if($candidates->hasPages())
            <div class="card-footer bg-white py-2">
                {{ $candidates->links() }}
            </div>
        @endif
    </div>
</div>
