<?php

require_once __DIR__ . '/../../vendor/autoload.php';


session_start();

use App\Presentation\Web\Controller\AuthController;
use App\Application\UseCase\LoginUser\LoginUserHandler;
use App\Infrastructure\Messaging\RabbitMQ\AuthenticationProducer;

// Routing simple
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Inicializar dependencias
$producer = new AuthenticationProducer();
$loginHandler = new LoginUserHandler($producer);
$authController = new AuthController($loginHandler);

// Rutas
switch ($uri) {
    case '/':
    case '/login':
        if (isset($_SESSION['user_id'])) {
            header('Location: /dashboard');
            exit;
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $authController->login();
        } else {
            $authController->showLoginForm();
        }
        break;

    case '/dashboard':
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }
        require __DIR__ . '/../src/Presentation/Web/View/dashboard.php';
        break;

    case '/waiting':
        if (!isset($_SESSION['login_pending'])) {
            header('Location: /login');
            exit;
        }
        require __DIR__ . '/../src/Presentation/Web/View/waiting.php';
        break;

    case '/check-auth':
        $authController->checkAuthStatus();
        break;

    case '/logout':
        $authController->logout();
        break;

    default:
        http_response_code(404);
        echo "<!DOCTYPE html>
        <html lang='es'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>404 - Página no encontrada</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                    min-height: 100vh;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    margin: 0;
                }
                .error-container {
                    background: white;
                    padding: 60px 40px;
                    border-radius: 15px;
                    text-align: center;
                    box-shadow: 0 20px 60px rgba(0,0,0,0.3);
                }
                h1 { color: #333; font-size: 72px; margin: 0; }
                p { color: #666; font-size: 18px; margin: 20px 0; }
                a {
                    display: inline-block;
                    padding: 12px 30px;
                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                    color: white;
                    text-decoration: none;
                    border-radius: 8px;
                    margin-top: 20px;
                    font-weight: bold;
                }
            </style>
        </head>
        <body>
            <div class='error-container'>
                <h1>404</h1>
                <p>Página no encontrada</p>
                <a href='/login'>Volver al inicio</a>
            </div>
        </body>
        </html>";
        break;
}