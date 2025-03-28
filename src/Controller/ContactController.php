<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'app_contact')]
    public function index(): Response
    {
        return $this->render('contact/index.html.twig');
    }

    #[Route('/contact/send-email', name: 'app_contact_send_email', methods: ['POST'])]
    public function sendEmail(Request $request, MailerInterface $mailer)
    {

    try {
        $email = (new Email())
            ->from('contact@cyrille-carre.fr')
            ->to('contact@cyrille-carre.fr')
            ->replyTo($request->get('email'))
            ->subject('Nouveau formulaire de contact')
            ->text("Nom: {$request->get('nom')}\nE-mail: {$request->get('email')}\nTéléphone: {$request->get('tel')}\nDétails:\n{$request->get('details')}");

        $mailer->send($email);

        return $this->redirectToRoute('app_contact_confirmation');
    } catch (\Exception $e) {
        return $this->redirectToRoute('app_contact_echec', [
            'error' => $e->getMessage(),
        ]);
    }
    }

    #[Route('/contact/confirmation', name: 'app_contact_confirmation')]
    public function confirmation(): Response
    {
        return $this->render('contact/confirmation.html.twig');
    }

    #[Route('/contact/echec', name: 'app_contact_echec')]
    public function echec(Request $request): Response
    {
        $error = $request->query->get('error');
        return $this->render('contact/echec.html.twig', [
            'error' => $error,
        ]);
    }
}
