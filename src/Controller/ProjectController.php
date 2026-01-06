<?php

namespace App\Controller;

use App\Entity\Project;
use App\Enum\TaskStatus;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class ProjectController extends AbstractController
{
    #[Route('/project', name: 'app_project')]
    public function index(): Response
    {
        return $this->redirectToRoute('app_home');
    }

    #[Route('/project/{id}', name: 'project_view')]
    public function view(Project $project): Response
    {
        $todoTasks = [];
        $doingTasks = [];
        $doneTasks = [];

        foreach ($project->getTasks() as $task) {
            match ($task->getStatus()) {
                TaskStatus::TODO => $todoTasks[] = $task,
                TaskStatus::DOING => $doingTasks[] = $task,
                TaskStatus::DONE => $doneTasks[] = $task,
            };
        }

        return $this->render('project/view.html.twig', [
            'project' => $project,
            'todoTasks' => $todoTasks,
            'doingTasks' => $doingTasks,
            'doneTasks' => $doneTasks,
            'active_menu' => 'projets',
        ]);
    }

    #[Route('/project/{id}/edit', name: 'project_edit')]
    public function edit(Project $project): Response
    {
        return $this->render('project/edit.html.twig', [
            'project' => $project,
            'active_menu' => 'projets',
        ]);
    }

    #[Route('/project/add', name: 'project_add')] 
    public function add(): Response
    {
        return $this->render('project/add.html.twig', [
            'active_menu' => 'projets',
        ]);
    }
}
