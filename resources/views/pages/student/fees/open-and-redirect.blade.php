{{-- resources/views/utils/open-and-redirect.blade.php --}}
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <title>Redirecting...</title>
</head>

<body>

    <script>

        $(document).ready(function() {
            const reprintUrl = "{{ route('fees.invoice-print', $invoiceId) }}";

            window.open(reprintUrl, '_blank');

            // setTimeout(function() {
            //     window.location.href = '{{ $redirectUrl }}';
            // }, 250);

        });
    </script>
</body>

</html>
