<?php 

namespace App\Controller;

use App\Entity\Task;
use App\Entity\Listing;
use App\Form\TaskType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("{listingId}/task", name="task_", requirements={"listingId"="\d+"})
 */
class TaskController extends AbstractController
{
    /**
     * @Route("/new", name="create")
     */
    public function create(EntityManagerInterface $entityManager, Request $request, $listingId)
    {
        $listing = $entityManager->getRepository(Listing::class)->find($listingId);

        $task = new Task();
        $task->setListing($listing);

        $form = $this->createForm(TaskType::class, $task);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($task);
            $entityManager->flush();

            return $this->redirectToRoute('listing_show', ['listingId' => $listingId]);
        }

        return $this->render('task.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/{taskId}/edit", name="edit", requirements={"taskId"="\d+"})
     */
    public function edit(EntityManagerInterface $entityManager, Request $request, $listingId, $taskId) 
    {
        $task = $entityManager->getRepository(Task::class)->find($taskId);

        if (empty($task)) {
            $this->addFlash(
                'warning',
                'Impossible de modifier la tâche'
            );
            
            return $this->redirectToRoute('listing_show', ['listingId' => $listingId]);
        }

        $name = $task->getName();

        $form = $this->createForm(TaskType::class, $task);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash(
                'success', 
                "La tâche $name a été modifiée avec succès"
            );

            return $this->redirectToRoute('listing_show', ['listingId' => $listingId]);
        }

        return $this->render('task.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/{taskId}/delete", name="delete", requirements={"taskId"="\d+"})
     */
    public function delete(EntityManagerInterface $entityManager, Request $request, $listingId, $taskId) 
    {
        $task = $entityManager->getRepository(Task::class)->find($taskId);

        if (empty($task)) {
            $this->addFlash(
                'warning',
                'Impossible de supprimer la tâche'
            );
            
            return $this->redirectToRoute('listing_show', ['listingId' => $listingId]);
        } else {


            $name = $task->getName();

            $entityManager->remove($task);
            $entityManager->flush();
    
                $this->addFlash(
                    'success', 
                    "La tâche $name a été supprimer"
                );
            }
                
            return $this->redirectToRoute('listing_show', ['listingId' => $listingId]);
        
    }
}