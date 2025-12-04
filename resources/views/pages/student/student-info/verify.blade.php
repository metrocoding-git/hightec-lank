@extends('layouts.app')

@section('title')
    Student Verification
@endsection

@section('mainContent')

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
                                        style="border:1px solid #ccc; width:100%;"></canvas>

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
