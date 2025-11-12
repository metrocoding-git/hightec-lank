<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Invoice Ready</title>
</head>
<body>
  <h3>Invoice created successfully!</h3>
  <p>Click below to view your invoice:</p>
  <button id="openInvoice" style="display:none;">View Invoice</button>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const button = document.getElementById('openInvoice');
      const invoiceUrl = "{{ route('fees.invoice-print', ['invoiceId' => $invoiceId]) }}";
      const redirectUrl = "{{ $redirectUrl ?? url()->previous() }}";

      // Auto trigger button click after 300ms
      setTimeout(() => {
        const popup = window.open(invoiceUrl, '_blank');

        if (!popup || popup.closed || typeof popup.closed === 'undefined') {
          // If popup blocked, show visible button for user
          button.style.display = 'inline-block';
          alert("Please allow pop-ups for this site and click the button to view your invoice.");
        } else {
          // Redirect after a few seconds
          setTimeout(() => {
            window.location.href = redirectUrl;
          }, 2500);
        }
      }, 300);
    });
  </script>
</body>
</html>
