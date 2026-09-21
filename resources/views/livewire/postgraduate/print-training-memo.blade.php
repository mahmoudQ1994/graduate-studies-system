<div class="container-fluid py-3" x-data="{ activeTab: 'page1' }">
    <style>
        .printable-page {
            border: 3px solid #333;
            background: #fff;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            padding: 30px;
            margin-bottom: 40px;
            position: relative;
            display: none;
            flex-direction: column;
            justify-content: space-between;
        }
        #page1-content {
            min-height: 85vh;
        }
        #page2-content {
            min-height: 110vh;
        }
        .printable-page.active-tab {
            display: flex;
        }
        .print-footer {
            border-top: 2px solid #ddd;
            padding-top: 6px;
            margin-top: 25px;
        }

        .letter-nav-icon {
            cursor: pointer;
            transition: all 0.2s ease-in-out;
            border: 2px solid #dee2e6;
        }
        .letter-nav-icon:hover {
            border-color: #0d6efd;
            background-color: #f8f9fa;
            transform: translateY(-2px);
        }
        .letter-nav-icon.active {
            border-color: #0d6efd;
            background-color: #e7f1ff;
            color: #0d6efd !important;
        }

        /* إعدادات الطباعة المخصصة لكل صفحة (أفقي للأولى وعمودي للثانية) */
        @page landscape-page {
            size: A4 landscape;
            margin: 11mm;
        }
        @page portrait-page {
            size: A4 portrait;
            margin: 8mm;
        }

        @media print {
            @page {
                size: auto;
                margin: 10mm;
            }

            body * {
                visibility: hidden !important;
            }

            .printable-page.active-tab,
            .printable-page.active-tab * {
                visibility: visible !important;
            }

            #page1-content.active-tab {
                 page: portrait-page;
                position: absolute !important;
                top: 0 !important;
                right: 0 !important;
                left: 0 !important;
                width: 100% !important;
                height: 277mm !important;
                min-height: auto !important;
                background: #fff !important;
                border: 3px double #333 !important;
                box-shadow: none !important;
                padding: 6mm 8mm !important;
                margin: 0 !important;
                z-index: 999999 !important;
            }

            #page2-content.active-tab {
                page: portrait-page;
                position: absolute !important;
                top: 0 !important;
                right: 0 !important;
                left: 0 !important;
                width: 100% !important;
                height: 277mm !important;
                min-height: auto !important;
                background: #fff !important;
                border: 3px double #333 !important;
                box-shadow: none !important;
                padding: 6mm 8mm !important;
                margin: 0 !important;
                z-index: 999999 !important;
            }
            #page2-content p,
            #page2-content li {
                text-align: justify !important;
                text-justify: inter-word !important;
            }

            .no-print {
                display: none !important;
            }
        }
    </style>

    <!-- صندوق البحث (يختفي عند الطباعة) -->
    <div class="card border-0 shadow-sm mb-4 no-print">
        <div class="card-body p-3">
            <h5 class="fw-bold text-primary mb-2" style="font-size: 14px;">
                <i class="bi bi-printer-fill me-2"></i>طباعة المستندات الرسمية (مذكرة العرض وخطاب الإفادة)
            </h5>
            <p class="text-muted small mb-3">أدخل الرقم القومي للطبيب (14 رقم) لتظهر لك أيقونات اختيار وطباعة المستندات.</p>

            <div class="row">
                <div class="col-md-5">
                    <label class="form-label fw-bold extra-small text-secondary">الرقم القومي:</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-white"><i class="bi bi-search text-primary"></i></span>
                        <input type="text"
                               wire:model.live.debounce.300ms="search_national_id"
                               maxlength="14"
                               class="form-control"
                               placeholder="أكتب الرقم القومي هنا...">
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(!empty($search_national_id))
        @if(strlen($search_national_id) === 14)
            @if($trainingRecord)

                <!-- أيقونات التنقل بين الخطابات (يختفي عند الطباعة) -->
                <div class="row mb-4 no-print">
                    <div class="col-12">
                        <div class="d-flex gap-3 align-items-center">

                            <div @click="activeTab = 'page1'"
                                 :class="{ 'active': activeTab === 'page1' }"
                                 class="letter-nav-icon p-3 rounded text-center flex-grow-1 shadow-sm bg-white">
                                <i class="bi bi-file-text fs-3 text-primary mb-1 d-block"></i>
                                <span class="fw-bold text-dark" style="font-size: 13px;">مذكرة العرض للمديرية (الصفحة الأولى)</span>
                            </div>

                            <div @click="activeTab = 'page2'"
                                 :class="{ 'active': activeTab === 'page2' }"
                                 class="letter-nav-icon p-3 rounded text-center flex-grow-1 shadow-sm bg-white">
                                <i class="bi bi-envelope-paper fs-3 text-success mb-1 d-block"></i>
                                <span class="fw-bold text-dark" style="font-size: 13px;">خطاب الإفادة (الصفحة الثانية)</span>
                            </div>

                            <div class="ms-auto">
                                <button onclick="window.print()" class="btn btn-primary px-4 py-3 shadow-sm h-100 fw-bold">
                                    <i class="bi bi-printer me-1"></i> طباعة المستند الحالي
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12 mt-3">

                        <!-- الصفحة الأولى: مذكرة العرض للمديرية -->
                        <div id="page1-content" class="printable-page rounded" :class="{ 'active-tab': activeTab === 'page1' }">
                            <div>
                                <div class="d-flex justify-content-between align-items-start mb-2 pb-2 border-bottom">
                                    <div class="text-center" style="width: fit-content;">
                                        <div class="fw-bold text-dark" style="font-size: 12px; margin-bottom: 2px;">{{ optional($settings)->governorate_name }}</div>
                                        <div class="fw-bold text-dark" style="font-size: 12px; margin-bottom: 2px;">{{ optional($settings)->directorate_name }}</div>
                                        <div class="fw-bold text-secondary" style="font-size: 12px; margin-bottom: 1px;">{{ optional($settings)->administration_name }}</div>
                                        <div class="fw-bold text-secondary" style="font-size: 12px;">{{ optional($settings)->department_name }}</div>
                                    </div>
                                    <div class="text-start">
                                        @if(optional($settings)->logo_path)
                                            <img src="{{ asset('storage/' . $settings->logo_path) }}" alt="Logo"
                                            style="height: 75px; width: auto;" class="object-fit-contain">
                                        @endif
                                    </div>
                                </div>

                                <div class="text-center my-3">
                                    <h5 class="fw-bold" style="font-size: 22px; font-family: 'Almarai', sans-serif;">مذكرة للعرض على السيد الدكتور / وكيل الوزارة</h5>
                                    <p class="fw-bold mt-1" style="font-size: 22px; font-family: 'Almarai', sans-serif;">تحية تقدير واحترام ،،،</p>
                                </div>

                                <div class="mb-2 text-justify" style="font-size: 15px;">
                                    إذ نتهز الفرصة لنتقدم لسيادتكم بأسمى آيات الشكر والتقدير لما تقدمونه من دعم وتوجيهات للإرتقاء بمنظومة التعليم الطبي والتدريب بمحافظة سوهاج.
                                </div>

                                <div class="mb-2">
                                    <p class="mb-1 fw-bold" style="font-family: 'Almarai', sans-serif; font-size: 18px;">الموضوع :</p>
                                    <p class="text-justify mb-2" style="font-size: 18px; font-family: 'Times New Roman', Times, serif;">
                                        بخصوص {{ $trainingRecord->action_type }} للتدريب السيد (ة) / {{ optional($trainingRecord->healthProfessional)->name ?? '-' }}
                                        - {{ optional($trainingRecord->healthProfessional)->profession ?? '-' }}
                                       @php
                                            $latestMovement = optional(optional($trainingRecord->healthProfessional)->medicalMovements)->first();
                                        @endphp

                                        تخصص  {{ optional($latestMovement)->specialty ?? '-' }}
                                        وجهة العمل الاصلية هى {{ optional(optional($trainingRecord->healthProfessional)->facility)->name ?? '-' }} .
                                    </p>
                                </div>

                                <div class="mb-2" style="font-size: 18px; line-height: 1.5;">
                                    <p class="fw-bold mb-1" style="font-family: 'Almarai', sans-serif; font-size: 18px;">النقاط الأساسية :-</p>
                                    <div class="pe-3" style="font-size: 18px; font-family: 'Times New Roman', Times, serif;">
                                        • تقدمت إلينا السيد (ة) /
                                        {{ optional($trainingRecord->healthProfessional)->name ?? '-' }}
                                        - {{ optional($trainingRecord->healthProfessional)->profession ?? '-' }}
                                         @php
                                            $latestMovement = optional(optional($trainingRecord->healthProfessional)->medicalMovements)->first();
                                        @endphp
                                        تخصص  {{ optional($latestMovement)->specialty ?? '-' }}
                                        وجهة العمل الاصلية هى {{ optional(optional($trainingRecord->healthProfessional)->facility)->name ?? '-' }}، بطلب للموافقة على {{ $trainingRecord->action_type }} للتدريب لمدة {{ $trainingRecord->duration_months }} أشهر
                                        @php
                                            $days = is_array($trainingRecord->training_days) ? $trainingRecord->training_days : [];
                                        @endphp
                                        @if(count($days) > 0 && count($days) < 7)
                                            بدوام جزئي أيام ({{ implode('، ', $days) }}) من كل أسبوع.
                                        @endif
                                        ، وذلك بمستشفى {{ $trainingRecord->training_entity }} بقسم {{ $trainingRecord->training_department }}، وذلك بداية من {{ $trainingRecord->start_date }} .
                                    </div>
                                </div>

                                <div class="mb-2">
                                    <p class="mb-1 fw-bold" style="font-family: 'Almarai', sans-serif; font-size: 18px;">العرض :</p>
                                    <p class="text-justify mb-2" style="font-size: 18px; font-family: 'Times New Roman', Times, serif;">
                                        الرجاء من سيادتكم التكرم بالموافقة على {{ $trainingRecord->action_type }} للتدريب المذكور( ة ) لمدة {{ $trainingRecord->duration_months }} أشهر
                                        @if(count($days) > 0 && count($days) < 7)
                                            بدوام جزئي أيام ({{ implode('، ', $days) }}) من كل أسبوع.
                                        @endif
                                        ، وذلك بمستشفى {{ $trainingRecord->training_entity }} بقسم {{ $trainingRecord->training_department }}، وذلك بداية من {{ $trainingRecord->start_date }} .
                                    </p>
                                </div>

                                <div class="text-center mb-2 fw-bold" style="font-family: 'Almarai', sans-serif; font-size: 22px;">
                                    وتفضلوا سيادتكم بقبول وافر الاحترام والتقدير ،،،
                                </div>

                                <div class="text-start mb-2 fw-bold" style="font-size: 13px;">
                                    تحريراً في: {{ date('Y/m/d') }}م
                                </div>

                                <div class="row mt-4" style="font-size: 14px; font-family: 'Almarai', sans-serif;">
                                <!-- الجزء الأيمن: نظم المعلومات والمشرف العام -->
                                    <div class="row text-center fw-bold mt-2" style="font-size: 16px;">
                                    <div style="line-height: 1.5; font-size: 13px;" class="col-6 text-muted">
                                        نظم معلومات<br>
                                        المنح والبعثات والدراسات العليا<br>
                                        والتعليم الطبي والتدريب
                                    </div>
                                    <div class="col-6">
                                        <div class="mb-1">المشرف العام</div>
                                        <div class="text-muted mb-4" style="font-size: 16px;">علي التعليم الطبي والتدريب</div>
                                        <div>{{ optional($officialHeader)->manager_name ?? 'د / الحسيني الجارحي' }}</div>
                                    </div>
                                </div>

                                <!-- الجزء الأيسر: رأي وكيل الوزارة والنقاط والتوقيع -->
                                <div class="col-12">
                                    <div class=" mb-2 fw-bold"
                                     style="font-family: 'Almarai', sans-serif; font-size: 22px">رأي السيد الدكتور / وكيل الوزارة</div>
                                    <div class="mb-2" style="border-bottom: 1px dotted #333; height: 20px;"></div>
                                    <div class="mb-3" style="border-bottom: 1px dotted #333; height: 20px;"></div>
                                    <div class="d-flex justify-content-between align-items-End px-4 mt-3 col-12">
                                        <div class="col-7"></div>
                                        <div class="col-5 text-center">
                                            <div class=" fw-bold" style="font-family: 'Almarai', sans-serif; font-size: 20px" >وكيل الوزارة</div>
                                            <div class="fw-bold mt-2" style="font-family: 'Almarai', sans-serif; font-size: 20px">{{ optional($officialHeader)->undersecretary_name ?? 'د/ أحمد رفعت عبد القادر' }}</div>
                                        </div>
                                    </div>
                                </div>
                        </div>
                            </div>

                            <div class="print-footer w-100 text-muted" style="font-size: 9px;">
                                <div class="row align-items-center m-0">
                                    <div class="col-5 text-end">العنوان: {{ optional($officialHeader)->detailed_address }}</div>
                                    <div class="col-4 text-center">البريد الإلكتروني: {{ optional($officialHeader)->official_email }}</div>
                                    <div class="col-3 text-start">رقم التواصل: {{ optional($officialHeader)->phone_fax }}</div>
                                </div>
                            </div>
                        </div>


                        <!-- الصفحة الثانية: خطاب الإفادة -->
                        <div id="page2-content" class="printable-page rounded" :class="{ 'active-tab': activeTab === 'page2' }">
                            <div>
                                <div class="d-flex justify-content-between align-items-start mb-2 pb-2 border-bottom">
                                    <div class="text-center" style="width: fit-content;">
                                        <div class="fw-bold text-dark" style="font-size: 12px; margin-bottom: 2px;">{{ optional($settings)->governorate_name }}</div>
                                        <div class="fw-bold text-dark" style="font-size: 12px; margin-bottom: 2px;">{{ optional($settings)->directorate_name }}</div>
                                        <div class="fw-bold text-secondary" style="font-size: 12px; margin-bottom: 1px;">{{ optional($settings)->administration_name }}</div>
                                        <div class="fw-bold text-secondary" style="font-size: 12px;">{{ optional($settings)->department_name }}</div>
                                    </div>
                                    <div class="text-start">
                                        @if(optional($settings)->logo_path)
                                            <img src="{{ asset('storage/' . $settings->logo_path) }}" alt="Logo"
                                            style="height: 75px; width: auto;" class="object-fit-contain">
                                        @endif
                                    </div>
                                </div>

                                <div class="text-center my-2">
                                    <h4 class="fw-bold"
                                    style="font-size: 20px; font-family: 'Almarai', sans-serif;">إفــــــــادة</h4>
                                </div>

                                <div class="text-center my-2 fw-bold"
                                style="font-size: 20px; font-family: 'Almarai', sans-serif;">
                                    السيد الاستاذ / مدير ادارة الموارد البشرية بمديرية الشئون الصحية بسوهاج<br>
                                    <span class="fw-bold"
                                    style="font-size: 22px; font-family: 'Almarai', sans-serif;">تحية طيبة وبعد ،،،</span>
                                </div>

                                <div class="mb-2">
                                    <p class="mb-1 fw-bold" style="font-family: 'Almarai', sans-serif; font-size: 22px;">الموضوع :-</p>
                                    <p class="text-justify mb-2" style="font-size: 18px; font-family: 'Times New Roman', Times, serif;">
                                        بخصوص {{ $trainingRecord->action_type }} التدريب السيد (ة) / {{ optional($trainingRecord->healthProfessional)->name ?? '-' }}
                                        - {{ optional($trainingRecord->healthProfessional)->profession ?? '-' }}
                                        @php
                                            $latestMovement = optional(optional($trainingRecord->healthProfessional)->medicalMovements)->first();
                                        @endphp

                                        تخصص  {{ optional($latestMovement)->specialty ?? '-' }} .
                                    </p>
                                </div>

                                <div class="mb-1" style="font-size: 18px; line-height: 1.5;">
                                    <p class="fw-bold mb-1" style="font-family: 'Almarai', sans-serif; font-size: 22px;">النقاط الأساسية :-</p>
                                    <div class="pe-2" style="font-size: 18px; font-family: 'Times New Roman', Times, serif;">
                                        • تقدمت إلينا السيد (ة) / {{ optional($trainingRecord->healthProfessional)->name ?? '-' }} - {{ optional($trainingRecord->healthProfessional)->profession ?? '-' }}
                                        @php
                                            $latestMovement = optional(optional($trainingRecord->healthProfessional)->medicalMovements)->first();
                                        @endphp
                                        @if($latestMovement && (!empty($latestMovement->specialty) || !empty($latestMovement->movement_date)))
                                            تخصص  <span>{{ optional($latestMovement)->specialty ?? '—' }}  </span>
                                        @endif
                                        وجهة العمل الأصلية هي {{ optional(optional($trainingRecord->healthProfessional)->facility)->name ?? '-' }}،
                                        بطلب للموافقة على مد إفادها للتدريب لمدة {{ $trainingRecord->duration_months }} أشهر
                                        @php
                                            $days = is_array($trainingRecord->training_days) ? $trainingRecord->training_days : [];
                                        @endphp
                                        @if(count($days) > 0 && count($days) < 7)
                                            بدوام جزئي أيام ({{ implode('، ', $days) }}) من كل أسبوع
                                        @endif
                                        ، وذلك بمستشفى {{ $trainingRecord->training_entity }}
                                        بقسم {{ $trainingRecord->training_department }}،
                                        وذلك بداية من {{ $trainingRecord->start_date }}.<br>
                                        • تم العرض على السيد الدكتور وكيل الوزارة للتكرم بالموافقة على مد إفاد المذكور(ة) للتدريب لمدة {{ $trainingRecord->duration_months }} أشهر
                                        @if(count($days) > 0 && count($days) < 7)
                                            بدوام جزئي أيام ({{ implode('، ', $days) }}) من كل أسبوع
                                        @endif
                                        ، وذلك ب{{ $trainingRecord->training_entity }} بقسم {{ $trainingRecord->training_department }}، وذلك بداية من {{ $trainingRecord->start_date }}، وقد تأشر من سيادته " لا مانع ".
                                    </div>
                                </div>

                                <div class="mb-2">
                                    <p class="mb-1 fw-bold" style="font-family: 'Almarai', sans-serif; font-size: 22px;">العرض :-</p>
                                    <p class="text-justify mb-2" style="font-size: 18px; font-family: 'Times New Roman', Times, serif;">
                                        مرسل لسيادتكم لاتخاذ ما يلزم في ضوء ما تأشر.
                                    </p>
                                </div>
                                 <div class="mb-2 fw-bold text-center w-100"
                                 style="font-family: 'Almarai', sans-serif; font-size: 22px;">
                                    وتفضلوا سيادتكم بقبول وافر الاحترام والتقدير ،،،
                                </div>

                                <div class="text-start mb-2 fw-bold" style="font-size: 13px;">
                                    تحريراً في: {{ date('Y/m/d') }}م
                                </div>

                                <div class="row text-center fw-bold mt-2" style="font-size: 16px;">
                                    <div style="line-height: 1.5; font-size: 13px;" class="col-6 text-muted">
                                        نظم معلومات<br>
                                        المنح والبعثات والدراسات العليا<br>
                                        والتعليم الطبي والتدريب
                                    </div>
                                    <div class="col-6">
                                        <div class="mb-1">المشرف العام</div>
                                        <div class="text-muted mb-4" style="font-size: 16px;">علي التعليم الطبي والتدريب</div>
                                        <div>{{ optional($officialHeader)->manager_name ?? 'د / الحسيني الجارحي' }}</div>
                                    </div>
                                </div>
                            </div>

                            <div class="print-footer w-100 text-muted" style="font-size: 9px;">
                                <div class="row align-items-center m-2">
                                    <div class="col-5 text-end">العنوان: {{ optional($officialHeader)->detailed_address }}</div>
                                    <div class="col-4 text-center">البريد الإلكتروني: {{ optional($officialHeader)->official_email }}</div>
                                    <div class="col-3 text-start">رقم التواصل: {{ optional($officialHeader)->phone_fax }}</div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

            @else
                <div class="alert alert-warning text-center p-3">
                    <i class="bi bi-exclamation-triangle me-2"></i>لا يوجد سجل تدريبي مرتبط بهذا الرقم القومي.
                </div>
            @endif
        @else
            <div class="alert alert-info text-center p-2" style="font-size: 11px;">
                يرجى استكمال الرقم القومي ليكون 14 رقماً (المدخل حالياً: {{ strlen($search_national_id) }} أرقام)...
            </div>
        @endif
    @endif
</div>
