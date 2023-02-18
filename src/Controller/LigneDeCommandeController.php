<?php

namespace App\Controller;

use App\Entity\LigneDeCommande;
use App\Form\LigneDeCommandeType;
use App\Repository\LigneDeCommandeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/ligne/de/commande')]
class LigneDeCommandeController extends AbstractController
{
    #[Route('/', name: 'app_ligne_de_commande_index', methods: ['GET'])]
    public function index(LigneDeCommandeRepository $ligneDeCommandeRepository): Response
    {
        return $this->render('ligne_de_commande/index.html.twig', [
            'ligne_de_commandes' => $ligneDeCommandeRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_ligne_de_commande_new', methods: ['GET', 'POST'])]
    public function new(Request $request, LigneDeCommandeRepository $ligneDeCommandeRepository): Response
    {
        $ligneDeCommande = new LigneDeCommande();
        $form = $this->createForm(LigneDeCommandeType::class, $ligneDeCommande);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $ligneDeCommandeRepository->save($ligneDeCommande, true);

            return $this->redirectToRoute('app_ligne_de_commande_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('ligne_de_commande/new.html.twig', [
            'ligne_de_commande' => $ligneDeCommande,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_ligne_de_commande_show', methods: ['GET'])]
    public function show(LigneDeCommande $ligneDeCommande): Response
    {
        return $this->render('ligne_de_commande/show.html.twig', [
            'ligne_de_commande' => $ligneDeCommande,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_ligne_de_commande_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, LigneDeCommande $ligneDeCommande, LigneDeCommandeRepository $ligneDeCommandeRepository): Response
    {
        $form = $this->createForm(LigneDeCommandeType::class, $ligneDeCommande);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $ligneDeCommandeRepository->save($ligneDeCommande, true);

            return $this->redirectToRoute('app_ligne_de_commande_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('ligne_de_commande/edit.html.twig', [
            'ligne_de_commande' => $ligneDeCommande,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_ligne_de_commande_delete', methods: ['POST'])]
    public function delete(Request $request, LigneDeCommande $ligneDeCommande, LigneDeCommandeRepository $ligneDeCommandeRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$ligneDeCommande->getId(), $request->request->get('_token'))) {
            $ligneDeCommandeRepository->remove($ligneDeCommande, true);
        }

        return $this->redirectToRoute('app_ligne_de_commande_index', [], Response::HTTP_SEE_OTHER);
    }
}
