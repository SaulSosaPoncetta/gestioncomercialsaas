<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; background: #f4f6f8; margin: 0; padding: 0; }
        .container { max-width: 480px; margin: 30px auto; background: #fff; border-radius: 8px; overflow: hidden; }
        .header { background: #14532d; color: #fff; padding: 20px; text-align: center; }
        .body { padding: 24px; color: #333; }
        .plan-box { background: #f0f7f2; border: 1px solid #14532d; border-radius: 6px; padding: 16px; margin: 16px 0; text-align: center; }
        .btn { display: inline-block; background: #14532d; color: #fff !important; padding: 12px 24px; border-radius: 6px; text-decoration: none; font-weight: bold; }
        .warning { background: #fff8e1; border: 1px solid #ffd54f; border-radius: 6px; padding: 12px; margin-top: 16px; font-size: 13px; }
        .footer { text-align: center; padding: 16px; color: #999; font-size: 12px; }
    </style>
</head>
<body>
<div class="container">
    <div class="header"><h2>Gestión Comercial</h2></div>
    <div class="body">
        <p>Hola {{ $nombreAdmin }},</p>
        <p>Gracias por registrar a <strong>{{ $empresa->razon_social }}</strong> en Gestión Comercial. Para completar el registro y activar tu cuenta, hacé clic en el botón de abajo.</p>

        @if($plan)
        <div class="plan-box">
            <div>Plan seleccionado</div>
            <strong>{{ $plan->nombre }}</strong>
            <div>${{ number_format($plan->precio, 2) }} / mensual</div>
        </div>
        @endif

        <div style="text-align:center; margin: 24px 0;">
            <a href="{{ $activationUrl }}" class="btn">Activar mi cuenta</a>
        </div>

        <div class="warning">
            <strong>Importante:</strong> Este link es de uso único y expira en 48 horas. Si no creaste esta cuenta, ignorá este mensaje.
        </div>

        <hr>
        <p style="font-size:13px;color:#666">Si el botón no funciona, copiá y pegá este link en tu navegador:</p>
        <p style="font-size:12px;word-break:break-all;color:#14532d">{{ $activationUrl }}</p>
    </div>
    <div class="footer">Gestión Comercial &copy; 2026 — Sistema de gestión comercial</div>
</div>
</body>
</html>
