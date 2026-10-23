<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office" lang="es">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="format-detection" content="telephone=no" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <meta http-equiv="X-UA-Compatible" content="IE=9, IE=8, IE=7, IE=EDGE" />
    <title>LABS</title>
    <meta name="color-scheme" content="only">
    <meta name="supported-color-schemes" content="only">

    <style type="text/css">
        body {
            margin: 0;
            padding: 0;
        }

        table {
            border-collapse: collapse;
        }

        .preheader {
            display: none !important;
            visibility: hidden;
            opacity: 0;
            color: transparent;
            height: 0;
            width: 0;
        }

        img {
            display: block;
        }

        a {
            color: #1a73e8;
        }

        .wrapper {
            width: 100% !important;
            max-width: 480px !important;
        }

        @media (min-width: 480px) {
            .hh {
                height: 16px !important;
            }
        }

        @media (max-width: 480px) {
            .no-height {
                height: 0 !important;
                display: none !important;
            }

            body,
            .bg {
                background: #fffffe !important;
            }

            .no-shadow {
                box-shadow: none !important;
            }

            .height {
                height: 4px !important;
            }

            .no-display {
                display: none !important;
                width: 0 !important;
                height: 0 !important;
                margin: 0 !important;
                padding: 0 !important;
            }
        }
    </style>
</head>

<body style="margin: 0; padding: 0; background:#EFF0F4;">
    <span class="preheader" style="display: none !important; visibility: hidden; opacity: 0; color: transparent; height: 0; width: 0;">
    </span>
    <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background:#EFF0F4;">
        <tr>
            <td align="center">
                <table class="wrapper" align="center" border="0" cellpadding="0" cellspacing="0" width="480" style="border-collapse: collapse;">
                    <tr height="20" class="no-height">
                        <td height="20" class="no-height"></td>
                    </tr>
                    <tr height="4" style="background:#114978">
                        <td height="4" style="background:#114978"></td>
                    </tr>
                    <tr>
                        <td style="background:#fffffe;box-shadow: 0px 3px 4px rgba(0, 0, 0, 0.05)" class="no-shadow">
                            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="border-collapse: collapse;">
                                <tr>
                                    <td width="20"></td>
                                    <td>
                                        <table border="0" cellpadding="0" cellspacing="0" width="100%" style="border-collapse: collapse;">
                                            <tr height="32">
                                                <td height="32"></td>
                                            </tr>
                                            <tr>
                                                <td align="left">
                                                    <img src="{{asset('/mail-header.png')}}" alt="LOGO" style="width: 700px;display:block;">
                                                </td>
                                            </tr>
                                            <tr height="40">
                                                <td height="40"></td>
                                            </tr>
                                            <tr>
                                                <td align="left" style="font-size: 16px; font-weight:500; line-height: 20px;color: #222222;font-family: Roboto, Arial, sans-serif;">Estimado Usuario,<br><br>
                                                    Se solicitó un código de acceso. Dicho código caducará en 10 minutos:
                                                </td>
                                            </tr>
                                            <tr height="24">
                                                <td height="24"></td>
                                            </tr>
                                        </table>
                                    </td>
                                    <td width="20"></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td style="background:#fffffe;box-shadow: 0px 3px 4px rgba(0, 0, 0, 0.05)" class="no-shadow">
                            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="border-collapse: collapse;">
                                <tr>
                                    <td width="20"></td>
                                    <td>
                                        <p style="font-size: 25px; text-align: center;"><strong>{{$codigo}}</strong></p>
                                    </td>
                                    <td width="20"></td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <tr>
                        <td class="wrapper" height="" style="background:#e6e6e6;box-shadow: 0px 3px 4px rgba(0, 0, 0, 0.05);">
                            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="border-collapse: collapse;">
                                <tr>
                                    <td width="20"></td>
                                    <td>
                                        <table border="0" cellpadding="0" cellspacing="0" width="100%" style="border-collapse: collapse;">
                                            <tr height="24">
                                                <td height="24"></td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <table role="presentation" cellpadding="0" cellspacing="0" border="0" align="left">
                                                        <tr>
                                                            <td align="center" width="24" style="padding: 0px 5px;" ng-show="slink.link != ''">
                                                                
                                                            </td>
                                                            <td align="center" width="24" style="padding: 0px 5px;" ng-show="slink.link != ''">
                                                                
                                                            </td>
                                                            <td align="center" width="24" style="padding: 0px 5px;" ng-show="slink.link != ''">
                                                                
                                                            </td>
                                                            <td align="center" width="24" style="padding: 0px 5px;" ng-show="slink.link != ''">
                                                                
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                            <tr height="16">
                                                <td height="16"></td>
                                            </tr>
                                        </table>
                                    </td>
                                    <td width="20"></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr height="40" class="no-height">
                        <td height="40" class="no-height"></td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>