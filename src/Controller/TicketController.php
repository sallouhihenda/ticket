<?php

namespace App\Controller;

use App\Entity\Ticket;
use App\Repository\TicketRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\DateTime;
#[Route ('/ticket')]
class TicketController extends AbstractController
{
    #[Route ('/list_tous/{nbPage?1}', name: 'tickets_list')]
    public function listtous($nbPage): Response
    {
        $limit = 3;
        $offset = ($nbPage-1)*$limit;
        $repository=$this->getDoctrine()->getRepository(ticket::class);
        $tickets = $this->getDoctrine()->getRepository( Ticket::class)->findAll();
        $tickets = $repository->findBy([],[],$limit,$offset);
        return $this->render('ticket/index.html.twig', [
            'tickets' => $tickets,
            'nbpage'  =>$nbPage
        ]);
    }

    #[Route ('/add/{titre}/{nom}/{description}', name: 'ticket.add')]
    public function addTicket($titre, $nom, $description)
    {
        $manager = $this->getDoctrine()->getManager();
        $ticket = new ticket();
        $ticket->SetTitre($titre);
        $ticket->SetNom($nom);
        $ticket->SetDescription($description);
        $ticket->SetStatut( "en attente");
        $ticket->SetDate(new \DateTime());

        $manager->persist($ticket);
        $manager->flush();
        return $this->redirectToRoute(route: 'tickets_list');
    }

    #[Route ('/update/{id}/{titre}/{nom}/{description}', name: 'ticket.update')]
    public function upDate(Request $request, $id, $titre, $nom, $description)
    {
        $ticket = $this->getDoctrine()->getRepository(persistentObject: ticket::class)->find($id);
        if ($ticket) {
            $ticket->setTitre($titre);
            $ticket->setNom($nom);
            $ticket->setDate(new\DateTime());
            $ticket->setDescription($description);
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($ticket);
            $manager->flush();
        }
        return $this->redirectToRoute('tickets_list');
    }


    #[Route ('/delete/{id}', name: 'ticket_delete')]
    public function deleteTicket(Request $request, $id)
    {


        $ticket = $this->getDoctrine()->getRepository(Ticket::class)->findOneById($id);

        if (!$ticket) {
            throw $this->createNotFoundException('No ticket found for id '.$id);
        }

       $manager=$this->getDoctrine()->getManager();

        $manager->remove($ticket);
        $manager->flush();

        return $this->redirectToRoute( 'tickets_list');
    }

    #[Route ('/Liste_intervale/{date_min}/{date_max}', name: 'ticket_list_intervale')]
    public function index(TicketRepository $ticketRepository,$date_min, $date_max): Response
    {

        $tickets=$ticketRepository->TicketByDate($date_min,$date_max);
        return $this->render('ticket/list_intervale.html.twig', [
            'ticket' => $tickets
        ]);

    }
}




