<?php

namespace App\Controller;

use App\Entity\Employee;
use App\Form\EmployeeType;
use App\Repository\EmployeeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class EmployeeController extends AbstractController
{   
    /**
     * Liste des employés
     */
    #[Route('/employee', name: 'employee_index')]
    #[IsGranted('ROLE_ADMIN')]
    public function index(EmployeeRepository $employeeRepository): Response
    {
        return $this->render('employee/index.html.twig', [
            'employees' => $employeeRepository->findAll(),
            'active_menu' => 'employes',
        ]);
    }

    /**
     * Vue d'un employé
     */
    #[Route('/employees/{id}', name: 'employee_view', requirements: ['id' => '\d+'])]
    #[IsGranted('ROLE_ADMIN')]
    public function view(Employee $employee): Response
    {
        return $this->render('employee/view.html.twig', [
            'employee' => $employee,
            'active_menu' => 'employes',
        ]);
    }

    /**     
     * Ajouter un employé
     */
    #[Route('/employee/edit/{id}', name: 'employee_edit', requirements: ['id' => '\d+'])] 
    #[IsGranted('ROLE_ADMIN')]
    public function edit(Request $request, Employee $employee, EntityManagerInterface $em): Response    
    {
        $form = $this->createForm(EmployeeType::class, $employee);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            return $this->redirectToRoute('employee_index');
        }
        return $this->render('employee/edit.html.twig', ['form' => $form, 'employee' => $employee, 'active_menu' => 'employes',]);
    }

    /** * Supprimer un employé */ 
    #[Route('/employee/delete/{id}', name: 'employee_delete', methods: ['POST'], requirements: ['id' => '\d+'])] 
    #[IsGranted('ROLE_ADMIN')]
    public function delete(Request $request, Employee $employee, EntityManagerInterface $em): Response 
    { 
        if ($this->isCsrfTokenValid('delete_employee_' . $employee->getId(), $request->request->get('_token'))) { 
            // Retirer l'employé de toutes ses tâches 
            foreach ($employee->getTasks() as $task) { $task->setEmployee(null); } 
            // Retirer l'employé de tous ses projets (ManyToMany) 
            foreach ($employee->getProjects() as $project) { $project->removeEmployee($employee); } 
            // Supprimer l'employé 
            $em->remove($employee); $em->flush(); 
            } 
            return $this->redirectToRoute('employee_index'); }
}
