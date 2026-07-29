<div id="printArea" class="bg-white overflow-y-auto flex-1 custom-scrollbar w-full overflow-x-hidden flex justify-center" style="font-family: 'Hind Siliguri', sans-serif;">
                <style>
                    .adm-form-container {
                        width: 650px;
                        max-width: 650px;
                        margin: 0 auto;
                        background-color: #fff;
                        border: none;
                        box-sizing: border-box;
                        position: relative;
                        transform-origin: top center;
                    }
                    @media (max-width: 670px) {
                        .adm-form-container {
                            zoom: calc((100vw - 24px) / 650);
                        }
                    }
                    /* Watermark */
                    .adm-watermark {
                        position: absolute;
                        top: 50%;
                        left: 50%;
                        transform: translate(-50%, -40%);
                        width: 60%;
                        max-width: 400px;
                        opacity: 0.04;
                        pointer-events: none;
                        z-index: 0;
                    }
                    .adm-watermark img {
                        width: 100%;
                        height: auto;
                        object-fit: contain;
                    }
                    .adm-watermark .adm-watermark-icon {
                        font-size: 220px;
                        color: #008444;
                    }
                    /* Header */
                    .adm-header {
                        background-color: #008444;
                        color: white;
                        text-align: center;
                        padding: 15px 10px 15px 90px;
                        position: relative;
                        border-bottom: 5px solid #8cc63f;
                    }
                    .adm-header-logo {
                        position: absolute;
                        left: 20px;
                        top: 50%;
                        transform: translateY(-50%);
                        width: 70px;
                        height: 70px;
                        border-radius: 50%;
                        background-color: #fff;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        overflow: hidden;
                        border: 2px solid #fff;
                    }
                    .adm-header-logo img {
                        width: 100%;
                        height: 100%;
                        object-fit: cover;
                    }
                    .adm-header h1 {
                        margin: 0;
                        font-size: 26px;
                        font-weight: 700;
                        letter-spacing: 1px;
                        line-height: 1.3;
                    }
                    .adm-contact-info {
                        margin-top: 8px;
                        font-size: 13px;
                    }
                    .adm-contact-info span {
                        margin: 0 8px;
                    }
                    .adm-address-info {
                        margin-top: 5px;
                        font-size: 12px;
                        opacity: 0.92;
                    }
                    /* Form Body */
                    .adm-form-body {
                        padding: 20px 24px;
                        position: relative;
                        z-index: 1;
                    }
                    /* Title Area */
                    .adm-title-area {
                        text-align: center;
                        margin-bottom: 20px;
                        position: relative;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                    }
                    .adm-form-title {
                        background-color: #e60000;
                        color: white;
                        font-size: 15px;
                        font-weight: bold;
                        padding: 3px 20px;
                        border-radius: 0px;
                        display: inline-block;
                    }
                    .adm-date-section {
                        position: absolute;
                        right: 0;
                        top: 50%;
                        transform: translateY(-50%);
                        font-size: 14px;
                        display: flex;
                        align-items: center;
                        gap: 6px;
                        color: #333;
                    }
                    .adm-date-box {
                        border: 1px solid #777;
                        padding: 2px 12px;
                        border-radius: 0px;
                        font-family: monospace;
                        font-size: 13px;
                        background: #fff;
                    }
                    /* Top Meta Section */
                    .adm-top-meta {
                        display: flex;
                        gap: 16px;
                        margin-bottom: 20px;
                    }
                    .adm-photo-box {
                        width: 105px;
                        height: 125px;
                        border: 1px solid #008444;
                        border-radius: 0px;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        color: #999;
                        font-size: 14px;
                        flex-shrink: 0;
                        overflow: hidden;
                        background: #fafafa;
                    }
                    .adm-photo-box img {
                        width: 100%;
                        height: 100%;
                        object-fit: cover;
                    }
                    .adm-meta-inputs {
                        flex: 1;
                        display: flex;
                        flex-direction: column;
                        justify-content: center;
                        gap: 8px;
                    }
                    .adm-meta-row {
                        display: flex;
                        align-items: center;
                    }
                    .adm-meta-label {
                        background-color: #008444;
                        color: white;
                        padding: 4px 12px;
                        font-size: 14px;
                        min-width: 90px;
                        text-align: center;
                        border-radius: 0px;
                        margin-right: 10px;
                        white-space: nowrap;
                        font-weight: 600;
                    }
                    .adm-meta-value {
                        flex-grow: 1;
                        border: 1px solid #999;
                        height: 28px;
                        border-radius: 0px;
                        display: flex;
                        align-items: center;
                        padding: 0 10px;
                        font-size: 13px;
                        font-weight: 600;
                        color: #222;
                        font-family: monospace;
                    }
                    .adm-meta-half-row {
                        display: flex;
                        gap: 10px;
                    }
                    .adm-meta-half {
                        flex: 1;
                        display: flex;
                        align-items: center;
                    }
                    /* Section Header */
                    .adm-section-header {
                        background-color: #008444;
                        color: white;
                        padding: 4px 15px;
                        font-size: 15px;
                        font-weight: 600;
                        display: inline-block;
                        border-radius: 0px;
                        margin-top: 10px;
                        margin-bottom: 14px;
                    }
                    /* Form Row */
                    .adm-form-row {
                        display: flex;
                        align-items: flex-end;
                        margin-bottom: 14px;
                        font-size: 14px;
                    }
                    .adm-label-text {
                        width: 130px;
                        font-weight: 600;
                        color: #111;
                        flex-shrink: 0;
                    }
                    .adm-colon {
                        width: 15px;
                        text-align: center;
                        flex-shrink: 0;
                    }
                    .adm-input-line {
                        flex-grow: 1;
                        border-bottom: 1px dotted #555;
                        min-height: 22px;
                        padding: 0 6px;
                        font-weight: 600;
                        color: #111;
                    }
                    /* Guardian Split Row */
                    .adm-guardian-row {
                        display: flex;
                        align-items: flex-end;
                        margin-bottom: 14px;
                        font-size: 14px;
                    }
                    .adm-guardian-relation-label {
                        padding: 0 10px;
                        font-weight: bold;
                        white-space: nowrap;
                    }
                    /* Dropdown Grid */
                    .adm-dropdown-grid {
                        display: grid;
                        grid-template-columns: 1fr 1fr;
                        gap: 8px;
                        margin-top: 14px;
                        margin-bottom: 14px;
                    }
                    .adm-dropdown-item {
                        flex: 1;
                        border: 1px solid #008444;
                        display: flex;
                        border-radius: 0px;
                        overflow: hidden;
                        height: 28px;
                    }
                    .adm-dropdown-label {
                        background-color: #008444;
                        color: white;
                        padding: 0 10px;
                        display: flex;
                        align-items: center;
                        font-size: 13px;
                        min-width: 55px;
                        justify-content: center;
                        font-weight: 600;
                        white-space: nowrap;
                    }
                    .adm-dropdown-value {
                        flex-grow: 1;
                        display: flex;
                        align-items: center;
                        padding: 0 8px;
                        font-size: 12px;
                        font-weight: 600;
                        color: #222;
                        background: transparent;
                    }
                    /* Footer */
                    .adm-footer-section {
                        margin-top: 50px;
                        padding-top: 5px;
                        display: flex;
                        justify-content: flex-end;
                    }
                    .adm-signature-area {
                        width: 250px;
                        text-align: center;
                        font-size: 14px;
                        border-top: 1px solid #000;
                        padding-top: 5px;
                        margin-top: 20px;
                        font-weight: 600;
                    }

                    /* ===== Print Fix ===== */
                    @media print {
                        * {
                            -webkit-print-color-adjust: exact !important;
                            print-color-adjust: exact !important;
                        }
                        @page {
                            size: A4 portrait;
                            margin: 4mm 6mm;
                        }
                        html, body {
                            height: 100% !important;
                            overflow: hidden !important;
                        }
                        body * {
                            visibility: hidden !important;
                        }
                        #admissionFormModal,
                        #admissionFormModal * {
                            visibility: visible !important;
                        }
                        #admissionFormModal {
                            position: fixed !important;
                            left: 0 !important;
                            top: 0 !important;
                            width: 100% !important;
                            height: 100% !important;
                            display: block !important;
                            padding: 0 !important;
                            margin: 0 !important;
                            background: white !important;
                            z-index: 99999 !important;
                            overflow: visible !important;
                        }
                        #admissionFormModal > div,
                        #admissionFormModal [class*="max-w-"] {
                            max-width: 100% !important;
                            width: 100% !important;
                            max-height: none !important;
                            height: auto !important;
                            box-shadow: none !important;
                            border: none !important;
                            margin: 0 !important;
                            padding: 0 !important;
                            overflow: visible !important;
                            background: white !important;
                        }
                        #printArea {
                            position: relative !important;
                            left: 0 !important;
                            top: 0 !important;
                            width: 100% !important;
                            height: auto !important;
                            margin: 0 !important;
                            padding: 0 !important;
                            border: none !important;
                            background: white !important;
                            color: black !important;
                            overflow: visible !important;
                        }
                        .adm-form-container {
                            border: none !important;
                            max-width: 100% !important;
                            width: 100% !important;
                            min-width: 0 !important;
                            zoom: 1 !important;
                            transform: none !important;
                            margin: 0 !important;
                            page-break-inside: avoid !important;
                            break-inside: avoid !important;
                        }
                        .no-print,
                        .no-print * {
                            display: none !important;
                            visibility: hidden !important;
                        }
                        /* Proportionate for full A4 page height */
                        .adm-header { padding: 12px 15px 12px 85px !important; }
                        .adm-header h1 { font-size: 22px !important; }
                        .adm-header-logo { width: 62px !important; height: 62px !important; }
                        .adm-contact-info { font-size: 11.5px !important; margin-top: 5px !important; }
                        .adm-address-info { font-size: 11px !important; margin-top: 4px !important; }
                        .adm-form-body { padding: 14px 20px !important; }
                        .adm-form-title { font-size: 16px !important; padding: 3px 20px !important; }
                        .adm-title-area { margin-bottom: 12px !important; }
                        .adm-date-section { font-size: 13px !important; }
                        .adm-date-box { font-size: 12px !important; padding: 2px 8px !important; }
                        .adm-top-meta { margin-bottom: 12px !important; gap: 14px !important; }
                        .adm-photo-box { width: 90px !important; height: 108px !important; }
                        .adm-meta-label { font-size: 12px !important; padding: 3px 8px !important; }
                        .adm-meta-value { font-size: 13px !important; height: 26px !important; }
                        .adm-section-header { font-size: 13.5px !important; padding: 3px 12px !important; margin: 10px 0 8px !important; }
                        .adm-form-row, .adm-guardian-row { margin-bottom: 8.5px !important; font-size: 13px !important; }
                        .adm-label-text { font-size: 13px !important; width: 120px !important; }
                        .adm-input-line { font-size: 13px !important; min-height: 20px !important; }
                        .adm-dropdown-grid { margin-top: 8px !important; margin-bottom: 8px !important; gap: 8px !important; }
                        .adm-dropdown-label { font-size: 11.5px !important; padding: 0 8px !important; }
                        .adm-dropdown-value { font-size: 11.5px !important; padding: 0 8px !important; }
                        .adm-footer-section { margin-top: 35px !important; }
                        .adm-signature-area { font-size: 13px !important; width: 220px !important; margin-top: 20px !important; }
                        .adm-guardian-relation-label { font-size: 13px !important; }
                    }
                </style>

                <div class="adm-form-container">

                    {{-- Watermark --}}
                    <div class="adm-watermark">
                        @if($school && $school->logo)
                            <img src="{{ asset('storage/' . $school->logo) }}" alt="watermark" />
                        @else
                            <img src="{{ asset('images/logo.png') }}" alt="watermark" />
                        @endif
                    </div>

                    {{-- Header --}}
                    <div class="adm-header">
                        <div class="adm-header-logo">
                            @if($school && $school->logo)
                                <img src="{{ asset('storage/' . $school->logo) }}" alt="Logo" />
                            @else
                                <img src="{{ asset('images/logo.png') }}" alt="Logo" />
                            @endif
                        </div>
                        <h1 id="print_school_name">{{ $school->school_name ?? 'দারুল নাজাত আদর্শ বালিকা মাদ্রাসা' }}</h1>
                        <div class="adm-contact-info">
                            <span><span id="print_school_phone">{{ $school->mobile ?? '01862-164400' }}</span></span>
                            |
                            <span><span id="print_school_email">{{ $school->email ?? 'info@darulnajat.edu.bd' }}</span></span>
                        </div>
                        <div class="adm-address-info">
                            <span id="print_school_address">{{ ($school && $school->village) ? ($school->village . ', ' . $school->upazila . ', ' . $school->district . ', ' . $school->division) : 'জমির প্লাজা (২য় তলা), দক্ষিণ ছায়াবিথি রোড, জোড়পুকুর পার, গাজীপুর সিটি কর্পোরেশন, গাজীপুর।' }}</span>
                        </div>
                    </div>

                    {{-- Form Body --}}
                    <div class="adm-form-body">

                        {{-- Title Badge & Date --}}
                        <div class="adm-title-area">
                            <div class="adm-form-title">Admission Form</div>
                            <div class="adm-date-section">
                                <span style="font-weight: bold;">Date:</span>
                                <span class="adm-date-box" id="print_date_box">--</span>
                            </div>
                        </div>

                        {{-- Photo & ID Section --}}
                        <div class="adm-top-meta">
                            <div class="adm-photo-box">
                                <img id="print_student_photo" src="" class="hidden" />
                                <span id="print_photo_placeholder">Photo</span>
                            </div>
                            <div class="adm-meta-inputs">
                                <div class="adm-meta-half-row">
                                    <div class="adm-meta-half">
                                        <div class="adm-meta-label">ID Number</div>
                                        <div class="adm-meta-value" id="print_student_id_number"></div>
                                    </div>
                                    <div class="adm-meta-half">
                                        <div class="adm-meta-label" style="min-width: 50px;">SL</div>
                                        <div class="adm-meta-value" id="print_admission_id"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Section 1: Student Info --}}
                        <div class="adm-section-header">Student Information</div>


                        <div class="adm-form-row">
                            <div class="adm-label-text">Student Name</div>
                            <div class="adm-colon">:</div>
                            <div class="adm-input-line" id="print_student_name"></div>
                        </div>
                        <div class="adm-form-row">
                            <div class="adm-label-text">Father's Name</div>
                            <div class="adm-colon">:</div>
                            <div class="adm-input-line" id="print_father_name"></div>
                        </div>
                        <div class="adm-form-row">
                            <div class="adm-label-text">Mother's Name</div>
                            <div class="adm-colon">:</div>
                            <div class="adm-input-line" id="print_mother_name"></div>
                        </div>
                        <div class="adm-form-row">
                            <div class="adm-label-text">Mobile No</div>
                            <div class="adm-colon">:</div>
                            <div class="adm-input-line" id="print_mobile"></div>
                        </div>

                        {{-- Guardian Row --}}
                        <div class="adm-guardian-row">
                            <div class="adm-label-text">Guardian Name</div>
                            <div class="adm-colon">:</div>
                            <div class="adm-input-line" style="flex: 1;" id="print_g_name"></div>
                            <div class="adm-guardian-relation-label">Relation</div>
                            <div class="adm-colon">:</div>
                            <div class="adm-input-line" style="flex: 0.7;" id="print_g_relation"></div>
                        </div>

                        <div class="adm-form-row">
                            <div class="adm-label-text">Mobile No</div>
                            <div class="adm-colon">:</div>
                            <div class="adm-input-line" id="print_g_mobile"></div>
                        </div>

                        {{-- Dropdown Grid --}}
                        <div class="adm-dropdown-grid">
                            <div class="adm-dropdown-item">
                                <div class="adm-dropdown-label">Class</div>
                                <div class="adm-dropdown-value" id="print_class"></div>
                            </div>
                            <div class="adm-dropdown-item">
                                <div class="adm-dropdown-label">Group</div>
                                <div class="adm-dropdown-value" id="print_group"></div>
                            </div>
                            <div class="adm-dropdown-item">
                                <div class="adm-dropdown-label">Section</div>
                                <div class="adm-dropdown-value" id="print_section"></div>
                            </div>
                            <div class="adm-dropdown-item">
                                <div class="adm-dropdown-label">Session</div>
                                <div class="adm-dropdown-value" id="print_session"></div>
                            </div>
                        </div>

                        <div class="adm-form-row">
                            <div class="adm-label-text">Student Type</div>
                            <div class="adm-colon">:</div>
                            <div class="adm-input-line" id="print_student_type"></div>
                        </div>

                        {{-- Section 2: Present Address --}}
                        <div class="adm-section-header">Present Address</div>

                        <div class="adm-form-row">
                            <div class="adm-label-text">Country</div>
                            <div class="adm-colon">:</div>
                            <div class="adm-input-line" id="print_curr_country"></div>
                        </div>
                        <div class="adm-form-row">
                            <div class="adm-label-text">Division</div>
                            <div class="adm-colon">:</div>
                            <div class="adm-input-line" id="print_curr_division"></div>
                        </div>
                        <div class="adm-form-row">
                            <div class="adm-label-text">District</div>
                            <div class="adm-colon">:</div>
                            <div class="adm-input-line" id="print_curr_district"></div>
                        </div>
                        <div class="adm-form-row">
                            <div class="adm-label-text">Upazila</div>
                            <div class="adm-colon">:</div>
                            <div class="adm-input-line" id="print_curr_upazila"></div>
                        </div>
                        <div class="adm-form-row">
                            <div class="adm-label-text">Village</div>
                            <div class="adm-colon">:</div>
                            <div class="adm-input-line" id="print_curr_village"></div>
                        </div>

                        {{-- Section 3: Permanent Address --}}
                        <div class="adm-section-header">Permanent Address</div>

                        <div class="adm-form-row">
                            <div class="adm-label-text">Country</div>
                            <div class="adm-colon">:</div>
                            <div class="adm-input-line" id="print_perm_country"></div>
                        </div>
                        <div class="adm-form-row">
                            <div class="adm-label-text">Division</div>
                            <div class="adm-colon">:</div>
                            <div class="adm-input-line" id="print_perm_division"></div>
                        </div>
                        <div class="adm-form-row">
                            <div class="adm-label-text">District</div>
                            <div class="adm-colon">:</div>
                            <div class="adm-input-line" id="print_perm_district"></div>
                        </div>
                        <div class="adm-form-row">
                            <div class="adm-label-text">Upazila</div>
                            <div class="adm-colon">:</div>
                            <div class="adm-input-line" id="print_perm_upazila"></div>
                        </div>
                        <div class="adm-form-row">
                            <div class="adm-label-text">Village</div>
                            <div class="adm-colon">:</div>
                            <div class="adm-input-line" id="print_perm_village"></div>
                        </div>

                        {{-- Footer Signature --}}
                        <div class="adm-footer-section">
                            <div class="adm-signature-area">Principal's Signature</div>
                        </div>

                    </div>
                </div>

            </div>
