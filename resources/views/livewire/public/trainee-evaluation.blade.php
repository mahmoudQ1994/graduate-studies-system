<div class="container-fluid py-5 px-3" style="background-color: #f0f2f5; min-height: 100vh;" dir="rtl">
    <div class="row justify-content-center">
        <div class="col-md-10 col-lg-8 col-xl-7">

            <div class="card border-0 shadow-lg rounded-4 p-4 p-md-5 bg-white text-start" dir="rtl">

                <!-- الترويسة الرسمية الاحترافية (الشعار يميناً والتسلسل الإداري يساره) -->
                <div class="pb-3 border-bottom mb-4">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">

                        <!-- التسلسل الإداري (الوزارة -> المحافظة -> المديرية -> الإدارة) -->
                        <div class="d-flex flex-column align-items-start text-start">
                            <span class="fw-bold text-dark mb-1" style="font-size: 0.92rem; line-height: 1.4;">
                                {{ $settings->ministry_name ?? 'وزارة الصحة والسكان' }}
                            </span>
                            <span class="fw-semibold text-secondary mb-1" style="font-size: 0.85rem; line-height: 1.4;">
                                {{ $settings->governorate_name ?? 'محافظة سوهاج' }}
                            </span>
                            <span class="text-muted d-block mb-1" style="font-size: 0.78rem; line-height: 1.4;">
                                {{ $settings->directorate_name ?? 'مديرية الشئون الصحية' }}
                            </span>
                            <span class="text-primary fw-semibold d-block" style="font-size: 0.75rem; line-height: 1.4;">
                                {{ $settings->administration_name ?? 'إدارة التدريب والتعليم الطبي' }}
                            </span>
                        </div>

                        <!-- الشعار الرسمي في أقصى اليمين -->
                        <div class="ms-auto">
                            @if($settings && $settings->logo_path)
                                <img src="{{ asset('storage/' . $settings->logo_path) }}" alt="Logo" class="rounded-3 shadow-sm border p-1 bg-white" style="width: 75px; height: 75px; object-fit: contain;">
                            @else
                                <div class="bg-success-subtle text-success rounded-3 d-flex align-items-center justify-content-center shadow-sm border" style="width: 75px; height: 75px;">
                                    <i class="bi bi-hospital fs-3"></i>
                                </div>
                            @endif
                        </div>

                    </div>
                </div>

                <!-- عنوان البرنامج التدريبي -->
                <div class="text-center mb-4 bg-light p-3 rounded-4 border">
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-1.5 rounded-pill fw-semibold mb-2" style="font-size: 0.82rem;">
                        <i class="bi bi-clipboard2-pulse-fill me-1"></i> استمارة التقييم الشامل للبرنامج التدريبي
                    </span>
                    @if($scheduleDetails)
                        <h5 class="fw-bold text-dark mb-1 mt-1" style="font-size: 1.05rem;">{{ $scheduleDetails->program_name }}</h5>
                        <span class="text-muted small" style="font-size: 0.78rem;">فترة التنفيذ: من <strong>{{ $scheduleDetails->start_date }}</strong> إلى <strong>{{ $scheduleDetails->end_date }}</strong></span>
                    @endif
                </div>

                @if ($successMessage)
                    <div class="alert alert-success rounded-4 border-0 text-center py-4 px-3 mb-4 shadow-sm bg-success-subtle text-success-emphasis">
                        <i class="bi bi-check-circle-fill fs-1 d-block mb-2 text-success"></i>
                        <h5 class="fw-bold mb-1">تم إرسال استمارة التقييم بنجاح</h5>
                        <p class="mb-0 small" style="font-size: 0.88rem;">{{ $successMessage }}</p>
                    </div>
                @endif

                @if ($errorMessage)
                    <div class="alert alert-danger rounded-3 border-0 text-center py-2.5 px-3 mb-4 shadow-sm" style="font-size: 0.85rem;">
                        <i class="bi bi-exclamation-triangle-fill text-danger me-1"></i>
                        <span class="fw-semibold">{{ $errorMessage }}</span>
                    </div>
                @endif

                @if($scheduleDetails && !$successMessage)
                    <form wire:submit.prevent="submitEvaluation">

                        <!-- أولاً: بيانات المتدرب والبريد الإلكتروني للشهادة -->
                        <div class="card bg-light border-0 rounded-4 p-3 mb-4 shadow-sm text-start">
                            <h6 class="fw-bold text-dark mb-3 border-bottom pb-2 text-start" style="font-size: 0.9rem;">
                                <i class="bi bi-person-badge text-primary me-1 "></i> أولاً: بيانات المتدرب والبريد الإلكتروني (لتلقي الشهادة)
                            </h6>

                            <div class="row g-3">
                                <div class="col-md-6 text-start">
                                    <label class="form-label fw-bold text-secondary small d-block">الرقم القومي (14 رقماً):</label>
                                    <input type="text" class="form-control rounded-pill bg-white border-light py-2 px-3 font-monospace text-start fs-6 shadow-sm" wire:model="national_id" placeholder="أدخل الرقم القومي..." maxlength="14">
                                </div>

                                <div class="col-md-6 text-start">
                                    <label class="form-label fw-bold text-secondary small d-block">البريد الإلكتروني الرسمي لاستلام الشهادة:</label>
                                    <input type="email" class="form-control rounded-pill bg-white border-light py-2 px-3 text-start shadow-sm" wire:model="official_email" placeholder="name@example.com">
                                </div>
                            </div>
                        </div>

                        <!-- ثانياً: محاور التقييم العالمية (من 1 إلى 5) -->
                        <div class="mb-4">
                            <h6 class="fw-bold text-dark mb-3 border-bottom pb-2 text-start" style="font-size: 0.9rem;">
                                <i class="bi bi-star-half text-warning me-1"></i> ثانياً: استطلاع رأي المحاور التدريبية (5 ممتاز - 1 ضعيف)
                            </h6>

                            <!-- دالة مساعدة لتصميم كل سؤال ليبدأ من اليمين بانتظام -->
                            @php
                                $ratingOptions = [
                                    '5' => '⭐⭐⭐⭐⭐ ممتاز',
                                    '4' => '⭐⭐⭐⭐ جيد جداً',
                                    '3' => '⭐⭐⭐ جيد',
                                    '2' => '⭐⭐ مقبول',
                                    '1' => '⭐ ضعيف'
                                ];
                            @endphp

                            <!-- 1. المحتوى العلمي -->
                            <div class="card p-3 mb-3 border shadow-sm rounded-3 text-start">
                                <span class="fw-bold text-primary small mb-3 d-block"><i class="bi bi-book me-1"></i> 1. المحتوى العلمي وبرنامج الدورة:</span>

                                <div class="mb-3 text-start">
                                    <label class="form-label text-dark small mb-1 fw-semibold d-block">وضوح الأهداف التدريبية ومناسبتها:</label>
                                    <select class="form-select form-select-sm rounded-pill bg-light" wire:model="content_clarity">
                                        @foreach($ratingOptions as $val => $label)
                                            <option value="{{ $val }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-3 text-start">
                                    <label class="form-label text-dark small mb-1 fw-semibold d-block">ملاءمة المحتوى لمتطلبات واحتياجات العمل الفعلي:</label>
                                    <select class="form-select form-select-sm rounded-pill bg-light" wire:model="content_relevance">
                                        @foreach($ratingOptions as $val => $label)
                                            <option value="{{ $val }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-3 text-start">
                                    <label class="form-label text-dark small mb-1 fw-semibold d-block">عمق المادة العلمية وشموليتها:</label>
                                    <select class="form-select form-select-sm rounded-pill bg-light" wire:model="content_depth">
                                        @foreach($ratingOptions as $val => $label)
                                            <option value="{{ $val }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="text-start">
                                    <label class="form-label text-dark small mb-1 fw-semibold d-block">التوافق بين مدة الدورة وحجم المحتوى:</label>
                                    <select class="form-select form-select-sm rounded-pill bg-light" wire:model="content_time_balance">
                                        @foreach($ratingOptions as $val => $label)
                                            <option value="{{ $val }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- 2. أداء المدرب -->
                            <div class="card p-3 mb-3 border shadow-sm rounded-3 text-start">
                                <span class="fw-bold text-primary small mb-3 d-block"><i class="bi bi-person-video3 me-1"></i> 2. أداء وكفاءة المحاضرين:</span>

                                <div class="mb-3 text-start">
                                    <label class="form-label text-dark small mb-1 fw-semibold d-block">إلمام المحاضر بموضوع البرنامج وخبرته:</label>
                                    <select class="form-select form-select-sm rounded-pill bg-light" wire:model="trainer_knowledge">
                                        @foreach($ratingOptions as $val => $label)
                                            <option value="{{ $val }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-3 text-start">
                                    <label class="form-label text-dark small mb-1 fw-semibold d-block">مهارات العرض، الإلقاء، وأسلوب الشرح:</label>
                                    <select class="form-select form-select-sm rounded-pill bg-light" wire:model="trainer_delivery">
                                        @foreach($ratingOptions as $val => $label)
                                            <option value="{{ $val }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-3 text-start">
                                    <label class="form-label text-dark small mb-1 fw-semibold d-block">تحفيز المشاركة والتفاعل والرد على الأسئلة:</label>
                                    <select class="form-select form-select-sm rounded-pill bg-light" wire:model="trainer_interaction">
                                        @foreach($ratingOptions as $val => $label)
                                            <option value="{{ $val }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="text-start">
                                    <label class="form-label text-dark small mb-1 fw-semibold d-block">الالتزام بالوقت وإدارة الجلسة التدريبية:</label>
                                    <select class="form-select form-select-sm rounded-pill bg-light" wire:model="trainer_time_management">
                                        @foreach($ratingOptions as $val => $label)
                                            <option value="{{ $val }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- 3. البيئة والقاعات -->
                            <div class="card p-3 mb-3 border shadow-sm rounded-3 text-start">
                                <span class="fw-bold text-primary small mb-3 d-block"><i class="bi bi-building me-1"></i> 3. القاعات والبيئة التدريبية:</span>

                                <div class="mb-3 text-start">
                                    <label class="form-label text-dark small mb-1 fw-semibold d-block">ملاءمة القاعة، الإضاءة، والتهوية:</label>
                                    <select class="form-select form-select-sm rounded-pill bg-light" wire:model="hall_comfort">
                                        @foreach($ratingOptions as $val => $label)
                                            <option value="{{ $val }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="text-start">
                                    <label class="form-label text-dark small mb-1 fw-semibold d-block">كفاءة الأجهزة والتجهيزات التقنية (عرض، صوت):</label>
                                    <select class="form-select form-select-sm rounded-pill bg-light" wire:model="equipment_quality">
                                        @foreach($ratingOptions as $val => $label)
                                            <option value="{{ $val }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- 4. التنظيم الإداري -->
                            <div class="card p-3 mb-3 border shadow-sm rounded-3 text-start">
                                <span class="fw-bold text-primary small mb-3 d-block"><i class="bi bi-people me-1"></i> 4. التنظيم الإداري:</span>

                                <div class="text-start">
                                    <label class="form-label text-dark small mb-1 fw-semibold d-block">حسن الاستقبال وتجاوب الفريق الإداري:</label>
                                    <select class="form-select form-select-sm rounded-pill bg-light" wire:model="admin_response">
                                        @foreach($ratingOptions as $val => $label)
                                            <option value="{{ $val }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- 5. الأثر والفائدة -->
                            <div class="card p-3 border shadow-sm rounded-3 text-start">
                                <span class="fw-bold text-primary small mb-3 d-block"><i class="bi bi-graph-up-arrow me-1"></i> 5. الأثر والفائدة العملية:</span>

                                <div class="mb-3 text-start">
                                    <label class="form-label text-dark small mb-1 fw-semibold d-block">حجم الفائدة والقدرة على تطبيق المعارف بالميدان:</label>
                                    <select class="form-select form-select-sm rounded-pill bg-light" wire:model="practical_application">
                                        @foreach($ratingOptions as $val => $label)
                                            <option value="{{ $val }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="text-start">
                                    <label class="form-label text-dark small mb-1 fw-semibold d-block">الرضا العام عن البرنامج التدريبي:</label>
                                    <select class="form-select form-select-sm rounded-pill bg-light" wire:model="overall_satisfaction">
                                        @foreach($ratingOptions as $val => $label)
                                            <option value="{{ $val }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- ثالثاً: المقترحات -->
                        <div class="mb-4 text-start">
                            <h6 class="fw-bold text-dark mb-3 border-bottom pb-2 text-start" style="font-size: 0.9rem;">
                                <i class="bi bi-chat-square-text text-success me-1"></i> ثالثاً: التوصيات والمقترحات التطويرية
                            </h6>
                            <div class="mb-3 text-start">
                                <label class="form-label text-muted small d-block">مقترحات لتحسين البرامج التدريبية المستقبلية (اختياري):</label>
                                <textarea class="form-control rounded-4 bg-light border-light p-3 text-start" wire:model="improvement_notes" rows="3" placeholder="اكتب مقترحاتك وملاحظاتك التطويرية..."></textarea>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-success w-100 rounded-pill py-3 fw-bold shadow-sm d-flex align-items-center justify-content-center gap-2 mt-3 fs-6" style="background: linear-gradient(135deg, #28a745, #20c997); border: none;">
                            <i class="bi bi-send-check-fill fs-5"></i>
                            <span>اعتماد وإرسال استمارة التقييم الشاملة</span>
                        </button>
                    </form>
                @endif
            </div>

            <div class="text-center mt-3 text-muted" style="font-size: 0.75rem;">
                نظام إدارة التدريب والتعليم الطبي © {{ date('Y') }}
            </div>
        </div>
    </div>
</div>
