@extends('layouts.app')

@section('title')
    Student Verification
@endsection

@section('mainContent')

    <section class="sms-breadcrumb mb-20">
        <div class="container-fluid">
            <div class="row justify-content-between">
                <h1>Student Verification</h1>
                <div class="bc-pages">
                    <a href="{{ route('dashboard') }}">Dashboard</a>
                    <a href="#">Student Info</a>
                    <a href="#">Verification</a>
                </div>
            </div>
        </div>
    </section>

    @php
        // Button-level permission (Section 25). The window itself (GET add-student.verify)
        // is already gated by route middleware, but the actual submit action has its own
        // separate key, so a viewer without it can see the page but not act on it.
        $canSubmitVerification = Auth::user()->hasPermission('add-student.verify.submit');
    @endphp

    <div class="container mt-4">

        <h2 class="mb-4">Student Verification</h2>

        <div class="card shadow-lg p-4">

            {{-- Student Info --}}
            <div class="mb-4">
                <h4 class="mb-3">Student Details</h4>
                <p><strong>Name:</strong> {{ $student->student_name }}</p>
                <p><strong>National ID:</strong> {{ $student->national_id_number }}</p>
            </div>

            @if ($student->is_verified)
                <div class="alert alert-success"><strong>Already Verified</strong></div>
            @elseif (!$canSubmitVerification)
                <div class="alert alert-secondary text-muted">
                    You do not have permission to submit student verification.
                </div>
            @else
                <form method="POST" enctype="multipart/form-data"
                    action="{{ route('add-student.verify.submit', $student->id) }}">
                    @csrf

                    <h4>Select Verification Items</h4>

                    @php
                        $items = [
                            'Full Name Verified' => 'Full Name',
                            'NIC Verified' => 'NIC Number',
                            'Address Verified' => 'Address',
                            'Mobile Number Verified' => 'Mobile Number',
                            'Mobile Number 2 Verified' => 'Mobile Number 2',
                        ];

                        $defaultValues = [
                            'Full Name' => $student->student_name,
                            'NIC Number' => $student->national_id_number,
                            'Address' => $student->permanent_address,
                            'Mobile Number' => $student->phone_number,
                            'Mobile Number 2' => $student->phone_number_2,
                        ];
                    @endphp

                    <div class="row mt-3">
                        @foreach ($items as $item => $label)
                            @if ($label === 'Mobile Number 2' && empty($student->phone_number_2))
                                @continue
                            @endif
                            @php $value = $defaultValues[$label] ?? ''; @endphp

                            <div class="col-md-6 mb-3">
                                <label class="verification-box">
                                    <input type="checkbox" class="verify-check" data-target="input_{{ md5($item) }}"
                                        name="verification_items[]" value="{{ $item }}">
                                    {{ $item }}
                                </label>

                                <input type="text" class="form-control mt-2 verify-input" id="input_{{ md5($item) }}"
                                    name="verification_values[{{ $item }}]" placeholder="Enter {{ $label }}"
                                    value="{{ $value }}" style="display:none;">
                            </div>
                        @endforeach
                    </div>

                    <hr>

                    {{-- SIGNATURE PREVIEW + RE-SIGN --}}
                    <div class="mb-3 text-center">

                        <div id="signature-preview" class="border rounded p-3 text-center"
                            style="height:200px; cursor:pointer; background:#f8f9fa;">
                            <span style="color:#777;" id="signature-text">Tap here to draw signature</span>
                        </div>

                        <!-- Re-sign button (appears after signing) -->
                        <button type="button" id="resign-btn" class="btn btn-danger mt-2" style="display:none;">
                            Re-Sign
                        </button>

                    </div>

                    <input type="hidden" name="signature_data" id="signature_data">


                    {{-- SIGNATURE MODAL --}}
                    <div class="modal fade" id="signatureModal">
                        <div class="modal-dialog modal-xl">
                            <div class="modal-content">

                                <div class="modal-header">
                                    <h4 class="modal-title">Verify & Sign</h4>
                                    <button type="button" class="close" data-dismiss="modal">×</button>
                                </div>

                                <div class="modal-body">

                                    <h5>Selected Verification Items</h5>
                                    <ul id="modal-selected-items" style="font-size:18px; font-weight:600;"></ul>

                                    <h5 class="mt-4 mb-2">Sign Here</h5>
                                    <canvas id="signature-pad" width="900" height="350"
                                        style="border:1px solid #ccc; width:100%; height:350px;"></canvas>

                                </div>

                                <div class="modal-footer">
                                    <button type="button" id="clear-signature" class="btn btn-warning">Clear</button>
                                    <button type="button" id="save-signature" class="btn btn-primary">Save</button>
                                </div>

                            </div>
                        </div>
                    </div>


                    {{-- PREVIEW MODAL --}}
                    <div class="modal fade" id="previewModal">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">

                                <div class="modal-header">
                                    <h4 class="modal-title">Verification Preview</h4>
                                    <button type="button" class="close" data-dismiss="modal">×</button>
                                </div>

                                <div class="modal-body">

                                    <h5>Selected Items & Values</h5>
                                    <ul id="modal-preview-list" style="font-size:18px; font-weight:600;"></ul>

                                    <h5 class="mt-4">Signature</h5>
                                    <div id="modal-signature-box"></div>

                                </div>

                                <div class="modal-footer">
                                    <button class="btn btn-success" type="submit">Confirm & Submit</button>
                                </div>

                            </div>
                        </div>
                    </div>

                    <button type="button" id="openPreview" class="btn btn-info w-100 mb-2">
                        Preview Before Submit
                    </button>

                </form>
            @endif

        </div>
    </div>

