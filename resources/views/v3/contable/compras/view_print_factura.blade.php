<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>AdminLTE 3 | Invoice Print</title>

    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">

    <link rel="stylesheet" href="{{url('v3/')}}/plugins/fontawesome-free/css/all.min.css">

    <link rel="stylesheet" href="{{url('v3/')}}/dist/css/adminlte.min.css?v=3.2.0">
</head>

<body>
    <div class="wrapper">

        <section class="invoice">
            <?= $html; ?>
        </section>

    </div>
    <script>
        window.addEventListener("load", window.print());
        window.onafterprint = function() {
            window.close();
        }
    </script>
</body>

</html>