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
                page: landscape-page;
                position: fixed !important;
                top: 0 !important;
                right: 0 !important;
                left: 0 !important;
                width: 100% !important;
                height: 185mm !important;
                background: #fff !important;
                border: 3px double #333 !important;
                box-shadow: none !important;
                padding: 10mm !important;
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
                <i class="bi bi-printer-fill me-2"></i>طباعة المستندات الرسمية (كشف المرشحين والخطاب الإداري)
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
            @if($candidate)

                <!-- أيقونات التنقل بين الخطابات (يختفي عند الطباعة) -->
                <div class="row mb-4 no-print">
                    <div class="col-12">
                        <div class="d-flex gap-3 align-items-center">

                            <div @click="activeTab = 'page1'"
                                 :class="{ 'active': activeTab === 'page1' }"
                                 class="letter-nav-icon p-3 rounded text-center flex-grow-1 shadow-sm bg-white">
                                <i class="bi bi-file-earmark-spreadsheet fs-3 text-primary mb-1 d-block"></i>
                                <span class="fw-bold text-dark" style="font-size: 13px;">كشف المرشحين (الصفحة الأولى - أفقي)</span>
                            </div>

                            <div @click="activeTab = 'page2'"
                                 :class="{ 'active': activeTab === 'page2' }"
                                 class="letter-nav-icon p-3 rounded text-center flex-grow-1 shadow-sm bg-white">
                                <i class="bi bi-file-earmark-text fs-3 text-success mb-1 d-block"></i>
                                <span class="fw-bold text-dark" style="font-size: 13px;">الخطاب الإداري للوزارة (الصفحة الثانية - عمودي)</span>
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

                        <!-- الصفحة الأولى: كشف المرشحين (أفقي Landscape) -->
                        <div id="page1-content" class="printable-page rounded" :class="{ 'active-tab': activeTab === 'page1' }">
                            <div>
                                <div class="d-flex justify-content-between align-items-start mb-2 pb-2 border-bottom">
                                    <div class="text-center" style="width: fit-content;">
                                        <div class="fw-bold text-dark" style="font-size: 13px; margin-bottom: 2px;">{{ optional($settings)->governorate_name }}</div>
                                        <div class="fw-bold text-dark" style="font-size: 13px; margin-bottom: 2px;">{{ optional($settings)->directorate_name }}</div>
                                        <div class="fw-bold text-secondary" style="font-size: 13px; margin-bottom: 1px;">{{ optional($settings)->administration_name }}</div>
                                        <div class="fw-bold text-secondary" style="font-size: 13px;">{{ optional($settings)->department_name }}</div>
                                    </div>
                                    <div class="text-start">
                                        @if(optional($settings)->logo_path)
                                            <img src="{{ asset('storage/' . $settings->logo_path) }}" alt="Logo"
                                            style="height: 75px; width: auto;" class="object-fit-contain">
                                        @endif
                                    </div>
                                </div>

                                <div class="text-center mb-2">
                                    <h6 class="fw-bold text-dark mb-1"
                                    style="font-size: 15px; text-decoration: underline;">
                                        كشف بأسماء السادة أعضاء المهن الطبية المستوفين لشروط الترشح طبقاً لشروط قانون 149 لسنة 2020
                                    </h6>
                                    <div class="fw-bold text-muted" style="font-size: 14px;">
                                        وشروط الإدارة العامة للمنح والبعثات للعام الدراسي {{ optional($settings)->fiscal_year }} م
                                    </div>
                                </div>

                                <div class="table-responsive mb-3">
                                    <table class="table table-bordered align-middle text-center" style="font-size: 10px;">
                                        <thead class="table-light">
                                            <tr>
                                                <th>م</th>
                                                <th>الاسم</th>
                                                <th>الرقم القومي</th>
                                                <th>رقم التليفون</th>
                                                <th>الدراسة المطلوبة</th>
                                                <th>تخصص الدراسة المطلوبة  </th>
                                                <th>تخصص وتاريخ النيابة </th>
                                                <th>التقدير العام </th>
                                                <th> تقدير المادة  </th>
                                                <th>  المجموع التراكمى  </th>
                                                <th>جامعة التخرج</th>
                                                <th>الجامعة المطلوبة</th>
                                                <th>نوع الترشح</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>1</td>
                                                <td class="fw-bold text-dark text-nowrap">{{ $candidate->name }}</td>
                                                <td class="font-monospace">{{ $candidate->national_id }}</td>
                                                <td class="font-monospace">{{ $candidate->phone ?? '—' }}</td>
                                                <td>{{ optional($latestRegistration)->required_degree ?? '—' }}</td>
                                                <td>{{ optional($latestRegistration)->required_specialty ?? '—' }} </td>
                                                <br>
                                                <td>
                                                    @php $latestMovement = optional($candidate->medicalMovements)->first(); @endphp
                                                    {{ optional($latestMovement)->specialty ?? '—' }}
                                                    <br>  {{ optional($latestMovement)->movement_date ?? '—' }}
                                                </td>
                                                <td>{{ optional($candidate->qualification)->general_grade ?? '—' }}</td>
                                                <td>{{ optional($candidate->qualification)->subject_grade ?? '—' }}</td>
                                                <td>{{ optional($candidate->qualification)->total_marks ?? '—' }}</td>

                                                <td>{{ optional($candidate->qualification)->university ?? '—' }}</td>
                                                <td>{{ optional($latestRegistration)->required_university ?? '—' }}</td>
                                                <td>{{ optional($latestRegistration)->sponsorship_type ?? 'ترشح وزاري استثنائي' }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <div class="row text-center fw-bold mt-2" style="font-size: 14px;">
                                    <div class="col-4"><div class="mb-3">مسئول نظم المعلومات</div></div>
                                    <div class="col-4">
                                        <div class="mb-1">المشرف العام</div>
                                        <div class="text-muted mb-3" style="font-size: 14px;">مدير التعليم الطبي والتدريب</div>
                                        <div>{{ optional($officialHeader)->manager_name ?? '—' }}</div>
                                    </div>
                                    <div class="col-4">
                                        <div class="mb-5">وكيل الوزارة</div>
                                        <div>{{ optional($officialHeader)->undersecretary_name ?? '—' }}</div>
                                    </div>
                                </div>
                            </div>

                            <div class="print-footer w-100 text-muted" style="font-size: 9px;">
                                <div class="row align-items-center m-0">
                                    <div class="col-4 text-end">العنوان: {{ optional($officialHeader)->detailed_address }}</div>
                                    <div class="col-4 text-center">البريد الإلكتروني: {{ optional($officialHeader)->official_email }}</div>
                                    <div class="col-4 text-start">رقم التواصل: {{ optional($officialHeader)->phone_fax }}</div>
                                </div>
                            </div>
                        </div>


                        <!-- الصفحة الثانية: الخطاب الإداري (عمودي Portrait مطابق للصورة) -->
                        <div id="page2-content" class="printable-page rounded" :class="{ 'active-tab': activeTab === 'page2' }">
                            <div>
                                <!-- ترويسة المستند مطابقة تماماً للصورة -->
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

                                <!-- جهة الإرسال والتحية -->
                                <div class=" text-center mb-3 fw-bold" style="font-size: 18px; line-height: 1.7;">
                                    <div>السيدة الأستاذة الدكتورة / مدير عام الإدارة العامة للمنح والبعثات</div>
                                    <div class="mt-1">بوزارة الصحة والسكان</div>
                                    <div class="text-center my-2">تحية طيبة وبعد ،,,</div>
                                </div>

                                <!-- الفقرة التمهيدية -->
                                <div class="mb-4" style="font-size: 15px; text-align: justify; line-height: 1.7;">
                                    إذ ننتهز الفرصة لنتقدم لسيادتكم بأسمى آيات الشكر والتقدير لما تقدمونه من دعم وتوجيهات للإرتقاء بمنظومة التعليم الطبي والتدريب بمحافظة سوهاج.
                                </div>

                                <!-- الموضوع -->
                                <div class="mb-3 fw-bold" style="font-size: 17px;">
                                    الموضوع :- بخصوص التقدم للدراسات العليا للعام الدراسي {{ optional($settings)->fiscal_year ?? '2027/2026' }}.
                                </div>

                                <!-- النقاط الأساسية والتفاصيل -->
                                <div class="mb-4" style="font-size: 16px; line-height: 1.7; justify-content: center;">
                                    <div class="fw-bold mb-1">النقاط الأساسية :-</div>
                                    <div class="pe-3 ">
                                        • إيماءاً إلى إستمارة الترشح الصادرة عن الموقع الإلكتروني للإدارة العامة للمنح والبعثات بوزارة الصحة والسكان والتي تقدم (ت)
                                         بها إلينا السيد ( ة)
                                        <span > /  {{ $candidate->name }} -
                                            بوظيفة  {{ $candidate->profession ?? 'طبيب بشري' }} ،
                                            جهة العمل الاصلية  :  {{ optional($candidate->facility)->name }}
                                            @if(!empty(optional($candidate)->secondment_facility))
                                            ومنتدب الى : {{ $candidate->secondment_facility ?? ' ' }}
                                            @endif
                                        </span>،
                                              @php
                                              $latestMovement = optional($candidate->medicalMovements)->first();
                                              @endphp
                                              @if($latestMovement && (!empty($latestMovement->specialty) || !empty($latestMovement->movement_date)))
                                              <span >/  {{ optional($latestMovement)->specialty ?? '—' }}
                                              بتاريخ {{ optional($latestMovement)->movement_date ?? '—' }}</span>
                                              @endif
                                              بخصوص الترشح لدراسة ماجستير في تخصص ،
                                        <span >({{ optional($latestRegistration)->required_specialty ?? 'أطفال' }})</span>
                                        بجامعة {{ optional($latestRegistration)->required_university ?? '—' }}
                                        ، وذلك ضمن حركة الترشيح
                                        {{ optional($latestRegistration)->sponsorship_type ?? '—' }}
                                        للدراسات العليا، للعام الدراسي
                                        {{ optional($settings)->fiscal_year ?? '2027/2026' }} - مرفق استمارة الترشح.
                                    </div>
                                    <div class="mb-3 fw-bold" style="font-size: 17px;">
                                        العرض :- الرجاء من سيادتكم التكرم بالتوجيه بما ترونه مناسباً في هذا الشأن .
                                    </div>
                                    <div class=" text-center mb-2 fw-bold" style="font-size: 18px; line-height: 1;">
                                        وتفضلوا سيادتكم بقبول وافر الاحترام والتقدير ،،،
                                    </div>
                                </div>

                                <!-- التاريخ وتنسيق التوقيعات المطابق للصورة تماماً -->
                                <div class="text-first mb-2 fw-bold" style="font-size: 13px;">
                                    تحريراً في: {{ date('Y/m/d') }}م
                                </div>

                                <div>
                                    <div class="row text-center fw-bold mt-2 pt-2" style="font-size: 18px;">
                                        <div style="line-height: 1.5; font-size: 12px; text-first" class=" col-4 text-secondary mb-4">
                                            نظم معلومات<br>
                                            المنح والبعثات والدراسات العليا<br>
                                            والتعليم الطبي والتدريب
                                        </div>
                                    </div>
                                </div>

                                <div class="row text-center fw-bold mt-2 pt-2" style="font-size: 18px;">
                                    <div class="col-6">
                                        <div class="mb-1">المشرف العام</div>
                                        <div class="text-muted mb-3" style="font-size: 16px;">علي التعليم الطبي والتدريب</div>
                                        <div>{{ optional($officialHeader)->manager_name ?? 'د / الحسيني الجارحي' }}</div>
                                    </div>
                                    <div class="col-6">
                                        <div class="mb-5">وكيل الوزارة</div>
                                        <div>{{ optional($officialHeader)->undersecretary_name ?? 'د/ أحمد رفعت عبد القادر' }}</div>
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
                    <i class="bi bi-exclamation-triangle me-2"></i>لا يوجد طبيب مسجل بهذا الرقم القومي.
                </div>
            @endif
        @else
            <div class="alert alert-info text-center p-2" style="font-size: 11px;">
                يرجى استكمال الرقم القومي ليكون 14 رقماً (المدخل حالياً: {{ strlen($search_national_id) }} أرقام)...
            </div>
        @endif
    @endif
</div>
