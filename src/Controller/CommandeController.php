<?php

namespace App\Controller;

use App\Entity\Codepromo;
use App\Entity\Produit;
use App\Entity\Commande;
use App\Entity\LigneDeCommande;
use App\Entity\User;
use App\Entity\Status;
use App\Form\CommandeType;
use App\Repository\CodepromoRepository;
use App\Repository\CommandeRepository;
use App\Repository\LigneDeCommandeRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\Session;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;


use Dompdf\Dompdf;
use Dompdf\Options;


#[Route('/commande')]
class CommandeController extends AbstractController
{

    #[Route('/admin', name: 'admin_com', methods: ['GET'])]
    public function adminHome( EntityManagerInterface $entityManager): Response
    {   
        

        $Commandes = $entityManager
                    ->getRepository(Commande::class)
                    ->findAll(['date' => 'DESC']);
        
         
          
        return $this->render('commande/admin/admin.html.twig', [
            'commandes' => $Commandes,
        ]);
    }
    #[Route('/admin/codes', name: 'admin_code', methods: ['GET'])]
    public function adminCodeHome( EntityManagerInterface $entityManager, Request $request): Response
    {   
        
        if ($request->query->getBoolean('successdelete')) {
            $this->addFlash('successdelete', 'Entity added successfully!');
        }
        if ($request->query->getBoolean('successadd')) {
            $this->addFlash('successadd', 'Entity added successfully!');
        }
        $codes = $entityManager
                    ->getRepository(Codepromo::class)
                    ->findAll(['date' => 'DESC']);
        
         
          
        return $this->render('commande/admin/codepromo.html.twig', [
            'codes' => $codes,
        ]);
    }

    #[Route('/admin/delete/{id}', name: 'deletecode', methods: ['GET'])]
    public function deletecode(Codepromo $code, CodepromoRepository $cdrepo): Response
    {   
             $cdrepo->remove($code, true);
          
        return $this->redirectToRoute('admin_code', ['successdelete' => true], Response::HTTP_SEE_OTHER);
    }


    #[Route('/admin/addCode', name: 'postcode', methods: ['POST'])]
    public function AjoutCode(Request $request, CodepromoRepository $cprepo): Response
    { 
        
        //get id produit from hidden form
        $code = $request->request->get('code');
        $valeur = $request->request->get('valeur');
       $time = new \DateTime();

        $cp =new Codepromo();
        $cp->setCode($code);
        $cp->setCreatedAt($time);
        $cp->setValeur($valeur);
        $cprepo->save($cp,true);
        
        return $this->redirectToRoute('admin_code', ['successadd' => true], Response::HTTP_SEE_OTHER);
    }



    #[Route('/admin/detail/{id}', name: 'admin_comdetail', methods: ['GET'])]
    public function detailCommande(Commande $comm, EntityManagerInterface $entityManager): Response
    {   
        $total = 0 ; 
        $LCommandes = $entityManager
                    ->getRepository(LigneDeCommande::class)
                    ->findBy(['id_commande' => $comm]);
                    foreach ($LCommandes as $l) {
                        $quantitie= $l->getQuantite();
                        $prix = $l->getIdProduit()->getPrixTtc();      
                        $total= $total + $prix * $quantitie;
                }
            $total = $total+7; 

        $Commandes = $entityManager
                    ->getRepository(Commande::class)
                    ->findAll();
        $latestCommande = $entityManager
                    ->getRepository(Commande::class)
                    ->findOneBy([], ['date' => 'DESC'], 1);
         
          
        return $this->render('commande/admin/show.html.twig', [
            'lcommandes' => $LCommandes,
            'total' => $total
        ]);
    }

    #[Route('/livred/{id}',  name: 'livred', methods: ['GET'])]
    public function LivredComande(Commande $comm, EntityManagerInterface $entityManager) : Response
    {
        $comm->setEtat(Status::LIVRED);
        $entityManager->getRepository(Commande::class)->save($comm, true);

         return $this->redirectToRoute('admin_com');
    }

    #[Route('/annule/{id}',  name: 'annule', methods: ['GET'])]
    public function AnnuleCommande(Commande $comm, EntityManagerInterface $entityManager) : Response
    {
        $comm->setEtat(Status::ANNULE);
        $entityManager->getRepository(Commande::class)->save($comm, true);

         return $this->redirectToRoute('affc');
    }

