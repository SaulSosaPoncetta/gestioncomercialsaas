<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; background: #f4f6f8; margin: 0; padding: 0; }
        .container { max-width: 480px; margin: 30px auto; background: #fff; border-radius: 8px; overflow: hidden; }
        .header { background: #14532d; color: #fff; padding: 20px; text-align: center; }
        .body { padding: 24px; color: #333; }
        .btn { display: inline-block; background: #14532d; color: #fff !important; padding: 12px 24px; border-radius: 6px; text-decoration: none; font-weight: bold; }
        .footer { text-align: center; padding: 16px; color: #999; font-size: 12px; }
    </style>
</head>
<body>
<div class="container">
    <div class="header"><h2>Gestión Comercial</h2></div>
    <div class="body">
        <p>¡Listo! La cuenta de <strong>{{ $empresa->razon_social }}</strong> ya está activa.</p>
        <p>Ya podés ingresar al sistema con el email y la contraseña que usaste al registrarte.</p>
        <div style="text-align:center; margin: 24px 0;">
            <a href="https://comercialpos.migestion.com.ar/login" class="btn">Ir al login</a>
        </div>
    </div>
    <div class="footer">Gestión Comercial &copy; 2026 — Sistema de gestión comercial</div>
</div>
</body>
</html>
