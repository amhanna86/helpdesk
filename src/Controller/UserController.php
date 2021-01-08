<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Entity\UserType as UserTypeEntity;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use OpenApi\Annotations as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 *
 * Class UserController
 * @package App\Controller
 * @Route("/api")
 * @OA\Tag(name="User")
 */
class UserController extends AbstractFOSRestController
{
    /**
     * @Rest\Get("/user")
     * @Rest\View()
     */
    public function getCurrentUser(): Response
    {
        $user = $this->getUser();
        $view = $this->view($user, 200);
        return $this->handleView($view);
    }

    /**
     * @Rest\Get("/users")
     * @Rest\View()
     */
    public function getUsers(): Response
    {
        $users = $this->getDoctrine()->getRepository(User::class)->findAll();
        $view = $this->view($users, 200);
        return $this->handleView($view);
    }

    /**
     * @Rest\Post("/register")
     * @OA\RequestBody(
     *     @OA\JsonContent(
     *     type="object",
     *     @OA\Property(property="email", type="string"),
     *     @OA\Property(property="password", type="string"),
     *     @OA\Property(property="firstName", type="string"),
     *     @OA\Property(property="lastName", type="string"),
     *     @OA\Property(property="company", type="string"),
     *     @OA\Property(property="phone", type="string")
     * )
     * )
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return Response
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $form = $this->createForm(UserType::class);
        $form->submit($request->request->all());

        if (false === $form->isValid()) {
            return $this->handleView($this->view($form));
        }
        $user = $form->getData();
        $hashedPassword =
            $passwordEncoder->encodePassword(
                $user,
                $user->getPassword()
            );
        $form->getData()->setPassword($hashedPassword);
        $this->getDoctrine()->getManager()->persist($user);
        $this->getDoctrine()->getManager()->flush();

        return $this->handleView($this->view([
            'status' => 'ok'
        ],
            Response::HTTP_CREATED
        ));
    }

    /**
     * @Rest\Put("/user/agent/{id}")
     * @param User $user
     * @return Response
     * @IsGranted("ROLE_ADMIN")
     */
    public function setUserTypeAsAgent(User $user): Response
    {
        $agent = $this
            ->getDoctrine()
            ->getRepository(UserTypeEntity::class)->findOneBy(['type' => UserTypeEntity::AGENT]);

        $user->setUserType($agent);
        $this->getDoctrine()->getManager()->flush();

        return $this->handleView($this->view([
            'status' => 'ok'
        ],
            Response::HTTP_OK
        ));
    }

    /**
     * @Rest\Put("/user/customer/{id}")
     * @param User $user
     * @return Response
     * @IsGranted("ROLE_ADMIN")
     */
    public function setUserTypeAsCustomer(User $user): Response
    {
        $customer = $this
            ->getDoctrine()
            ->getRepository(UserTypeEntity::class)->findOneBy(['type' => UserTypeEntity::CUSTOMER]);

        $user->setUserType($customer);
        $this->getDoctrine()->getManager()->flush();

        return $this->handleView($this->view([
            'status' => 'ok'
        ],
            Response::HTTP_OK
        ));
    }
}