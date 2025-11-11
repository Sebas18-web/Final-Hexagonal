<?php

namespace App\Infrastructure\Messaging\RabbitMQ;

use App\Domain\Repository\UserRepositoryInterface;
use App\Domain\ValueObject\Username;
use PhpAmqpLib\Message\AMQPMessage;

class AuthenticationConsumer
{
    private $channel;
    private UserRepositoryInterface $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->channel = RabbitMQConnection::getChannel();
        $this->userRepository = $userRepository;
    }

    public function consume(): void
    {
        echo " [*] Esperando mensajes de autenticación. Para salir presiona CTRL+C\n";

        // Declarar la cola de respuesta
        $this->channel->queue_declare(
            'authentication_response_queue',
            false,
            true,
            false,
            false
        );

        $callback = function (AMQPMessage $msg) {
            $data = json_decode($msg->body, true);
            
            echo " [x] Procesando login para: {$data['username']}\n";

            try {
                $username = new Username($data['username']);
                $user = $this->userRepository->findByUsername($username);

                if ($user === null) {
                    $response = [
                        'success' => false,
                        'message' => 'Usuario no encontrado',
                        'correlation_id' => $data['correlation_id']
                    ];
                    $this->userRepository->recordLoginAttempt($data['username'], false);
                } else {
                    $passwordValid = $user->verifyPassword($data['password']);
                    
                    if ($passwordValid) {
                        $response = [
                            'success' => true,
                            'message' => $user->getRoleMessage(),
                            'user_id' => $user->getId()->getValue(),
                            'username' => $user->getUsername()->getValue(),
                            'role' => $user->getRole()->getValue(),
                            'correlation_id' => $data['correlation_id']
                        ];
                        $this->userRepository->recordLoginAttempt($data['username'], true);
                    } else {
                        $response = [
                            'success' => false,
                            'message' => 'Contraseña incorrecta',
                            'correlation_id' => $data['correlation_id']
                        ];
                        $this->userRepository->recordLoginAttempt($data['username'], false);
                    }
                }

                // Enviar respuesta
                $responseMsg = new AMQPMessage(
                    json_encode($response),
                    ['correlation_id' => $msg->get('correlation_id')]
                );

                $this->channel->basic_publish(
                    $responseMsg,
                    '',
                    $msg->get('reply_to')
                );

                $msg->ack();

                echo " [✓] Respuesta enviada\n";

            } catch (\Exception $e) {
                echo " [!] Error: " . $e->getMessage() . "\n";
                $msg->nack();
            }
        };

        $this->channel->basic_qos(null, 1, null);
        $this->channel->basic_consume(
            'authentication_queue',
            '',
            false,
            false,
            false,
            false,
            $callback
        );

        while ($this->channel->is_consuming()) {
            $this->channel->wait();
        }
    }
}