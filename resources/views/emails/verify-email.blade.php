<!DOCTYPE html>
<html lang="my">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Email အတည်ပြုပါ</title>
</head>
<body style="margin:0; padding:0; background-color:#080B14; font-family:'Segoe UI', Helvetica, Arial, sans-serif;">

<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#080B14; padding:40px 16px;">
  <tr>
    <td align="center">

      <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:440px; background-color:#10131F; border:1px solid #232838; border-radius:24px; overflow:hidden;">

        <!-- Top accent bar -->
        <tr>
          <td style="height:5px; background-color:#7C3AED; background-image:linear-gradient(90deg, #7C3AED 0%, #06B6D4 55%, #F59E0B 100%); font-size:0; line-height:0;">&nbsp;</td>
        </tr>

        <tr>
          <td style="padding:40px 36px 32px;">

            <!-- Brand badge -->
            <table role="presentation" cellpadding="0" cellspacing="0" align="center" style="margin:0 auto 20px;">
              <tr>
                <td style="width:52px; height:52px; border-radius:16px; background-color:#7C3AED; background-image:linear-gradient(135deg, #7C3AED, #06B6D4); text-align:center; vertical-align:middle; font-size:22px; font-weight:800; color:#ffffff;">
                  ZX
                </td>
              </tr>
            </table>

            <!-- Brand name -->
            <p style="margin:0; text-align:center; font-size:13px; font-weight:700; letter-spacing:2px; color:#94A3B8;">
              Z.A.K.E.R.X.A
            </p>

            <!-- Heading -->
            <h1 style="margin:18px 0 8px; text-align:center; font-size:21px; font-weight:800; color:#F1F5F9; letter-spacing:-0.3px;">
              Email <span style="color:#22D3EE;">အတည်ပြုပါ</span>
            </h1>

            <p style="margin:0 0 28px; text-align:center; font-size:13.5px; line-height:1.7; color:#94A3B8;">
              Sign up ပြုလုပ်ပေးတဲ့အတွက် ကျေးဇူးတင်ပါတယ်။<br>
              စတင်အသုံးပြုနိုင်ရန် Email လိပ်စာကို အတည်ပြုပေးပါ။
            </p>

            <!-- Button -->
            <table role="presentation" cellpadding="0" cellspacing="0" align="center" style="margin:0 auto 28px;">
              <tr>
                <td style="border-radius:14px; background-color:#7C3AED; background-image:linear-gradient(135deg, #7C3AED, #06B6D4);">
                  <a href="{{ $url }}" target="_blank"
                     style="display:inline-block; padding:14px 36px; font-size:14px; font-weight:700; color:#ffffff; text-decoration:none; border-radius:14px;">
                    Email အတည်ပြုရန်
                  </a>
                </td>
              </tr>
            </table>

            <!-- Fallback link -->
            <p style="margin:0 0 24px; text-align:center; font-size:12px; line-height:1.6; color:#64748B;">
              Button အလုပ်မလုပ်ပါက အောက်ပါ link ကို copy ကူး၍ browser ထဲ paste လုပ်ပါ:<br>
              <a href="{{ $url }}" style="color:#22D3EE; word-break:break-all;">{{ $url }}</a>
            </p>

            <!-- Divider -->
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin:0 0 20px;">
              <tr>
                <td style="border-top:1px solid #232838; font-size:0; line-height:0;">&nbsp;</td>
              </tr>
            </table>

            <p style="margin:0; text-align:center; font-size:12px; line-height:1.7; color:#475569;">
              ဒီ link သည် 60 မိနစ်အတွင်း သက်တမ်းကုန်ပါမည်။<br>
              ဒီ request ကို သင်ကိုယ်တိုင် မလုပ်ခဲ့ပါက ဒီ email ကို လျစ်လျူရှုနိုင်ပါသည်။
            </p>

          </td>
        </tr>

        <!-- Footer -->
        <tr>
          <td style="padding:20px 36px; background-color:#0B0E18; border-top:1px solid #1A1F2E;">
            <p style="margin:0; text-align:center; font-size:11px; color:#475569;">
              &copy; {{ date('Y') }} Zakerxa &mdash; AI-Powered Content Studio
            </p>
          </td>
        </tr>

      </table>

    </td>
  </tr>
</table>

</body>
</html>