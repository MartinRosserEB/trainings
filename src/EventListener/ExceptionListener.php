<?php

namespace App\EventListener;

use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use App\SwiftPlugin\SwiftExceptionLoggerPlugin;

class ExceptionListener
{
    private $mailer;
    private $templating;
    private $eL;
    private $from;
    private $to;

    public function __construct(\Swift_Mailer $mailer, \Twig_Environment $templating, SwiftExceptionLoggerPlugin $eL, $from, $to)
    {
        $this->mailer = $mailer;
        $this->templating = $templating;
        $this->eL = new \Swift_Plugins_LoggerPlugin($eL);
        $this->from = $from;
        $this->to = $to;
    }

    public function onKernelException(ExceptionEvent $event)
    {
        $message = (new \Swift_Message('Error message from trainings installation'))
        ->setFrom($this->from)
        ->setTo($this->to)
        ->setBody(
            $this->templating->render(
                'emails/error_message.html.twig',
                [
                    'exception' => $event->getException(),
                ]
            ),
            'text/html'
        )
        ->addPart(
            $this->templating->render(
                'emails/error_message.txt.twig',
                [
                    'exception' => $event->getException(),
                ]
            ),
            'text/plain'
        );

        $this->mailer->registerPlugin($this->eL);
        $this->mailer->send($message);
    }
}
