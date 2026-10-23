<html>

<head>
    <title>Checkout</title>
    <!--<script src="https://cdn.kushkipagos.com/kushki.min.js"></script>-->
    <script src="https://cdn.kushkipagos.com/kushki-checkout.js"></script>
</head>

<body>
    <form id="my-form" action="https://gastroquito.siaam.ec/public/ventas/confirmpago" method="post">
        <input type="hidden" name="cart_id" value="123">
    </form>
    <script type="text/javascript">
        var kushki = new KushkiCheckout({
            kformId: "m5t2uSA5J",
            form: "my-form",
            publicMerchantId: "aac7649350404b3b85907d90426c6155", // Reemplaza esto por tucredencial pública
            inTestEnvironment: true,
            amount: {
                subtotalIva: 0,
                iva: 0,
                subtotalIva0: 1,
            }
        });
        console.log(kushki);
    </script>
</body>

</html>