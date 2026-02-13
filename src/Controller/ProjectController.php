<?php

namespace App\Controller;

use App\Entity\Project;
use App\Enum\TaskStatus;
use App\Form\ProjectType;
use App\Repository\ProjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ProjectController extends AbstractController
{
    /** 
     * Liste des projets (redirection vers la page d'accueil)
     */
    #[Route('/project', name: 'app_project')]
    public function index(ProjectRepository $projectRepository): Response
    {
        return $this->render('project/index.html.twig', [
            'projects' => $projectRepository->findBy(['archived' => false]),
            'active_menu' => 'projets',
        ]);
    }

    /** 
     * Ajouter un projet
     */
    #[Route('/project/add', name: 'project_add', methods: ['GET', 'POST'])]
    public function add(Request $request, EntityManagerInterface $entityManager): Response
    {
        $project ??= new Project();
        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);     
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($project);
            $entityManager->flush();

            return $this->redirectToRoute('project_view', [
                'id' => $project->getId(),
            ]);
        }
        
        // Debug: afficher les erreurs dans les logs
        if ($form->isSubmitted() && !$form->isValid()) {
            dump($form->getErrors(true, false));
        }
        
        return $this->render('project/add.html.twig', [
            'form' => $form,
            'active_menu' => 'projets',
        ]);
    }

    /** 
     * Vue d'un projet
     */
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

    /** 
     * Éditer un projet
     */
    #[Route('/project/{id}/edit', name: 'project_edit')]
    public function edit(Project $project, Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('project_view', [
                'id' => $project->getId(),
            ]);
        }

        return $this->render('project/edit.html.twig', [
            'form' => $form,
            'project' => $project,
            'active_menu' => 'projets',
        ]);
    }

    /** 
     * Supprimer un projet
     */
    #[Route('/project/{id}/delete', name: 'project_delete', methods: ['POST'])]
    public function delete(Project $project, EntityManagerInterface $entityManager, Request $request): Response
    {
        // Protection CSRF
        if ($this->isCsrfTokenValid('delete_project_' . $project->getId(), $request->request->get('_token'))) {

            $entityManager->remove($project);
            $entityManager->flush();

            return $this->redirectToRoute('app_home');
        }

        // Si token invalide → retour à la vue du projet
        return $this->redirectToRoute('project_add', [
            'id' => $project->getId(),
        ]);
    }

    /** 
     * Archiver un projet
     */
    #[Route('/project/{id}/archive', name: 'project_archive', methods: ['POST'])]
    public function archive(Project $project, EntityManagerInterface $entityManager, Request $request): Response
    {
        if ($this->isCsrfTokenValid('archive_project_' . $project->getId(), $request->request->get('_token'))) {
            // Force Doctrine à recharger l’état réel depuis la base 
            $entityManager->refresh($project);
           
            $project->setArchived(true);
            $entityManager->flush();
            return $this->redirectToRoute('app_home');
        }

        return $this->redirectToRoute('project_view', [
            'id' => $project->getId(),
        ]);
    }
}
