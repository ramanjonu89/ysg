<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        .container {
            display: flex;
            justify-content: center;
            flex-direction: column;
            align-items: center;
            background-color: #f6f6f6;
        }

        .row {
            display: flex;
            
        }

        .content {
            width: 600px;
        }

        .justify-content-center {
            justify-content: center;
        }

        .p-custom {
            position: absolute;
            top: 34%;
            left: 49%;
            font-size: 1.3rem;
        }

        .button {
            background-color: transparent;
            position: absolute;
            /* width: 150px; */
            width: 25%;
            /* height: 28px; */
            height: 55%;
            left: 17%;
            top: 50%;
            transform: translateY(-55%);
        }
        span {
            padding: 3px 6px;
            position: absolute; 
            left: 50%;
            top: 50%; 
            transform: translateY(20%);
            font-weight: bold;
        }

        @media screen and (max-width: 600px) {
        .content {
            width: 100%;
            padding: 0 10px;
        }

        /* .container {
            float: left;
        } */

        .button {
            width: 26%;
            left: 17%;
            height: 70%;
            top: 50%;
            transform: translateY(-50%);
        }

        span {
            left: 60%;
            top: 55%;
            transform: translate(-50%, -50%);
            font-size: 0.5rem;
        }
        
        .p-custom {
            top: auto;
            left: auto;
            text-align: center;
            margin-top: 20px;
        }

        .justify-content-center {
            justify-content: flex-start;
        }
    }
    </style>
</head>
<body style="font-family:arial, 'helvetica neue', helvetica, sans-serif;-webkit-text-size-adjust:100%;">
    <div class="container">
        <div class="content">
            <table width="100%" cellspacing="0" cellpadding="0"style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;width:100%">
                <tr>
                    <td>
                        <table  cellpadding="0" cellspacing="0" width="100%"style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;width:100%;">
                            <tr>
                                <td style="position: relative;">
                                    <img src="{{ $message->embed(my_asset('assets/img/referral/referral_code_1.jpg')) }}" style="width: 100%;height: 100%;">
                                    <span class="">3633_kqOqFF8fr</span>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <!-- register -->
                <tr style="position: relative;top: -4px;">
                    <td>
                        <table cellpadding="0" cellspacing="0" width="100%" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;width:100%">
                            <tr>
                                <td style="position: relative;">
                                    <img src="{{ $message->embed(my_asset('assets/img/referral/referral_code_2.jpg')) }}" style="width: 100%;">
                                    <a href="{{ $referralLink }}" class="button" target="_blank">
                                    </a>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <!-- end register -->
                <tr style="position: relative;top: -8px;">
                    <td>
                        <table cellpadding="0" cellspacing="0" width="100%" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;width:100%">
                            <tr>
                                <td style="position: relative;">
                                    <img src="{{ $message->embed(my_asset('assets/img/referral/referral_code_3.jpg')) }}" style="width: 100%;">
                                  
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <!-- shop link -->
                <tr style="position: relative;top: -12px;">
                    <td>
                        <table cellpadding="0" cellspacing="0" width="100%" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;width:100%">
                            <tr>
                                <td style="position: relative;">
                                    <img src="{{ $message->embed(my_asset('assets/img/referral/referral_code_4.jpg')) }}" style="width: 100%;">
                                    <a href="https://ysg.com.sg/shops/visit/Premium-Redemption-Centre-1429" class="button" target="_blank">
                                        
                                    </a>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <!-- end shop link -->
                <tr style="position: relative;top: -16px;">
                    <td>
                        <table style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;width:100%">
                            <tr>
                                <td style="position: relative;">
                                    <img src="{{ $message->embed(my_asset('assets/img/referral/referral_code_5.jpg')) }}" style="width: 100%;">
                                    
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html>