<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
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

<div class="bg-white w-100" style="max-width: 28rem;">
    <div class="bg-white w-100">
        <h1 class="text-center text-secondary fs-4 fw-light text-uppercase bg-light pb-3">
            Expelliarmus Shop
        </h1>
    </div>

    <div class="p-4 pb-2">
        <div class="text-secondary text-center small mb-3">
            <p>Hello! For your activity, we give you a coupon!</p>
        </div>

        <hr class="my-4">

        <div class="mb-4 d-flex justify-content-center align-items-center flex-column">
            <div class="mb-3 text-center">
                <span>Your Coupon Code:</span>
            </div>
            <div class="mb-4 fs-5 fw-bold">
                {{ $couponCode }}
            </div>
            <div class="mb-3" style="color: #5b21b6;">
                <p class="m-0">Get a discount to <strong>{{ $discount }}</strong>% to
                    <strong>{{ $expiresAt }}</strong>!</p>
            </div>
        </div>

        <hr class="my-4">

        <div class="text-center small text-secondary">
            <p>Thanks for the purchase, your <a href="#" class="text-decoration-underline text-info">Expelliarmus
                    Shop!</a></p>
        </div>
    </div>

    <div class="bg-light text-secondary small py-3 d-flex justify-content-center">
        <p class="m-0">© 2025 Expelliarmus Shop. All rights reserved.</p>
    </div>
</div>

</body>
</html>