    #[Route('/pdf/{id}',  name: 'pdf', methods: ['GET'])]
    public function pdfDownload(Commande $comm, EntityManagerInterface $entityManager) : Response
    {
        $total = 0 ; 
        $LCommandes = $entityManager
                    ->getRepository(LigneDeCommande::class)
                    ->findBy(['id_commande' => $comm]);
                    foreach ($LCommandes as $l) {
                        $quantitie= $l->getQuantite();
                        $prix = $l->getIdProduit()->getPrixTtc();      
                        $total= $total + $prix * $quantitie;
                }
                $total = $total+7; 
        $dompdf = new Dompdf();
        $pdfOptions = new Options();
        $pdfOptions->set(array('isRemoteEnabled' => true));
        $dompdf = new Dompdf($pdfOptions);
        $html = $this->render('commande/datapdf.html.twig', [
            'lcommandes' => $LCommandes,
            'total'=> $total
        ]);
        // Generate the PDF
        $dompdf->loadHtml($html->getContent());
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        // Output the PDF as a string
        $pdfOutput = $dompdf->output();
                // Send the PDF as a response with a "Content-Type" header of "application/pdf"
            $dompdf->stream("commande_sporteve.pdf", [
                    "attachment" => true,
                ]); 
    $this->redirectToRoute('panier');
         return new Response();
    }

    #[Route('/affcommande', name: 'affc', methods: ['GET'])]
    public function AfficheCommandeUser( EntityManagerInterface $entityManager): Response
    {   
        $user = $entityManager
        ->getRepository(User::class)
        ->find(1);

        $Commandes = $entityManager
                    ->getRepository(Commande::class)
                    ->findBy(['user_id' => $user],['date' => 'DESC']);
        $latestCommande = $entityManager
                    ->getRepository(Commande::class)
                    ->findOneBy([], ['date' => 'DESC'], 1);
         
          
        return $this->render('frontcommande.html.twig', [
            'commandes' => $Commandes,
        ]);
    }


    #[Route('/home', name: 'app_commande_index', methods: ['GET'])]
    public function UserHome(Request $request, EntityManagerInterface $entityManager): Response
    {   
        $session = new Session();
        if( empty($session->get('panier'))){
            $session->set('panier', []);
            $session->set('cade', null);
        }   
       
        //get all products frome database 
        $produits = $entityManager
                    ->getRepository(Produit::class)
                    ->findAll();
      //  dd($session->get('idsession'));
            if ($request->query->getBoolean('success')) {
                $this->addFlash('success', 'Entity added successfully!');
            }
            if ($request->query->getBoolean('successp')) {
                $this->addFlash('successp', 'Entity added successfully!');
            }

        return $this->render('front.html.twig', [
            'produits' => $produits,
        ]);
    }