@endsection

@section('style')
    <style>
        /* ---- Modern refresh: breadcrumb card (Section 25) ---- */
        .sms-breadcrumb {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 14px rgba(20, 20, 43, 0.06);
            border: 1px solid var(--border_color);
            padding: 18px 24px;
            position: relative;
            overflow: hidden;
        }

        .sms-breadcrumb::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            bottom: 0;
            width: 4px;
            background: linear-gradient(180deg, var(--gradient_1), var(--gradient_2));
        }

        .sms-breadcrumb .row {
            align-items: center;
        }

        .sms-breadcrumb h1 {
            font-size: 20px;
            font-weight: 700;
            margin: 0;
        }

        .sms-breadcrumb .bc-pages {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
        }

        .sms-breadcrumb .bc-pages a {
            font-size: 13px;
            font-weight: 500;
            color: #8a90a2;
            text-decoration: none;
            transition: color .15s ease;
        }

        .sms-breadcrumb .bc-pages a:hover {
            color: var(--gradient_1);
        }

        .sms-breadcrumb .bc-pages a:not(:last-child)::after {
            content: '\203A';
            margin: 0 8px;
            color: #c7cbd6;
            font-weight: 400;
        }

        .sms-breadcrumb .bc-pages a:last-child {
            color: var(--gradient_1);
            font-weight: 700;
            pointer-events: none;
        }

        .card {
            border-radius: 12px;
            border: 1px solid var(--border_color);
        }

        .verification-box {
            display: block;
            background: #f8f9fa;
            padding: 15px;
            border-radius: 10px;
            border: 2px solid #eaeaea;
            cursor: pointer;
            font-size: 16px;
        }

        .verification-box:hover {
            background: #eef6ff;
            border-color: #007bff;
        }
    </style>
@endsection

