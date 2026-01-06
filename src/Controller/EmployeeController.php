<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class EmployeeController extends AbstractController
{
    #[Route('/employee', name: 'employee_index')]
    public function index(): Response
    {
        return $this->render('employee/index.html.twig', [
            'active_menu' => 'employes',
        ]);
    }

    #[Route('/employees/{id}', name: 'employee_view', requirements: ['id' => '\d+'])]
    public function view(int $id): Response
    {
        return $this->render('employee/view.html.twig', [
            'active_menu' => 'employes',
            'employeeId' => $id,
        ]);
    }

    #[Route('/employees/add', name: 'employee_add')] 
    public function add(): Response
    {
        return $this->render('employee/add.html.twig', [
            'active_menu' => 'employes',
        ]);
    }
}
