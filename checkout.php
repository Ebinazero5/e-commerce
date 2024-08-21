<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Checkout</title>
    <script src="https://js.stripe.com/v3/"></script>
</head>
<body>
    <form action="process_order.php" method="POST" id="payment-form">
        <div id="card-element"></div> <!-- Stripe.js will inject the card input here -->
        <button type="submit">Submit Payment</button>
    </form>

    <script>
        var stripe = Stripe('ppk_test_51PnF6ERudXHrdoCra8epduikojmjrLI7GXSbvkI6yb8cPoTSSfbzYI1kjtbKirKTBzvrZif8xXKTHt7YfUgrjSp800UKMKcO3v'); // Replace with your publishable key
        var elements = stripe.elements();

        var card = elements.create('card');
        card.mount('#card-element');

        var form = document.getElementById('payment-form');
        form.addEventListener('submit', function(event) {
            event.preventDefault();

            stripe.createToken(card).then(function(result) {
                if (result.error) {
                    // Display error.message in your UI.
                } else {
                    var hiddenInput = document.createElement('input');
                    hiddenInput.setAttribute('type', 'hidden');
                    hiddenInput.setAttribute('name', 'stripeToken');
                    hiddenInput.setAttribute('value', result.token.id);
                    form.appendChild(hiddenInput);

                    // Submit the form
                    form.submit();
                }
            });
        });
    </script>
</body>
</html>
