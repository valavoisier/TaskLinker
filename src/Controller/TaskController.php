<?php

namespace App\Controller;

use App\Entity\Task;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class TaskController extends AbstractController
{
    #[Route('/task', name: 'app_task')]
    public function index(): Response
    {
        return $this->render('task/index.html.twig', [
            'active_menu' => 'projets',
        ]);
    }
    #[Route('/task/{id}', name: 'task_view')] 
    public function view(Task $task): Response
    {
        return $this->render('task/view.html.twig', [
            'task' => $task, 
            'active_menu' => 'projets',]);
    }
    #[Route('/project/{projectId}/task/add', name: 'task_add')] 
    public function add(int $projectId): Response
    {
        return $this->render('task/add.html.twig', [
            'projectId' => $projectId, 
            'active_menu' => 'projets',
        ]);
    }
}
