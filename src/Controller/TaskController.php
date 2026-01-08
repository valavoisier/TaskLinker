<?php

namespace App\Controller;

use App\Entity\Task;
use App\Form\TaskType;
use App\Entity\Project;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class TaskController extends AbstractController
{

    #[Route('/project/{id}/task/add', name: 'task_add')]
    public function add(Project $project, Request $request, EntityManagerInterface $entityManager): Response
    {
        $task = new Task();
        $task->setProject($project);
        $form = $this->createForm(TaskType::class, $task, [
             'project' => $project, 
            ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($task);
            $entityManager->flush();
            return $this->redirectToRoute('project_view', ['id' => $project->getId(),]);
        }
        return $this->render('task/add.html.twig', [
            'form' => $form->createView(),
            'active_menu' => 'projets',
        ]);
    }

    #[Route('/task/{id}/edit', name: 'task_edit')] 
    public function edit(Task $task, Request $request, EntityManagerInterface $entityManager): Response
    {
        $project = $task->getProject();// Récupère le projet associé à la tâche
        $form = $this->createForm(TaskType::class, $task, ['project' => $project, // On passe le projet au formulaire 
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            return $this->redirectToRoute('project_view', [
                'id' => $project->getId(),
            ]);
        }
        return $this->render('task/edit.html.twig', [
            'form' => $form->createView(), 
            'task' => $task,
            'project' => $project, 
            'active_menu' => 'projets',]);
    }

    #[Route('/task/{id}/delete', name: 'task_delete', methods: ['POST'])]
public function delete(Task $task, EntityManagerInterface $entityManager): Response
{
    $projectId = $task->getProject()->getId();

    $entityManager->remove($task);
    $entityManager->flush();

    return $this->redirectToRoute('project_view', [
        'id' => $projectId,
    ]);
}

}
