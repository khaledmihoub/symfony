<?php

namespace App\Controller;
use App\Entity\Produit;
use App\Entity\Commande;
use App\Entity\LigneDeCommande;
use App\Entity\User;
use App\Form\CommandeType;
use App\Repository\CommandeRepository;
use App\Repository\LigneDeCommandeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\Session;
use Doctrine\ORM\EntityManagerInterface;
#[Route('/commande')]
class CommandeController extends AbstractController
{
    #[Route('/home', name: 'app_commande_index', methods: ['GET'])]
    public function index(CommandeRepository $commandeRepository,Request $request, EntityManagerInterface $entityManager): Response
    {   
        $session = new Session();
        if( empty($session->get('panier'))){
            $session->set('panier', []);
        }   
       
        //get all products frome database 
        $produits = $entityManager
                    ->getRepository(Produit::class)
                    ->findAll();
      //  dd($session->get('idsession'));
            if ($request->query->getBoolean('success')) {
                $this->addFlash('success', 'Entity added successfully!');
            }

        return $this->render('front.html.twig', [
            'produits' => $produits,
        ]);
    }


    #[Route('/panier', name: 'my_route', methods: ['POST'])]
    public function index2(CommandeRepository $commandeRepository,Request $request,EntityManagerInterface $entityManager): Response
    { 
        //get id produit from hidden form
        $idproduit = $request->request->get('myVariable');
       
        //remplir la session par les id produit choisis 
        $session = new Session();


        //tableau rempli par les id_prod du panier
        $arr_panier = $session->get('panier') ;
        array_push($arr_panier , $idproduit);
        //mise a jour panier 
        $session->set('panier',$arr_panier );


        $produits = array();
       
        //recuperer les produits a parir de la base de données 
        foreach ( $session->get('panier') as $i ){
                $product =  $entityManager
                         ->getRepository(Produit::class)
                         ->find($i);     
             $produits[]= $product;
        }
       
        return $this->render('frontcart.html.twig', [
           'produits' => $produits,
        ]);
    }


    #[Route('/panier/vider', name: 'viderpanier', methods: ['get'])]
    public function viderpanier(CommandeRepository $commandeRepository,Request $request,EntityManagerInterface $entityManager): Response
    { 
        $session = new Session();
        $session->set('panier', []);
       
       
        $produits = array();
        
        return $this->render('frontcart.html.twig', [
           'produits' => $produits,
        ]);
    }

    #[Route('/newcommande', name: 'submitcommande', methods: ['POST'])]
    public function newcomande(CommandeRepository $commandeRepository,LigneDeCommandeRepository $commandeLRepository,Request $request,EntityManagerInterface $entityManager): Response
    {       
        //recuperer user statique son id 1 
        $user = $entityManager
        ->getRepository(User::class)
        ->find(1);

        $time = new \DateTime();

        $commande = new Commande();

        $commande->setEtat("checked");
        $commande->setDate($time);
        $commande->setUserId($user);
        //enregistrer la commande a l la base de donné rempli par time , user, etat
        $commandeRepository->save($commande, true);
       
        $lastComm = $entityManager
        ->getRepository(Commande::class)
        ->findOneBy([], ['id' => 'DESC']);
        
        $table = array();
        $table[] = $request->request->get('table');
        
        for ($i = 0; $i < count($table); $i++) {
           for ($j = 0; $j < count($table[$i]); $j++) {
               $quantitie= intval($table[$i][$j]['quantity']);
               $idp = intval($table[$i][$j]['idproduit']);

               $produit = $entityManager
               ->getRepository(Produit::class)
               ->find($idp);

                $LC = new LigneDeCommande();

                $LC->setIdCommande( $lastComm);
                $LC->setIdProduit($produit);
                $LC->setQuantite($quantitie);
                $commandeLRepository->save($LC, true);
               }
       }
   
       $session = new Session();
       $session->set('panier', []);


       return $this->redirectToRoute('app_commande_index', [   'success' => true], Response::HTTP_SEE_OTHER);
    }






    #[Route('/new', name: 'app_commande_new', methods: ['GET', 'POST'])]
    public function new(Request $request, CommandeRepository $commandeRepository): Response
    {
        $commande = new Commande();
        $form = $this->createForm(CommandeType::class, $commande);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $commandeRepository->save($commande, true);

            return $this->redirectToRoute('app_commande_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('commande/new.html.twig', [
            'commande' => $commande,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_commande_show', methods: ['GET'])]
    public function show(Commande $commande): Response
    {
        return $this->render('commande/show.html.twig', [
            'commande' => $commande,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_commande_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Commande $commande, CommandeRepository $commandeRepository): Response
    {
        $form = $this->createForm(CommandeType::class, $commande);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $commandeRepository->save($commande, true);

            return $this->redirectToRoute('app_commande_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('commande/edit.html.twig', [
            'commande' => $commande,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_commande_delete', methods: ['POST'])]
    public function delete(Request $request, Commande $commande, CommandeRepository $commandeRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$commande->getId(), $request->request->get('_token'))) {
            $commandeRepository->remove($commande, true);
        }

        return $this->redirectToRoute('app_commande_index', [], Response::HTTP_SEE_OTHER);
    }


}
