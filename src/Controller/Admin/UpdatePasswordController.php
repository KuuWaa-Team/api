<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class UpdatePasswordController extends AbstractController
{
    #[Route('/admin/update/password', name: 'app_admin_update_password')]
    public function index(): Response
    {
        return $this->render('admin/update_password/index.html.twig', [
            'controller_name' => 'UpdatePasswordController',
        ]);
    }
}