    #[Route('/panier', name: 'my_route', methods: ['POST'])]
    public function remplirPanier(Request $request): Response
    { 
        
        //get id produit from hidden form
        $idproduit = $request->request->get('myVariable');
        //remplir la session par les id produit choisis 
        $session = new Session();
        //tableau rempli par les id_prod du panier
        $arr_panier = $session->get('panier') ;
    
        $boolean= true ;
        if (! empty($arr_panier)){
                foreach ( $arr_panier as $i ){   
                        if ($i == $idproduit){
                            $boolean= false ;
                        }
                    };
        }
        if ($boolean){
            array_push($arr_panier,$idproduit); 
        } 
        else  return $this->redirectToRoute('app_commande_index', ['successp' => true], Response::HTTP_SEE_OTHER);;
            $session->set('panier',$arr_panier );

            return $this->redirectToRoute('panier');
    }
    #[Route('/panier', name: 'panier', methods: ['GET'])]
    public function AffichePanier(Request $request,EntityManagerInterface $entityManager): Response
    {   
        $session = new Session();
        $produits = array();
        $code=$session->get('code');
        
       // $bool = false;
        foreach ( $session->get('panier') as $i ){
                $product =  $entityManager
                         ->getRepository(Produit::class)
                         ->find($i);     
             $produits[]= $product;
        }
        if ($request->query->getBoolean('successE')) {
            $this->addFlash('successE', 'Entity added successfully!');
            
        }
        if ($request->query->getBoolean('successCode')) {
            $this->addFlash('successCode', 'Entity added successfully!');
            
        }
        if ($request->query->getBoolean('successC')) {
           // $bool = true;
            $this->addFlash('successC', 'Entity added successfully!');
        }
        return $this->render('frontcart.html.twig', [
           'produits' => $produits,
           'code' =>$code
           //'bo'=>$bool
        ]);
    }

   
    #[Route('/panier/vider', name: 'viderpanier', methods: ['get'])]
    public function viderPanier(): Response
    { 
        $session = new Session();
        $session->set('panier', []);
        $session->set('code',null);
        $produits = array(); 
        return $this->render('frontcart.html.twig', [
           'produits' => $produits,
        ]);
    }
#[Route('/code', name: 'code', methods: ['POST'])]
    public function VerifCode(Request $request,EntityManagerInterface $entityManager): Response
    {   
        $session = new Session();
       $codepromo = $request->request->get('codepromo'); 
        
           if ( !empty($codepromo)) {
            
              $code = $entityManager
                    ->getRepository(Codepromo::class)
                    ->findOneByCode($codepromo);

                    if ( !empty($code)) {
                        $session->set('code',$code);
                   return $this->redirectToRoute('panier', ['successCode' => true], Response::HTTP_SEE_OTHER);
                }
                 
             }else $code="";
        return $this->redirectToRoute('panier', ['successCode' => false], Response::HTTP_SEE_OTHER);
    }
    #[Route('/newcommande', name: 'submitcommande', methods: ['POST'])]
    public function newComandeUser(ValidatorInterface $validator,CommandeRepository $commandeRepository,LigneDeCommandeRepository $commandeLRepository,Request $request,EntityManagerInterface $entityManager): Response
    {     
        $session = new Session();  
           $code= $session->get('code');
           
         
          $codea = $entityManager
          ->getRepository(Codepromo::class)
          ->find($code->getId());
        //recuperer user statique son id 1 
        $user = $entityManager
        ->getRepository(User::class)
        ->find(1); 
//dd($user);
        $time = new \DateTime();

        $commande = new Commande();

        $commande->setEtat(Status::ENCOURS);
        $commande->setDate($time);
        $commande->setUserId($user);
      
        if(isset($codea) && !empty($codea)){
        // dd($code);
                    $commande->setCode($codea) ;  
                  //  dd($commande);
                  $commandeRepository->save($commande, true);     
                
        }else {
            $entityManager->persist($commande);
                    $entityManager->flush(); 
        }
        $lastComm = $entityManager
        ->getRepository(Commande::class)
        ->findOneBy([], ['id' => 'DESC']);
   
        $table = array();
        $table[] = $request->request->get('table');

        $p = 0;
        $total= 0 ; 
        // count errors 
        foreach ($table as $ligne) {
            foreach ($ligne as $l) {
               $quantitie= intval($l['quantity']);
               $idp = intval($l['idproduit']);

               $produit = $entityManager
               ->getRepository(Produit::class)
               ->find($idp);
                        $total= $total + $produit->getPrixTtc()* $quantitie;
                $LC = new LigneDeCommande();

                $LC->setIdCommande( $lastComm);
                $LC->setIdProduit($produit);
                $LC->setQuantite($quantitie);
                
                $errors = $validator->validate($LC);
              
                if (count($errors) > 0) $p++; 

               }
            }
        
        if ($p == 0) {          
            foreach ($table as $ligne) {
                foreach ($ligne as $l) {
                    $quantitie= intval($l['quantity']);
                    $idp = intval($l['idproduit']);
        
                    $produit = $entityManager
                    ->getRepository(Produit::class)
                    ->find($idp);
        
                        $LC = new LigneDeCommande();
        
                        $LC->setIdCommande($lastComm);
                        $LC->setIdProduit($produit);
                        $LC->setQuantite($quantitie);
                        
                        $commandeLRepository->save($LC, true);
                   }
                }
            $session = new Session();
            $session->set('panier', []); 
            $session->set('code', null); 
                 return $this->redirectToRoute('panier', ['successC' => true], Response::HTTP_SEE_OTHER);

        }else {
            $commandeRepository->remove($lastComm, true);
        return $this->redirectToRoute('panier', ['successE' => true], Response::HTTP_SEE_OTHER);
            }
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
