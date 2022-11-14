<?php
namespace App\Controller;

use App\Entity\Category;
use App\Entity\Livre;
use App\Form\LivreType;
use App\Repository\CategoryRepository;
use App\Repository\LivreRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class LivreController extends AbstractController
{
    /**
     * @Route("/showlivres", name="livres")
     */
    public function index(ManagerRegistry $doctrine)
    {   
        $repo = $doctrine->getRepository(Livre::class);
        $livres = $repo->findAll();
        return $this->render('livre/index.html.twig', [
            "livres"=>$livres
        ]);
    }

    /**
     * @Route("/addlivre", name="addlivre")
     * @Route("/modifierlivre/{id}", name="modifierlivre")
     */
    public function add(ManagerRegistry $doctrine, Request $request, UserInterface $user, Livre $livre = null)
    {   
        if(!$livre){
            $livre = new Livre();
        }
        $form = $this->createForm(LivreType::class, $livre);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $livre->setUser($user);
            $om = $doctrine->getManager();
            $om->persist($livre);
            $om->flush();
            return $this->redirectToRoute("livres");
        }

        return $this->render('livre/add.html.twig', [
            "formLivre"=>$form->createView(),
            "id"=>$livre->getId()
        ]);
    }

    /**
     * @Route("/removelivre/{id}", name="removelivre")
     */
    public function remove(ManagerRegistry $doctrine, Livre $livre)
    {   
        $om = $doctrine->getManager();
        $om->remove($livre);
        $om->flush();
        return $this->redirectToRoute("livres");
    }

    /**
     * @Route("/livresCategory/{id}", name="livresCategory")
     */
    public function findLivresByCategory(ManagerRegistry $doctrine, $id)
    {   
        $repo = $doctrine->getRepository(Category::class);
        $livres = $repo->findLivresByCategory($id);
        return $this->render('livre/livresCategory.html.twig', [
            "livres"=>$livres
        ]);
    }
}
