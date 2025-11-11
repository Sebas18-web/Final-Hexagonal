<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Procesando Autenticación...</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .waiting-container {
            background: white;
            padding: 60px 40px;
            border-radius: 15px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            text-align: center;
            max-width: 450px;
            width: 100%;
        }
        .spinner {
            width: 60px;
            height: 60px;
            border: 6px solid #f3f3f3;
            border-top: 6px solid #667eea;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto 25px;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        h2 {
            color: #333;
            margin-bottom: 15px;
            font-size: 24px;
        }
        p {
            color: #666;
            line-height: 1.6;
            margin-bottom: 10px;
        }
        .info-box {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin-top: 25px;
        }
        .info-box p {
            font-size: 13px;
            color: #777;
        }
        .rabbit-icon {
            font-size: 40px;
            margin-bottom: 10px;
        }
        .status-message {
            background: #e3f2fd;
            padding: 12px;
            border-radius: 8px;
            margin-top: 20px;
            color: #1976d2;
            font-size: 14px;
            font-weight: 500;
        }
    </style>
    <script>
        let attempts = 0;
        const maxAttempts = 30; // 30 intentos = 60 segundos

        // Polling para verificar el estado de autenticación
        const checkInterval = setInterval(function() {
            attempts++;
            
            fetch('/check-auth')
                .then(response => response.json())
                .then(data => {
                    console.log('Status:', data);
                    
                    if (data.status === 'success') {
                        clearInterval(checkInterval);
                        window.location.href = '/dashboard';
                    } else if (data.status === 'error') {
                        clearInterval(checkInterval);
                        window.location.href = '/login?error=auth_failed';
                    }
                    
                    // Timeout después de 60 segundos
                    if (attempts >= maxAttempts) {
                        clearInterval(checkInterval);
                        alert('Tiempo de espera agotado. Por favor, intenta nuevamente.');
                        window.location.href = '/login';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        }, 2000); // Verificar cada 2 segundos

        // Mensaje para el usuario sobre el consumer
        setTimeout(function() {
            const statusMsg = document.getElementById('status-msg');
            if (statusMsg) {
                statusMsg.innerHTML = '⚠️ Si el proceso tarda mucho, asegúrate de que el consumer de RabbitMQ esté ejecutándose: <code>php consumer.php</code>';
                statusMsg.style.background = '#fff3cd';
                statusMsg.style.color = '#856404';
            }
        }, 10000); // Mostrar después de 10 segundos
    </script>
</head>
<body>
    <div class="waiting-container">
        <div class="rabbit-icon">🐰</div>
        <div class="spinner"></div>
        <h2>Procesando Autenticación</h2>
        <p>Tu solicitud está siendo procesada a través de RabbitMQ...</p>
        <p style="font-size: 14px; color: #999;">Por favor espera un momento</p>
        
        <div class="info-box">
            <p><strong>🔄 Flujo de Autenticación:</strong></p>
            <p>1. Solicitud enviada a RabbitMQ</p>
            <p>2. Consumer procesa el mensaje</p>
            <p>3. Validación en base de datos</p>
            <p>4. Respuesta y redirección</p>
        </div>

        <div class="status-message" id="status-msg">
            ⏳ Esperando respuesta del consumer...
        </div>
    </div>
</body>
</html>