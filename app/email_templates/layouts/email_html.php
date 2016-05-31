<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

    <title><?=$this->e($title)?></title>

    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>

    <style type="text/css">
        body {
            margin: 0;
            padding: 0;
            min-width: 100%!important;
            font-family: Arial, sans-serif;
            color: #333333;
        }
    </style>
</head>
<body bgcolor="#f5f5f5" style="margin: 0; padding: 0;">

<table bgcolor="#f5f5f5" border="0" cellpadding="0" cellspacing="0" width="100%">
    <tr>
        <td bgcolor="##0a6c74" height="40" align="center">
            <h1 style="color: #ffffff;">trk.life</h1>
        </td>
    </tr>

    <tr>
        <td>
            <table align="center" border="0" cellpadding="0" cellspacing="0" width="600">
                <tr><td style="font-size: 0; line-height: 0;" height="15">&nbsp;</td></tr>

                <tr>
                    <td bgcolor="#ffffff" style="border: 1px solid #dcdcdc; padding-left: 15px; padding-right: 15px; padding-top: 15px; padding-bottom: 15px;">
                        <?=$this->section('content')?>

                        <p>
                            Thanks,<br>
                            trk.life
                        </p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>

</body>
</html>