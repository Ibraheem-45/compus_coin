<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify your Campus Coin account</title>
</head>
<body style="margin: 0; padding: 0; background-color: #eef2ff; font-family: Arial, sans-serif; color: #1f2937;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color: #eef2ff; padding: 40px 0;">
        <tr>
            <td align="center">
                <table role="presentation" width="600" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border-radius: 18px; overflow: hidden; border: 1px solid #dfe7ff;">
                    <tr>
                        <td style="background: linear-gradient(135deg, #5863d8 0%, #7c3aed 100%); padding: 28px 32px; text-align: center; color: #ffffff;">
                            <div style="font-size: 28px; font-weight: 800; letter-spacing: 0.5px;">
                                Campus Coin
                            </div>
                            <div style="font-size: 12px; margin-top: 8px; letter-spacing: 1.5px; opacity: 0.9;">
                                TRACK • SAVE • GROW
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 36px 32px 24px;">
                            <div style="font-size: 22px; font-weight: 700; color: #1f2937; margin-bottom: 18px;">
                                Hello {{ $userName }}!
                            </div>

                            <div style="font-size: 15px; line-height: 1.7; color: #374151; margin-bottom: 24px;">
                                Thanks for joining <strong>Campus Coin</strong>. To finish setting up your account and start managing your budget smartly, please verify your email address.
                            </div>

                            <div style="text-align: center; margin: 28px 0;">
                                <a href="{{ $verificationUrl }}" style="display: inline-block; background: #5863d8; color: #ffffff; text-decoration: none; font-weight: 700; border-radius: 10px; padding: 14px 28px; font-size: 15px;">
                                    Verify Email Address
                                </a>
                            </div>

                            <div style="font-size: 13px; line-height: 1.7; color: #6b7280; margin-top: 18px;">
                                If you did not create this account, you can safely ignore this email.
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 0 32px 28px;">
                            <div style="border-top: 1px solid #e5e7eb; padding-top: 18px; font-size: 12px; color: #6b7280; text-align: center;">
                                Best regards,<br>
                                <strong style="color: #5863d8;">Campus Coin</strong>
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
