<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Expelliarmus Shop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet"
          integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
            crossorigin="anonymous">
    </script>
</head>
<body class="bg-light d-flex align-items-center justify-content-center min-vh-100">

<div class="bg-white w-100" style="max-width: 540px;">
    <div class="bg-light py-3">
        <h1 class="text-center text-muted fs-4 fw-light text-uppercase mb-0">Expelliarmus Shop</h1>
    </div>
    <div class="p-4">

        <div class="text-secondary mb-3 small">
            <p class="fs-5 text-center">Thanks for ordering</p>
            <p>Hello, {{ $user->first_name }}.</p>
            <p>
                We appreciate your order, we’re currently processing it. So hang tight and we’ll send you confirmation
                very soon!
            </p>
        </div>

        <hr class="my-3">

        @foreach($orderLines as $orderLine)
            <div class="d-flex justify-content-between align-items-center bg-white p-3 border rounded mb-2">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-secondary rounded" style="width: 40px; height: 40px; overflow: hidden;">
                        <img src="{{ $orderLine->product->preview_image }}" alt="Product Image"
                             class="img-fluid h-100 w-100 object-fit-cover">
                    </div>
                    <span class="text-dark">
                        {{ \Illuminate\Support\Str::limit($orderLine->product->title, 30) }}
                    </span>
                </div>
                <div class="text-end text-muted small d-flex align-items-center gap-2">
                    @if($orderLine->discount !== null)
                        <span class="text-danger fw-medium">-{{ $orderLine->discount->percentage }}</span>
                    @endif
                    <span>{{ $orderLine->quantity }}x</span>
                    <span class="fw-semibold">${{ $orderLine->totalPrice }}</span>
                </div>
            </div>
        @endforeach

        <hr class="my-3">

        <div class="d-flex flex-column gap-2 text-dark small py-2">
            @if($coupon !== null)
                <div class="text-end">Coupon: - {{ $coupon->discount }}%.</div>
            @endif
            <div class="text-end fw-bold h6">Total: ${{ $totalPrice }}</div>
            <div>Billing address: {{ $user->address }}</div>
            <div>Recipient: {{ $user->fullName() }}</div>
        </div>

        <div class="text-center small pt-2 text-muted">
            <p>Want to track the history of your orders?
                <a href="{{ config('app.frontend_url')."/sign-up?email={$user->email}&name={$user->first_name}" }}"
                   class="text-decoration-underline text-primary">Create account now!</a>
            </p>
        </div>

    </div>

    <div class="bg-light text-center text-muted small py-3">
        <h2 class="h6 mb-0">© {{ now()->year }} Expelliarmus Shop. All rights reserved.</h2>
    </div>
</div>
</body>
</html>
