<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;


#[AsController]
class UserController extends AbstractController
{
    public function __construct(private readonly Security $security)
    {
    }
    public function __invoke()
    {
        $user = $this->security->getUser();
        if(!$user){
            throw new AccessDeniedHttpException();
        }
        dump("user: ", $user);
        return $user;
    }
}
