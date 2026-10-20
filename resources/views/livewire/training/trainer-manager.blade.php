<div class="container-fluid py-3" dir="rtl" style="font-size: 0.9rem;">
    <div class="row justify-content-center">
        <div class="col-md-12">

            <!-- رسائل النجاح والتنبيهات -->
            @if (session()->has('success'))
                <div class="alert alert-success alert-dismissible fade show text-end shadow-sm border-0 py-2 small" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="card shadow-sm border-0 rounded-3 overflow-hidden">
                <div class="card-header bg-gradient bg-primary text-white d-flex justify-content-between align-items-center py-2 px-3">
                    <h6 class="mb-0 fw-bold fs-6">
                        <i class="bi bi-person-plus-fill me-1"></i> إضافة مدرب / استشاري جديد
                    </h6>
                    <a href="{{ route('training.trainers') }}" class="btn btn-light btn-sm text-primary fw-bold px-2 py-1 shadow-sm" style="font-size: 0.8rem;">
                        <i class="bi bi-arrow-right me-1"></i> العودة لقائمة المدربين
                    </a>
                </div>

                <div class="card-body bg-body-tertiary p-3">
                    <form wire:submit.prevent="save">

                        <!-- نظام التبويبات (Tabs) بتصميم مصغر وأنيق -->
                        <ul class="nav nav-pills nav-fill mb-3 bg-white p-1 rounded-2 shadow-sm border" id="trainerTabs" role="tablist" style="font-size: 0.85rem;">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active fw-bold py-1.5 px-2 rounded-2" id="basic-tab" data-bs-toggle="tab" data-bs-target="#basic" type="button" role="tab">
                                    <i class="bi bi-person-badge me-1"></i> 1. البيانات الأساسية
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link fw-bold py-1.5 px-2 rounded-2" id="qualifications-tab" data-bs-toggle="tab" data-bs-target="#qualifications" type="button" role="tab">
                                    <i class="bi bi-mortarboard me-1"></i> 2. المؤهلات والوظيفة
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link fw-bold py-1.5 px-2 rounded-2" id="specialization-tab" data-bs-toggle="tab" data-bs-target="#specialization" type="button" role="tab">
                                    <i class="bi bi-tools me-1"></i> 3. التخصص والمالية
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link fw-bold py-1.5 px-2 rounded-2" id="courses-tab" data-bs-toggle="tab" data-bs-target="#courses" type="button" role="tab">
                                    <i class="bi bi-journal-bookmark me-1"></i> 4. الدورات والخبرات
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link fw-bold py-1.5 px-2 rounded-2" id="attachments-tab" data-bs-toggle="tab" data-bs-target="#attachments" type="button" role="tab">
                                    <i class="bi bi-paperclip me-1"></i> 5. المرفقات الرسمية
                                </button>
                            </li>
                        </ul>

                        <div class="tab-content bg-white p-3 rounded-3 shadow-sm border" id="trainerTabsContent">

                            <!-- 1. تبويب البيانات الأساسية -->
                            <div class="tab-pane fade show active" id="basic" role="tabpanel" aria-labelledby="basic-tab">
                                <div class="d-flex align-items-center mb-3 pb-2 border-bottom">
                                    <div class="bg-primary bg-opacity-10 p-1.5 rounded-circle text-primary me-2">
                                        <i class="bi bi-person-lines-fill fs-6"></i>
                                    </div>
                                    <h6 class="text-primary fw-bold mb-0" style="font-size: 0.95rem;">المعلومات الشخصية وطرق التواصل</h6>
                                </div>

                                <div class="row g-2">

                                    <div class="col-md-3">
                                        <label class="form-label fw-semibold text-secondary mb-1" style="font-size: 0.82rem;">الصفة التدريبية <span class="text-danger">*</span></label>
                                        <select wire:model="trainer_type" class="form-select form-select-sm shadow-none">
                                            <option value="trainer">مدرب</option>
                                            <option value="consultant">استشاري</option>
                                        </select>
                                        @error('trainer_type') <span class="text-danger" style="font-size: 0.75rem;">{{ $message }}</span> @enderror
                                    </div>

                                    <div class="col-md-5">
                                        <label class="form-label fw-semibold text-secondary mb-1" style="font-size: 0.82rem;">الاسم رباعي (بالعربي) <span class="text-danger">*</span></label>
                                        <input type="text" wire:model="name_ar" class="form-control form-control-sm shadow-none" placeholder="أدخل الاسم رباعياً بالعربية">
                                        @error('name_ar') <span class="text-danger" style="font-size: 0.75rem;">{{ $message }}</span> @enderror
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold text-secondary mb-1" style="font-size: 0.82rem;">الاسم رباعي (بالإنجليزي)</label>
                                        <input type="text" wire:model="name_en" class="form-control form-control-sm shadow-none" placeholder="Full Name in English">
                                        @error('name_en') <span class="text-danger" style="font-size: 0.75rem;">{{ $message }}</span> @enderror
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold text-secondary mb-1" style="font-size: 0.82rem;">الرقم القومي (14 رقم) <span class="text-danger">*</span></label>
                                        <input type="text" wire:model.live.debounce.300ms="national_id" maxlength="14" class="form-control form-control-sm shadow-none" placeholder="أدخل 14 رقماً للاستنتاج التلقائي">
                                        @error('national_id') <span class="text-danger" style="font-size: 0.75rem;">{{ $message }}</span> @enderror
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold text-secondary mb-1" style="font-size: 0.82rem;">تاريخ الميلاد (تلقائي)</label>
                                        <input type="date" wire:model="birth_date" class="form-control form-control-sm bg-light shadow-none" readonly>
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold text-secondary mb-1" style="font-size: 0.82rem;">النوع (تلقائي)</label>
                                        <select wire:model="gender" class="form-select form-select-sm bg-light shadow-none" disabled>
                                            <option value="">-- يحدد تلقائياً --</option>
                                            <option value="male">ذكر</option>
                                            <option value="female">أنثى</option>
                                        </select>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-secondary mb-1" style="font-size: 0.82rem;">رقم المحمول</label>
                                        <input type="text" wire:model="phone" class="form-control form-control-sm shadow-none" placeholder="010xxxxxxxx">
                                        @error('phone') <span class="text-danger" style="font-size: 0.75rem;">{{ $message }}</span> @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-secondary mb-1" style="font-size: 0.82rem;">البريد الإلكتروني</label>
                                        <input type="email" wire:model="email" class="form-control form-control-sm shadow-none" placeholder="example@domain.com">
                                        @error('email') <span class="text-danger" style="font-size: 0.75rem;">{{ $message }}</span> @enderror
                                    </div>

                                </div>
                            </div>

                            <!-- 2. تبويب المؤهلات والوظيفة -->
                            <div class="tab-pane fade" id="qualifications" role="tabpanel" aria-labelledby="qualifications-tab">
                                <div class="d-flex align-items-center mb-3 pb-2 border-bottom">
                                    <div class="bg-primary bg-opacity-10 p-1.5 rounded-circle text-primary me-2">
                                        <i class="bi bi-mortarboard-fill fs-6"></i>
                                    </div>
                                    <h6 class="text-primary fw-bold mb-0" style="font-size: 0.95rem;">بيانات المؤهل العلمي والوضع الوظيفي</h6>
                                </div>

                                <div class="row g-2">

                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold text-secondary mb-1" style="font-size: 0.82rem;">المؤهل العلمي الأساسي</label>
                                        <select wire:model="academic_qualification" class="form-select form-select-sm shadow-none">
                                            <option value="">-- اختر المؤهل العلمي --</option>
                                            @foreach($academicSpecialties as $spec)
                                                <option value="{{ $spec->name }}">{{ $spec->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('academic_qualification') <span class="text-danger" style="font-size: 0.75rem;">{{ $message }}</span> @enderror
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold text-secondary mb-1" style="font-size: 0.82rem;">سنة الحصول على المؤهل</label>
                                        <input type="number" wire:model="qualification_year" class="form-control form-control-sm shadow-none" placeholder="مثال: 2015">
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold text-secondary mb-1" style="font-size: 0.82rem;">الجامعة أو المعهد</label>
                                        <select wire:model="university_or_institute" class="form-select form-select-sm shadow-none">
                                            <option value="">-- اختر الجامعة أو المعهد --</option>
                                            @foreach($universities as $uni)
                                                <option value="{{ $uni->name }}">{{ $uni->name }} ({{ $uni->type }})</option>
                                            @endforeach
                                        </select>
                                        @error('university_or_institute') <span class="text-danger" style="font-size: 0.75rem;">{{ $message }}</span> @enderror
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold text-secondary mb-1" style="font-size: 0.82rem;">الوظيفة الحالية</label>
                                        <input type="text" wire:model="current_job" class="form-control form-control-sm shadow-none" placeholder="المسمى الوظيفي الحالي">
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold text-secondary mb-1" style="font-size: 0.82rem;">جهة العمل</label>
                                        <input type="text" wire:model="workplace" class="form-control form-control-sm shadow-none" placeholder="اسم المستشفى أو المؤسسة">
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold text-secondary mb-1" style="font-size: 0.82rem;">محافظة جهة العمل</label>
                                        <input type="text" wire:model="workplace_governorate" class="form-control form-control-sm shadow-none" placeholder="مثال: سوهاج، القاهرة">
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-secondary mb-1" style="font-size: 0.82rem;">الدرجة الوظيفية</label>
                                        <select wire:model.live="job_grade_id" class="form-select form-select-sm shadow-none">
                                            <option value="">-- اختر الدرجة الوظيفية --</option>
                                            @foreach($jobGrades as $grade)
                                                <option value="{{ $grade->id }}">{{ $grade->name ?? $grade->title }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-secondary mb-1" style="font-size: 0.82rem;">الحالة الوظيفية</label>
                                        <select wire:model="job_status_id" class="form-select form-select-sm shadow-none">
                                            <option value="">-- اختر الحالة الوظيفية --</option>
                                            @foreach($jobStatuses as $status)
                                                <option value="{{ $status->id }}">{{ $status->name ?? $status->title }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                </div>
                            </div>

                            <!-- 3. تبويب التخصص والمالية -->
                            <div class="tab-pane fade" id="specialization" role="tabpanel" aria-labelledby="specialization-tab">
                                <div class="d-flex align-items-center mb-3 pb-2 border-bottom">
                                    <div class="bg-primary bg-opacity-10 p-1.5 rounded-circle text-primary me-2">
                                        <i class="bi bi-award-fill fs-6"></i>
                                    </div>
                                    <h6 class="text-primary fw-bold mb-0" style="font-size: 0.95rem;">التخصصات، الدراسات العليا، والمستحقات المالية</h6>
                                </div>

                                <div class="row g-2">

                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold text-secondary mb-1" style="font-size: 0.82rem;">التخصص الفني</label>
                                        <select wire:model="specialization_id" class="form-select form-select-sm shadow-none">
                                            <option value="">-- اختر التخصص الفني --</option>
                                            @foreach($specializations as $spec)
                                                <option value="{{ $spec->id }}">{{ $spec->name ?? $spec->title }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold text-secondary mb-1" style="font-size: 0.82rem;">الدراسات العليا في التخصص</label>
                                        <input type="text" wire:model="postgraduate_specialization" class="form-control form-control-sm shadow-none" placeholder="دكتوراه / زمالة / ماجستير">
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold text-secondary mb-1" style="font-size: 0.82rem;">سنة الحصول عليها</label>
                                        <input type="number" wire:model="postgraduate_specialization_year" class="form-control form-control-sm shadow-none" placeholder="مثال: 2020">
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold text-secondary mb-1" style="font-size: 0.82rem;">سنوات الخبرة بالتخصص</label>
                                        <input type="number" wire:model="specialization_experience_years" class="form-control form-control-sm shadow-none" min="0">
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold text-secondary mb-1" style="font-size: 0.82rem;">الدراسات العليا الإدارية</label>
                                        <input type="text" wire:model="administrative_postgraduates" class="form-control form-control-sm shadow-none" placeholder="مثال: MBA, إدارة مستشفيات">
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold text-secondary mb-1" style="font-size: 0.82rem;">مستوى إجادة اللغة الإنجليزية</label>
                                        <select wire:model="english_proficiency_level" class="form-select form-select-sm shadow-none">
                                            <option value="">-- اختر المستوى --</option>
                                            <option value="ممتاز">ممتاز</option>
                                            <option value="جيد جداً">جيد جداً</option>
                                            <option value="جيد">جيد</option>
                                            <option value="مقبول">مقبول</option>
                                        </select>
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold text-success mb-1" style="font-size: 0.82rem;">مكافأة السعة التدريبية (تلقائي)</label>
                                        <input type="number" step="0.01" wire:model="capacity_reward_amount" class="form-control form-control-sm bg-light fw-bold text-success shadow-none">
                                    </div>

                                    <div class="col-md-12 mt-3 p-3 border rounded-3 bg-light shadow-sm">
                                        <div class="form-check form-switch mb-2">
                                            <input class="form-check-input" type="checkbox" wire:model.live="has_tot_certificate" id="totCheck" style="transform: scale(1.1); margin-left: 8px;">
                                            <label class="form-check-label fw-bold text-dark" for="totCheck" style="font-size: 0.85rem;">حاصل على برنامج إعداد مدربين (TOT)؟</label>
                                        </div>

                                        @if($has_tot_certificate)
                                            <div class="row g-2 mt-1 pt-2 border-top">
                                                <div class="col-md-6">
                                                    <label class="form-label fw-semibold text-secondary mb-1" style="font-size: 0.8rem;">جهة الحصول على البرنامج</label>
                                                    <input type="text" wire:model="tot_provider" class="form-control form-control-sm bg-white shadow-none" placeholder="اسم الجهة المانحة لشهادة TOT">
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label fw-semibold text-secondary mb-1" style="font-size: 0.8rem;">تاريخ الحصول على الشهادة</label>
                                                    <input type="date" wire:model="tot_date" class="form-control form-control-sm bg-white shadow-none">
                                                </div>
                                            </div>
                                        @endif
                                    </div>

                                </div>
                            </div>

                            <!-- 4. تبويب الدورات والخبرات -->
                            <div class="tab-pane fade" id="courses" role="tabpanel" aria-labelledby="courses-tab">
                                <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-primary bg-opacity-10 p-1.5 rounded-circle text-primary me-2">
                                            <i class="bi bi-journal-text fs-6"></i>
                                        </div>
                                        <h6 class="text-primary fw-bold mb-0" style="font-size: 0.95rem;">الدورات التدريبية، المنح، والبرامج المقدمة</h6>
                                    </div>
                                    <button type="button" wire:click="addCourseRow" class="btn btn-sm btn-primary py-1 px-2 shadow-sm" style="font-size: 0.8rem;">
                                        <i class="bi bi-plus-circle me-1"></i> إضافة صف جديد
                                    </button>
                                </div>

                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover align-middle shadow-sm mb-0" style="font-size: 0.85rem;">
                                        <thead class="table-light text-center">
                                            <tr>
                                                <th style="width: 20%;">نوع السجل</th>
                                                <th style="width: 35%;">اسم البرنامج / الدورة / المنحة</th>
                                                <th style="width: 20%;">التاريخ</th>
                                                <th style="width: 20%;">الجهة المانحة / المنظمة</th>
                                                <th style="width: 5%;">حذف</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($courses as $index => $course)
                                                <tr>
                                                    <td>
                                                        <select wire:model="courses.{{ $index }}.course_type" class="form-select form-select-sm shadow-none">
                                                            <option value="obtained">دورة حاصل عليها</option>
                                                            <option value="offered">دورة يقدمها</option>
                                                            <option value="external_grant">منحة خارجية</option>
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <input type="text" wire:model="courses.{{ $index }}.course_name" class="form-control form-control-sm shadow-none" placeholder="اسم البرنامج التدريبي">
                                                    </td>
                                                    <td>
                                                        <input type="date" wire:model="courses.{{ $index }}.acquisition_date" class="form-control form-control-sm shadow-none">
                                                    </td>
                                                    <td>
                                                        <input type="text" wire:model="courses.{{ $index }}.issuer_entity" class="form-control form-control-sm shadow-none" placeholder="الجهة">
                                                    </td>
                                                    <td class="text-center">
                                                        @if(count($courses) > 1)
                                                            <button type="button" wire:click="removeCourseRow({{ $index }})" class="btn btn-sm btn-outline-danger border-0 py-0 px-1">
                                                                <i class="bi bi-trash fs-6"></i>
                                                            </button>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- 5. تبويب المرفقات الرسمية -->
                            <div class="tab-pane fade" id="attachments" role="tabpanel" aria-labelledby="attachments-tab">
                                <div class="d-flex align-items-center mb-3 pb-2 border-bottom">
                                    <div class="bg-primary bg-opacity-10 p-1.5 rounded-circle text-primary me-2">
                                        <i class="bi bi-paperclip fs-6"></i>
                                    </div>
                                    <h6 class="text-primary fw-bold mb-0" style="font-size: 0.95rem;">رفع المستندات والمرفقات الرسمية (PDF أو صور)</h6>
                                </div>

                                <div class="row g-3">

                                    <div class="col-md-6">
                                        <div class="p-2.5 border rounded-3 bg-light shadow-sm">
                                            <label class="form-label fw-semibold text-secondary mb-1" style="font-size: 0.82rem;">صورة الرقم القومي (الوجه الأمامي)</label>
                                            <input type="file" wire:model="national_id_front" class="form-control form-control-sm shadow-none">
                                            <div wire:loading wire:target="national_id_front" class="text-primary mt-1" style="font-size: 0.75rem;">جاري رفع الملف...</div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="p-2.5 border rounded-3 bg-light shadow-sm">
                                            <label class="form-label fw-semibold text-secondary mb-1" style="font-size: 0.82rem;">صورة الرقم القومي (الوجه الخلفي)</label>
                                            <input type="file" wire:model="national_id_back" class="form-control form-control-sm shadow-none">
                                            <div wire:loading wire:target="national_id_back" class="text-primary mt-1" style="font-size: 0.75rem;">جاري رفع الملف...</div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="p-2.5 border rounded-3 bg-light shadow-sm">
                                            <label class="form-label fw-semibold text-secondary mb-1" style="font-size: 0.82rem;">صورة بيان الحالة الوظيفية</label>
                                            <input type="file" wire:model="job_status_certificate" class="form-control form-control-sm shadow-none">
                                            <div wire:loading wire:target="job_status_certificate" class="text-primary mt-1" style="font-size: 0.75rem;">جاري رفع الملف...</div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="p-2.5 border rounded-3 bg-light shadow-sm">
                                            <label class="form-label fw-semibold text-secondary mb-1" style="font-size: 0.82rem;">صورة شهادة إعداد مدربين (TOT)</label>
                                            <input type="file" wire:model="tot_certificate" class="form-control form-control-sm shadow-none">
                                            <div wire:loading wire:target="tot_certificate" class="text-primary mt-1" style="font-size: 0.75rem;">جاري رفع الملف...</div>
                                        </div>
                                    </div>

                                </div>
                            </div>

                        </div>

                        <!-- زر الحفظ النهائي -->
                        <div class="row mt-3">
                            <div class="col-12 text-end">
                                <button type="submit" class="btn btn-success px-4 py-2 shadow-sm fw-bold rounded-pill" style="font-size: 0.9rem;">
                                    <i class="bi bi-save2-fill me-1"></i> حفظ بيانات المدرب
                                </button>
                            </div>
                        </div>

                    </form>
                </div>
            </div>

        </div>
    </div>
</div>
