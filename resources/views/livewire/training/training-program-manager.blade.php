<div class="container-fluid py-3">
    <!-- عنوان الصفحة وزر الإضافة -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold text-primary mb-0">
            <i class="bi bi-journal-plus me-1"></i> إدخال وإدارة البرامج التدريبية
        </h5>
        <button class="btn btn-primary btn-sm" type="button" data-bs-toggle="collapse" data-bs-target="#programFormCollapse" aria-expanded="{{ $isEditMode ? 'true' : 'false' }}">
            <i class="bi bi-plus-lg me-1"></i> {{ $isEditMode ? 'تعديل بيانات البرنامج' : 'إضافة برنامج تدريبي جديد' }}
        </button>
    </div>

    <!-- رسائل التنبيه -->
    @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- نموذج الإضافة والتعديل الشامل -->
    <div class="collapse {{ $isEditMode ? 'show' : '' }} mb-4" id="programFormCollapse">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-header bg-primary text-white py-2 fw-bold d-flex justify-content-between align-items-center">
                <span>{{ $isEditMode ? 'تعديل البرنامج التدريبي' : 'إضافة برنامج تدريبي جديد' }}</span>
                @if($isEditMode)
                    <button type="button" wire:click="resetFields" class="btn btn-light btn-sm py-0 px-2" style="font-size: 0.75rem;">إلغاء</button>
                @endif
            </div>
            <div class="card-body bg-light">
                <form wire:submit.prevent="save">
                    <div class="row g-3">
                        <!-- البيانات الأساسية -->
                        <div class="col-md-2">
                            <label class="form-label small fw-bold">كود البرنامج (تلقائي)</label>
                            <div class="input-group input-group-sm">
                                <input type="text" class="form-control @error('code') is-invalid @enderror" wire:model="code">
                                <button type="button" wire:click="generateCode" class="btn btn-outline-secondary"><i class="bi bi-arrow-clockwise"></i></button>
                            </div>
                            @error('code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-5">
                            <label class="form-label small fw-bold">اسم البرنامج (بالعربية) <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-sm @error('name_ar') is-invalid @enderror" wire:model="name_ar" placeholder="اسم البرنامج التدريبي بالعربية">
                            @error('name_ar') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-5">
                            <label class="form-label small fw-bold">اسم البرنامج (بالإنجليزية)</label>
                            <input type="text" class="form-control form-control-sm" wire:model="name_en" placeholder="Program Name in English">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label small fw-bold">نوع البرنامج <span class="text-danger">*</span></label>
                            <select class="form-select form-select-sm @error('program_type_id') is-invalid @enderror" wire:model="program_type_id">
                                <option value="">-- اختر نوع البرنامج --</option>
                                @foreach($programTypes as $type)
                                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                                @endforeach
                            </select>
                            @error('program_type_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-3">
                            <label class="form-label small fw-bold">مكان التدريب (القاعة)</label>
                            <select class="form-select form-select-sm" wire:model="hall_id">
                                <option value="">-- اختر القاعة --</option>
                                @foreach($halls as $hall)
                                    <option value="{{ $hall->id }}">{{ $hall->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label small fw-bold">مستوى البرنامج</label>
                            <input type="text" class="form-control form-control-sm" wire:model="level" placeholder="مثال: مبتدئ، متقدم، استشاري">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label small fw-bold">استشاري البرنامج / المسؤول العلمي</label>
                            <input type="text" class="form-control form-control-sm" wire:model="consultant_name" placeholder="اسم الاستشاري المشرف">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label small fw-bold">عدد أيام البرنامج الواحد</label>
                            <input type="number" class="form-control form-control-sm" wire:model="duration_days" min="1">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label small fw-bold">عدد الساعات التدريبية</label>
                            <input type="number" class="form-control form-control-sm" wire:model="training_hours" min="0">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label small fw-bold">عدد الدورات خلال العام المالي</label>
                            <input type="number" class="form-control form-control-sm" wire:model="courses_count_per_year" min="1">
                        </div>

                        <!-- أهداف البرنامج مقسمة إلى ثلاثة محاور -->
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-success"><i class="bi bi-lightbulb me-1"></i> أهداف المعرفة (Knowledge)</label>
                            <textarea class="form-control form-control-sm" wire:model="objective_knowledge" rows="2" placeholder="أهداف المعرفة المكتسبة..."></textarea>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-primary"><i class="bi bi-gear me-1"></i> أهداف المهارات (Skills)</label>
                            <textarea class="form-control form-control-sm" wire:model="objective_skills" rows="2" placeholder="المهارات العملية المستهدفة..."></textarea>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-warning"><i class="bi bi-heart me-1"></i> أهداف السلوك المهني (Attitude)</label>
                            <textarea class="form-control form-control-sm" wire:model="objective_attitude" rows="2" placeholder="تطوير السلوك المهني والاتجاهات..."></textarea>
                        </div>

                        <div class="col-12">
                            <label class="form-label small fw-bold">متطلبات التدريب المسبقة</label>
                            <input type="text" class="form-control form-control-sm" wire:model="prerequisites" placeholder="الشروط أو المتطلبات للانضمام للبرنامج">
                        </div>

                        <!-- مربعات الاختيار المتعدد (Checkboxes للارتباطات) -->
                        <div class="col-md-3 border-end">
                            <label class="form-label small fw-bold text-secondary">أساليب التدريب:</label>
                            <div class="d-flex flex-column gap-1 max-h-120 overflow-auto" style="max-height: 120px;">
                                @foreach($allMethods as $method)
                                    <div class="form-check form-check-sm">
                                        <input class="form-check-input" type="checkbox" wire:model="selectedMethods" value="{{ $method->id }}" id="method_{{ $method->id }}">
                                        <label class="form-check-label small" for="method_{{ $method->id }}">{{ $method->name }}</label>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="col-md-3 border-end">
                            <label class="form-label small fw-bold text-secondary">قياس المعرفة المكتسبة:</label>
                            <div class="d-flex flex-column gap-1 overflow-auto" style="max-height: 120px;">
                                @foreach($allKnowledge as $item)
                                    <div class="form-check form-check-sm">
                                        <input class="form-check-input" type="checkbox" wire:model="selectedKnowledge" value="{{ $item->id }}" id="knowledge_{{ $item->id }}">
                                        <label class="form-check-label small" for="knowledge_{{ $item->id }}">{{ $item->name }}</label>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="col-md-3 border-end">
                            <label class="form-label small fw-bold text-secondary">قياس أثر التدريب:</label>
                            <div class="d-flex flex-column gap-1 overflow-auto" style="max-height: 120px;">
                                @foreach($allImpact as $item)
                                    <div class="form-check form-check-sm">
                                        <input class="form-check-input" type="checkbox" wire:model="selectedImpact" value="{{ $item->id }}" id="impact_{{ $item->id }}">
                                        <label class="form-check-label small" for="impact_{{ $item->id }}">{{ $item->name }}</label>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label small fw-bold text-secondary">الفئات المستهدفة:</label>
                            <div class="d-flex flex-column gap-1 overflow-auto" style="max-height: 120px;">
                                @foreach($allAudiences as $audience)
                                    <div class="form-check form-check-sm">
                                        <input class="form-check-input" type="checkbox" wire:model="selectedAudiences" value="{{ $audience->id }}" id="audience_{{ $audience->id }}">
                                        <label class="form-check-label small" for="audience_{{ $audience->id }}">{{ $audience->name }}</label>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="col-12 text-end mt-3">
                            <button type="submit" class="btn btn-success btn-sm px-4">
                                <i class="bi bi-check-circle me-1"></i> {{ $isEditMode ? 'تحديث البرنامج التدريبي' : 'حفظ البرنامج التدريبي' }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- جدول عرض البرامج التدريبية -->
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white py-2 d-flex justify-content-between align-items-center">
            <span class="fw-bold text-secondary">قائمة البرامج التدريبية المسجلة</span>
            <div style="width: 300px;">
                <input type="text" class="form-control form-control-sm" wire:model.live.debounce.300ms="search" placeholder="بحث بالاسم، الكود، أو الاستشاري...">
            </div>
        </div>
        <div class="card-body p-0 table-responsive">
            <table class="table table-hover table-striped mb-0 text-center align-middle small">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>الكود</th>
                        <th>اسم البرنامج</th>
                        <th>نوع البرنامج</th>
                        <th>الساعات / الأيام</th>
                        <th>مكان التدريب</th>
                        <th>الاستشاري</th>
                        <th>أُضيف بواسطة</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($programs as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td><span class="badge bg-light text-dark border">{{ $item->code }}</span></td>
                            <td class="fw-bold text-start">{{ $item->name_ar }}</td>
                            <td><span class="badge bg-info text-dark">{{ $item->programType->name ?? '-' }}</span></td>
                            <td>{{ $item->training_hours }} ساعة / {{ $item->duration_days }} أيام</td>
                            <td>{{ $item->hall->name ?? '-' }}</td>
                            <td class="text-muted">{{ $item->consultant_name ?? '-' }}</td>
                            <td class="text-muted" style="font-size: 0.75rem;">{{ $item->creator->name ?? '-' }}</td>
                            <td>
                                <button wire:click="edit({{ $item->id }})" class="btn btn-outline-primary btn-sm py-0 px-1 me-1" title="تعديل">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                                <button wire:confirm="هل أنت متأكد من رغبتك في حذف هذا البرنامج التدريبي؟" wire:click="delete({{ $item->id }})" class="btn btn-outline-danger btn-sm py-0 px-1" title="حذف">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-4 text-muted">لا توجد برامج تدريبية مسجلة حتى الآن.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($programs->hasPages())
            <div class="card-footer bg-white py-2">
                {{ $programs->links() }}
            </div>
        @endif
    </div>
</div>