@section('script')
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>

    <script>
        // -------- Update Modal Selected Items -------- //
        function updateModalPreview() {
            let list = document.getElementById("modal-selected-items");
            list.innerHTML = "";

            document.querySelectorAll(".verify-check").forEach(cb => {
                if (cb.checked) {
                    const label = cb.value;
                    const inp = document.getElementById(cb.dataset.target);
                    const val = inp.value || "-";

                    let li = document.createElement("li");
                    li.textContent = `${label} — ${val}`;
                    list.appendChild(li);
                }
            });
        }


        // -------- Signature Pad -------- //
        let canvas = document.getElementById("signature-pad");
        let signaturePad = new SignaturePad(canvas);



        function resizeCanvas() {
            let canvas = document.getElementById("signature-pad");
            const ratio = Math.max(window.devicePixelRatio || 1, 1);

            console.log("asasdasd", canvas.offsetWidth, ratio, canvas.width, canvas.height)

            canvas.width = canvas.offsetWidth * ratio;
            canvas.height = canvas.offsetHeight * ratio;
            canvas.getContext("2d").scale(ratio, ratio);
            console.log("12344", canvas.offsetWidth, ratio, canvas.width, canvas.height)

            signaturePad.clear();
        }



        // Open signature modal
        $("#signature-preview").click(function() {

            if ($(".verify-check:checked").length === 0) {
                alert("Please select at least one verification item before signing.");
                return;
            }

            signaturePad.clear();
            updateModalPreview();
            $("#signatureModal").modal("show");
        });


        $('#signatureModal').on('shown.bs.modal', function() {

            canvas = document.getElementById("signature-pad");

            if (!signaturePad) {
                signaturePad = new SignaturePad(canvas);
                canvas.style.touchAction = "none"; // mobile support
            }

            resizeCanvas();

            console.log(
                "VISIBLE:",
                canvas.offsetWidth,
                canvas.offsetHeight
            );
        });



        // Checkbox toggle inputs
        $(".verify-check").change(function() {

            let target = $("#" + $(this).data("target"));
            let wasSigned = $("#signature_data").val() !== "";

            if ($(this).is(":checked")) {
                target.show();
            } else {
                target.hide();
            }

            if (wasSigned) {
                resetSignatureAndUnlock();
            }

            updateModalPreview();
        });


        // Input typing auto-clear
        $(".verify-input").on("input", function() {
            if ($("#signature_data").val() !== "") {
                resetSignatureAndUnlock();
            }
            updateModalPreview();
        });


        // Clear signature
        $("#clear-signature").click(function() {
            signaturePad.clear();
        });


        // Save signature
        $("#save-signature").click(function() {

            if (!signaturePad.isEmpty()) {

                let dataUrl = signaturePad.toDataURL();
                $("#signature_data").val(dataUrl);

                $("#signature-preview").html(`<img src="${dataUrl}" style="height:180px;">`);
                $("#signature-text").hide();

                lockInputs();

                // Show Re-Sign button
                $("#resign-btn").show();
            }

            $("#signatureModal").modal("hide");
        });


        // -------- Re-Sign Button -------- //
        $("#resign-btn").click(function() {

            resetSignatureAndUnlock();

            alert("Signature cleared. Please sign again.");

            // Re-open signature modal
            updateModalPreview();
            signaturePad.clear();
            // $("#signatureModal").modal("show");
        });


        // -------- Preview Before Submit -------- //
        $("#openPreview").click(function() {

            if ($("#signature_data").val() === "") {
                alert("Please sign before preview.");
                return;
            }

            let list = $("#modal-preview-list");
            list.html("");

            $(".verify-check:checked").each(function() {
                let label = $(this).val();
                let input = $("#" + $(this).data("target")).val();
                list.append(`<li>${label} — <strong>${input}</strong></li>`);
            });

            let sign = $("#signature_data").val();
            $("#modal-signature-box").html(`<img src="${sign}" style="height:150px;">`);

            $("#previewModal").modal("show");
        });


        // -------- Helper Functions -------- //

        function lockInputs() {
            $(".verify-check").prop("disabled", false);
            $(".verify-input").prop("readonly", true);
        }

        function resetSignatureAndUnlock() {

            $("#signature_data").val("");

            $("#signature-preview").html(
                `<span style="color:#777;" id="signature-text">Tap here to draw signature</span>`
            );

            $("#resign-btn").hide();

            $(".verify-check").prop("disabled", false);
            $(".verify-input").prop("readonly", false);
        }
    </script>
@endsection