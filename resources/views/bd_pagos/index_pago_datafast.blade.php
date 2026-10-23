<html>
<style>
    .button {
        width: 100%
    }

    @media (min-width: 480px) {

        /* esta regla se aplica solo a dispositivos con un ancho de pantalla mínimo de 480px*/
        .button {
            width: 50%;
        }
    }
</style>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pago Online</title>
</head>

<body>
    <?php
    $checkid = json_decode($checkid);
    $checkoutId = $checkid->id;
    $empresa = Sis_medico\Empresa::where('prioridad', 1)->where('estado', 1)->first();
    ?>
    <form action="" class="paymentWidgets" data-brands="VISA MASTER DINERS DISCOVER"></form>
    <input type="hidden" id="checkid" value="{{$checkid->id}}">
    <input type="hidden" id="contador" value="0">
</body>
<script src="{{$empresa->url_datafast}}/v1/paymentWidgets.js?checkoutId=<?= $checkoutId; ?>"></script>

<script type="text/javascript">
    setInterval(() => {
        if (document?.querySelector(".wpwl-control-cardHolder").value.trim() == "") {
            document.querySelector('form.wpwl-form-card .wpwl-button').disabled = true
        } else {
            document.querySelector('form.wpwl-form-card .wpwl-button').disabled = false
        }
    }, 500)
    window.onload = function() {
        setTimeout(() => {
            const form = document.querySelector('form');
            const button = document.querySelector('form.wpwl-form-card .wpwl-button');
            if (!document.querySelector(".wpwl-control-cardHolder") || document.querySelector(".wpwl-control-cardHolder")?.value.trim() == '') {
                document.querySelector('form.wpwl-form-card .wpwl-button').disabled = true;
            }

            // if (button) {
            //     button.insertAdjacentHTML('beforebegin', `
            //         <br/><br/>
            //         <img src="https://www.datafast.com.ec/images/verified.png" style="display:block;margin:0 auto; width:100%;">
            //     `);
            // }
            document.querySelector('.wpwl-label-brand').innerHTML = 'Tipo de tarjeta'
            document.querySelector('.wpwl-label-cardNumber').innerHTML = 'Número de la tarjeta'
            document.querySelector('.wpwl-label-expiry').innerHTML = 'Expira'
            document.querySelector('.wpwl-label-cardHolder').innerHTML = 'Nombre(Igual que en la tarjeta)'
            document.querySelector('.wpwl-label-cvv').innerHTML = 'CVV'
            document.querySelector(".wpwl-control-cardHolder").addEventListener('keyup', (e) => {
                if (document.querySelector(".wpwl-control-cardHolder").value.trim() == "") {
                    document.querySelector('form.wpwl-form-card .wpwl-button').disabled = true
                    var cardHolderElement = document.querySelector(".wpwl-control-cardHolder");
                    var errorHTML = '<div class="wpwl-hint-cardHolderError wpwl-has-error">Campo Requerido</div>';
                    cardHolderElement.insertAdjacentHTML('afterend', errorHTML);
                } else {
                    document.querySelector('form.wpwl-form-card .wpwl-button').disabled = false
                    var errorElement = document.querySelector(".wpwl-hint-cardHolderError");
                    if (errorElement) {
                        errorElement.parentNode.removeChild(errorElement);
                    }
                }
            })
        }, 500);
    }
</script>

</html>