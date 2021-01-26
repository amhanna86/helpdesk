<?php

namespace App\Controller;

use App\Entity\Ticket;
use App\Entity\TicketComment;
use App\Form\TicketCommentType;
use App\Form\TicketEditType;
use App\Form\TicketType;
use OpenApi\Annotations as OA;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * Class TicketController
 * @package App\Controller
 * @Route("/api")
 * @OA\Tag(name="Ticket")
 */
class TicketController extends AbstractFOSRestController
{
    /**
     * @Rest\Get("/tickets")
     * @Rest\View()
     * @return Response
     * @IsGranted("ROLE_ADMIN")
     */
    public function getTickets(): Response
    {
        $tickets = $this->getDoctrine()->getRepository(Ticket::class)->findAll();
        $view = $this->view($tickets, 200);
        return $this->handleView($view);
    }


    /**
     * @Rest\Get("/tickets/user")
     * @Rest\View()
     * @return Response
     */
    public function getUserTickets(): Response
    {
        $user = $this->getUser();
        $userTickets = $this->getDoctrine()->getRepository(Ticket::class)->findByUser($user);
        $view = $this->view($userTickets, 200);
        return $this->handleView($view);
    }

    /**
     * @Rest\Get("/ticket/{id}")
     * @Rest\View()
     * @param Ticket $ticket
     * @IsGranted("view_ticket", subject="ticket")
     * @return Response
     */
    public function getTicket(Ticket $ticket): Response
    {
        $view = $this->view($ticket, 200);
        return $this->handleView($view);
    }

    /**
     * @Rest\Get("/ticket/{id}/comments")
     * @Rest\View()
     * @param Ticket $ticket
     * @return Response
     */
    public function getTicketComments(Ticket $ticket): Response
    {
        $view = $this->view($ticket->getTicketComments(), 200);
        return $this->handleView($view);
    }

    /**
     * @Rest\Post("/ticket/new")
     * @Rest\View()
     * @OA\RequestBody(
     *     @OA\JsonContent(
     *     type="object",
     *     @OA\Property(property="title", type="string"),
     *     @OA\Property(property="description", type="string")
     * )
     * )

     * @param Request $request
     * @return Response
     */
    public function postTicket(Request $request): Response
    {
        $form = $this->createForm(TicketType::class);
        $form->submit($request->request->all());

        if (false === $form->isValid()) {
            return $this->handleView($this->view($form));
        }
        /**
         * @var Ticket $ticket
         */
        $ticket = $form->getData();
        $ticket->setStatus(Ticket::NEW);
        $ticket->setCustomer($this->getUser());
        $this->getDoctrine()->getManager()->persist($ticket);
        $this->getDoctrine()->getManager()->flush();

        return $this->handleView($this->view([
            'status' => 'ok'
        ],
            Response::HTTP_CREATED
        ));
    }

    /**
     * @Rest\Put("/ticket/edit/{id}")
     * @Rest\View()
     * @OA\RequestBody(
     *     @OA\JsonContent(
     *     type="object",
     *     @OA\Property(property="status", type="string"),
     *     @OA\Property(property="agent", type="string")
     * )
     * )
     * @param Request $request
     * @param Ticket $ticket
     * @return Response
     */
    public function putTicket(Request $request, Ticket $ticket): Response
    {
        $form = $this->createForm(TicketEditType::class, $ticket);
        $form->submit($request->request->all());

        if (false === $form->isValid()) {
            return $this->handleView($this->view($form));
        }
        $this->getDoctrine()->getManager()->flush();
        $data = [
            'status' => $ticket->getStatus(),
            'agent' => $ticket->getAgent()
        ];
        $view = $this->view($data, 200);
        return $this->handleView($view);
    }

    /**
     * @Rest\Post("/ticket/comment/{id}/new")
     * @Rest\View()
     * @OA\RequestBody(
     *     @OA\JsonContent(
     *     type="object",
     *     @OA\Property(property="comment", type="string")
     * )
     * )
     * @param Request $request
     * @param Ticket $ticket
     * @return Response
     */
    public function postComment(Request $request, Ticket $ticket): Response
    {
        $form = $this->createForm(TicketCommentType::class);
        $form->submit($request->request->all());

        if (false === $form->isValid()) {
            return $this->handleView($this->view($form));
        }
        /**
         * @var TicketComment $ticketComment
         */
        $ticketComment = $form->getData();
        $ticketComment->setTicket($ticket);
        $ticketComment->setUser($this->getUser());
        $this->getDoctrine()->getManager()->persist($ticketComment);
        $this->getDoctrine()->getManager()->flush();

        $view = $this->view($ticketComment, 200);
        return $this->handleView($view);
    }
}
