<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Invoice Ready</title>
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(135deg, #f0f4ff, #dbeafe);
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      margin: 0;
    }

    .card {
      background: #fff;
      border-radius: 20px;
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
      padding: 40px;
      text-align: center;
      max-width: 400px;
      width: 90%;
      animation: fadeIn 0.6s ease;
    }

    .card h3 {
      font-size: 1.6rem;
      color: #2563eb;
      margin-bottom: 10px;
    }

    .card p {
      font-size: 1rem;
      color: #555;
      margin-bottom: 25px;
    }

    #openInvoice {
      background-color: #2563eb;
      color: #fff;
      border: none;
      padding: 12px 28px;
      border-radius: 12px;
      font-size: 1rem;
      font-weight: 500;
      cursor: pointer;
      transition: all 0.3s ease;
      box-shadow: 0 4px 10px rgba(37, 99, 235, 0.3);
    }

    #openInvoice:hover {
      background-color: #1e40af;
      transform: translateY(-2px);
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(15px); }
      to { opacity: 1; transform: translateY(0); }
    }
  </style>
</head>
<body>
  <div class="card">
    <h3>Invoice Created Successfully!</h3>
    <p>Your invoice is ready. Click below to view it.</p>
    <button id="openInvoice" style="display:none;">View Invoice</button>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const button = document.getElementById('openInvoice');
      const invoiceUrl = "{{ route('fees.invoice-print', ['invoiceId' => $invoiceId]) }}";
      const redirectUrl = "{{ $redirectUrl ?? url()->previous() }}";

      // Try auto open invoice
      setTimeout(() => {
        const popup = window.open(invoiceUrl, '_blank');

        if (!popup || popup.closed || typeof popup.closed === 'undefined') {
          button.style.display = 'inline-block';
          alert("Please allow pop-ups for this site, then click 'View Invoice' to continue.");
        } else {
          setTimeout(() => {
            window.location.href = redirectUrl;
          }, 2500);
        }
      }, 400);

      // Manual click fallback
      button.addEventListener('click', function() {
        window.open(invoiceUrl, '_blank');
        setTimeout(() => {
          window.location.href = redirectUrl;
        }, 2500);
      });
    });
  </script>
</body>
</html>
