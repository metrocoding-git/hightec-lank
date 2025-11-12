


@extends('layouts.app')
@section('title')
    About
@endsection

@section('mainContent')

@endsection

@push('script')
<script>

    $(document).ready(function() {
        const reprintUrl = "{{ route('fees.invoice-print', $invoiceId) }}";

        window.open(reprintUrl, '_blank');

        setTimeout(function() {
            window.location.href = '{{ $redirectUrl }}';
        }, 2500);

    });
</script>
@endpush