<?php

namespace App\Presentation\Web\Controller;

use App\Application\UseCase\LoginUser\LoginUserCommand;
use App\Application\UseCase\LoginUser\LoginUserHandler;

class AuthController
{
    private LoginUserHandler $loginHandler;

    public function __construct(LoginUserHandler $loginHandler)
    {
        $this->loginHandler = $loginHandler;
    }

    public function showLoginForm(): void
    {
        require __DIR__ . '/../View/login.php';
    }

    public function login(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /login');
            return;
        }

        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';

        if (empty($username) || empty($password)) {
            $_SESSION['error'] = 'Por favor, completa todos los campos';
            header('Location: /login');
            return;
        }

        try {
            $command = new LoginUserCommand($username, $password);
            $correlationId = $this->loginHandler->handle($command);

            // Guardar el correlation_id en sesión para verificar la respuesta
            $_SESSION['correlation_id'] = $correlationId;
            $_SESSION['login_pending'] = true;
            $_SESSION['pending_username'] = $username;

            // Redirigir a una página de espera
            header('Location: /waiting');

        } catch (\Exception $e) {
            $_SESSION['error'] = 'Error en el proceso de autenticación: ' . $e->getMessage();
            header('Location: /login');
        }
    }

    public function checkAuthStatus(): void
{
    session_start(); // 🔥 sin esto no hay $_SESSION

    header('Content-Type: application/json');

    if (!isset($_SESSION['correlation_id'])) {
        echo json_encode(['status' => 'error', 'message' => 'No hay solicitud pendiente']);
        return;
    }

    $correlationId = $_SESSION['correlation_id'];

    try {
    $channel = \App\Infrastructure\Messaging\RabbitMQ\RabbitMQConnection::getChannel();

    $channel->queue_declare('authentication_response_queue', false, true, false, false);

    $response = null;

    $callback = function ($msg) use (&$response, $correlationId) {
        $data = json_decode($msg->body, true);

        if (isset($data['correlation_id']) && $data['correlation_id'] === $correlationId) {
            $response = $data;
        }
    };

    $channel->basic_consume('authentication_response_queue', '', false, true, false, false, $callback);

    $timeout = time() + 3; // Esperar hasta 3 segundos
    while (!$response && time() < $timeout) {
        $channel->wait(null, false, 1);
    }

    \App\Infrastructure\Messaging\RabbitMQ\RabbitMQConnection::close();

    if ($response) {
        if ($response['success']) {
            $_SESSION['user_id'] = $response['user_id'];
            $_SESSION['username'] = $response['username'];
            $_SESSION['role'] = $response['role'];
            unset($_SESSION['login_pending']);

            echo json_encode([
                'status' => 'success',
                'message' => $response['message'],
                'redirect' => '/dashboard'
            ]);
        } else {
            unset($_SESSION['login_pending']);
            echo json_encode([
                'status' => 'error',
                'message' => $response['message']
            ]);
        }
    } else {
        echo json_encode(['status' => 'pending']);
    }

} catch (\Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Error al verificar autenticación: ' . $e->getMessage()
    ]);
}

}



    public function logout(): void
    {
        session_destroy();
        header('Location: /login');
    }
}
