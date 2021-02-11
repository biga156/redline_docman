<?php
namespace App\Controller;

use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
//use Symfony\Component\Mime\Address;

class MailerController extends AbstractController
{
    /**
     * @Route("/email_registration", name="email_registration_succes", methods={"GET"})
     */
    public function sendEmailRegistration(MailerInterface $mailer)
    {
         
        $email = (new Email())
        ->from(new Address('bigastora@gmail.com', 'Bigastora Mail Bot'))
            ->to($_GET['ta'])
            //->cc('cc@example.com')
            ->bcc('biga256@gmail.com')
            //->replyTo('fabien@example.com')
            //->priority(Email::PRIORITY_HIGH)
            ->subject('Registration succes!')
            ->text($_GET['un'].', you have successfully registered!');
            //->html('<p>See Twig integration for better HTML integration!</p>');

        $mailer->send($email);
        return $this->redirectToRoute('app_login');
        
    }

     /**
     * @Route("/email_role", name="email_role_changed", methods={"GET"})
     */
    public function sendEmailRole(MailerInterface $mailer)
    {
         
        $email = (new Email())
        ->from(new Address('bigastora@gmail.com', 'Bigastora Mail Bot'))
            ->to($_GET['ta'])
            //->cc('cc@example.com')
            //->bcc('bigastora@gmail.com')
            //->replyTo('fabien@example.com')
            //->priority(Email::PRIORITY_HIGH)
            ->subject('Your role has been changed!')
            //->text('Hello '.$_GET['un'].', your new role has been activated! Your new role is '.$_GET['r'].'.' )
            ->html('<p>Hello '.$_GET['un'].', your new role has been activated! Your new role is '.$_GET['r'].'.</p><p>Now you can log in <a href="https://www.bigastora.com">our site</a>!</p>');

        $mailer->send($email);
        return $this->redirectToRoute('users_index');
        
    }
}