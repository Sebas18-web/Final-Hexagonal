<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Panel de Control</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f7fa;
            padding: 20px;
        }
        .dashboard {
            max-width: 900px;
            margin: 0 auto;
        }
        .card {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            margin-bottom: 20px;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #eee;
        }
        h1 { 
            color: #333; 
            font-size: 28px;
            margin-bottom: 5px;
        }
        .welcome-text {
            color: #666;
            font-size: 16px;
        }
        .role-badge {
            padding: 12px 24px;
            border-radius: 25px;
            font-weight: bold;
            font-size: 16px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .role-admin {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        .role-user {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
        }
        .logout-btn {
            padding: 12px 24px;
            background: #ff4757;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            font-weight: 600;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .logout-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 71, 87, 0.3);
        }
        .info-box {
            background: #f8f9fa;
            padding: 25px;
            border-radius: 10px;
            margin: 20px 0;
        }
        .info-box h3 {
            color: #333;
            margin-bottom: 15px;
            font-size: 18px;
        }
        .info-item {
            display: flex;
            padding: 10px 0;
            border-bottom: 1px solid #e0e0e0;
        }
        .info-item:last-child {
            border-bottom: none;
        }
        .info-label {
            font-weight: 600;
            color: #555;
            min-width: 120px;
        }
        .info-value {
            color: #333;
            font-family: 'Courier New', monospace;
        }
        .features {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        .feature-card {
            background: white;
            padding: 25px;
            border-radius: 10px;
            border: 2px solid #e0e0e0;
            text-align: center;
        }
        .feature-icon {
            font-size: 40px;
            margin-bottom: 10px;
        }
        .feature-title {
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
        }
        .feature-desc {
            color: #666;
            font-size: 14px;
        }
        .success-message {
            background: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid #28a745;
        }
    </style>
</head>
<body>
    <div class="dashboard">
        <div class="card">
            <div class="success-message">
                ✅ Autenticación exitosa a través de RabbitMQ
            </div>
            
            <div class="header">
                <div>
                    <h1>Bienvenido, <?= htmlspecialchars($_SESSION['username'] ?? 'Usuario') ?>!</h1>
                    <p class="welcome-text">Has iniciado sesión correctamente</p>
                    <div style="margin-top: 15px;">
                        <span class="role-badge <?= ($_SESSION['role'] ?? '') === 'admin' ? 'role-admin' : 'role-user' ?>">
                            <?= ($_SESSION['role'] ?? '') === 'admin' ? '👑 Usuario administrador' : '👤 Usuario normal' ?>
                        </span>
                    </div>
                </div>
                <a href="/logout" class="logout-btn">🚪 Cerrar Sesión</a>
            </div>

            <div class="info-box">
                <h3>📊 Información de tu Sesión</h3>
                <div class="info-item">
                    <span class="info-label">User ID:</span>
                    <span class="info-value"><?= $_SESSION['user_id'] ?? 'N/A' ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Username:</span>
                    <span class="info-value"><?= htmlspecialchars($_SESSION['username'] ?? 'N/A') ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Rol:</span>
                    <span class="info-value"><?= htmlspecialchars($_SESSION['role'] ?? 'N/A') ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Session ID:</span>
                    <span class="info-value"><?= session_id() ?></span>
                </div>
            </div>
        </div>

        <div class="card">
            <h3 style="margin-bottom: 20px;">🏗️ Arquitectura Implementada</h3>
            <div class="features">
                <div class="feature-card">
                    <div class="feature-icon">⬢</div>
                    <div class="feature-title">Hexagonal</div>
                    <div class="feature-desc">Arquitectura de puertos y adaptadores</div>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">🐰</div>
                    <div class="feature-title">RabbitMQ</div>
                    <div class="feature-desc">Mensajería asíncrona</div>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">🗄️</div>
                    <div class="feature-title">MySQL</div>
                    <div class="feature-desc">Persistencia de datos</div>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">🔒</div>
                    <div class="feature-title">Seguridad</div>
                    <div class="feature-desc">Passwords con bcrypt</div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>