<?php

namespace App\Security\Voter;

use App\Entity\Ticket;
use App\Entity\User;
use App\Entity\UserType;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class TicketVoter extends Voter
{
    const VIEW_TICKET = 'view_ticket';
    const EDIT_TICKET = 'edit_ticket';
    const CREATE_TICKET = 'create_ticket';

    protected function supports(string $attribute, $subject): bool
    {

        if (!in_array($attribute,
            [
                self::VIEW_TICKET,
                self::EDIT_TICKET,
                self::CREATE_TICKET
            ]
        )) {

            return false;
        }

        if (!$subject instanceof Ticket) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            // the user must be logged in; if not, deny access
            return false;
        }

        // you know $subject is a Post object, thanks to `supports()`
        /** @var Ticket $post */
        $post = $subject;

        switch ($attribute) {
            case self::VIEW_TICKET:
                return $this->canView($post, $user);
            case self::EDIT_TICKET:
                return $this->canEdit($post, $user);
            case self::CREATE_TICKET:
                return $this->canCreate($user);
        }

        throw new \LogicException('This code should not be reached!');
    }

    private function canView(Ticket $ticket, User $user): bool
    {

        // if they can edit, they can view
        if ($this->canEdit($ticket, $user)) {
            return true;
        }

        return false;
    }

    private function canEdit(Ticket $ticket, User $user): bool
    {
        return $user === $ticket->getCustomer() ||
            $user === $ticket->getAgent();
    }

    private function canCreate(User $user): bool
    {
        return $user->getUserType() === UserType::CUSTOMER;
    }
}
