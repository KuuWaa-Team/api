<?php 

namespace App\Service;

use Exception;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

class MailerService {
    public function __construct(
        private MailerInterface $mailer,
    )
    {}

    /**
     * @throws TransportExceptionInterface
     */
    public function sendEmail (
        string $to, 
        string $template,
        array $data,
        string $subject,
        array $attachment = [],
        string $from = "noreply@kuuwaa.com"
    ): bool {
        $email = (new TemplatedEmail())
            ->from(new Address($from, "KuuWaa"))
            ->to($to)
            ->subject($subject)
            ->htmlTemplate($template)
            ->context($data)
        ;
        if (!empty($attachment)) {
            $email->attachFromPath($attachment["path"], $attachment["name"]);
        }
        try {
            $this->mailer->send($email);
            return true;
        } catch (Exception $e) {
            return true;
        }
    }
}