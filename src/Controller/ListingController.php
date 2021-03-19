<?php

namespace App\Controller;

use App\Entity\Listing;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/", name="listing_")
 */

class ListingController extends AbstractController
{
    /**
     * @Route("/{listingId}", name="show", requirements={"listingId"="\d+"})
     */
    public function show(EntityManagerInterface $entityManager, $listingId = null) 
    {
        $listings = $entityManager->getRepository(Listing::class)->findAll();

        if (!empty($listingId)){
            $currentListing = $entityManager->getRepository(Listing::class)->find($listingId);
        }

        if (empty($currentListing)){
            $currentListing = current($listings);
        }

        return $this->render("listing.html.twig", ['listings' => $listings, 'currentListing' => $currentListing]);
    }

    /**
     * @Route("/new", methods="POST", name="create")
     */
    public function create(EntityManagerInterface $entityManager, Request $request) 
    {
        $name = $request->get('name');

        if(empty($name)) {
            $this->addFlash(
                "warning",
                "Un nom de liste est obligatoire !"
            );
            return $this->redirectToRoute('listing_show');
        }

        $listing = new Listing();
        $listing->setName($name);


        try {
            $entityManager->persist($listing);
            $entityManager->flush();

            $this->addFlash(
                "success",
                "La liste « $name » a été crée avec succès"
            );
        } catch (UniqueConstraintViolationException $e) {
            $this->addFlash(
                "warning",
                "La liste $name existe déjà."
            );
        }

        return $this->redirectToRoute('listing_show');
    }

    /**
     * @Route("/{listingId}/delete", name="delete", requirements={"listingId"="\d+"})
     */
    public function delete(EntityManagerInterface $entityManager, $listingId)
    {
        $listing = $entityManager->getRepository(Listing::class)->find($listingId);

        if (empty($listing)) {
            $this->addFlash(
                "warning",
                "Impossible de supprimer de la liste"
            );
        } else {
            $entityManager->remove($listing);
            $entityManager->flush();

            $name = $listing->getName();

            $this->addFlash(
                "success",
                "La liste $name à été supprimé avec succès"
            );
        }

        return $this->redirectToRoute('listing_show');

    }
}
