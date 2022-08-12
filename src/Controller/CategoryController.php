<?php

namespace App\Controller;

use DateTimeImmutable;
use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CategoryController extends AbstractController
{
    #[Route('/category', name: 'categorys')]
    public function index(CategoryRepository $categoryRepository): Response
    {
        $categorys = $categoryRepository->findAll();
        return $this->render('category/index.html.twig', [
            'categorys' => $categorys,
        ]);
    }

    #[Route('/category/{slug}', name: 'category_show')]
    public function show($slug, CategoryRepository $categoryRepository): Response
    {
        $category = $categoryRepository->findOneBy(['slug' => $slug]);
        return $this->render('category/show.html.twig', [
            'category' => $category
        ]);
    }

    #[Route('/admin/categorys', name: 'admin_categorys')]
    public function adminList(CategoryRepository $categoryRepository): Response
    {
        $categorys = $categoryRepository->findAll();
        return $this->render('category/adminList.html.twig', [
            'categorys' => $categorys
        ]);
    }

    #[Route('/admin/category/create', name: 'category_create')]
    public function create(Request $request, CategoryRepository $categoryRepository, ManagerRegistry $managerRegistry): Response
    {
        $category = new Category(); // création d'un nouveau produit
        $form = $this->createForm(CategoryType::class, $category); // création d'un formulaire avec en paramètre le nouveau produit
        $form->handleRequest($request); // gestionnaire de requêtes HTTP

        if ($form->isSubmitted() && $form->isValid()) { // vérifie si le formulaire a été soumis et est valide

            $categorys = $categoryRepository->findAll(); // récupère tous les produits en base de données
            $categoryNames = []; // initialise un tableau pour les noms de produits
            foreach ($categorys as $category) { // pour chaque produit récupéré
                $categoryNames[] = $category->getName(); // stocke le nom du produit dans le tableau
            }
            if (in_array($form['name']->getData(), $categoryNames)) { // vérifie qsi le nom du produit à créé n'est pas déjà utilisé en base de données
                $this->addFlash('danger', 'Le produit n\'a pas pu être créé : le nom de produit est déjà utilisé');
                return $this->redirectToRoute('admin_categorys');
            }

            $infoImg1 = $form['img1']->getData(); // récupère les données du champ img1 du formulaire

            if (empty($infoImg1)) { // vérifie la présence de l'image principale dans le formulaire
                $this->addFlash('danger', 'Le produit n\'a pas pu être créé : l\'image principale est obligatoire mais n\'a pas été renseignée');
                return $this->redirectToRoute('admin_categorys');
            }

            $extensionImg1 = $infoImg1->guessExtension(); // récupère l'extension de fichier de l'image 1
            $nomImg1 = time() . '-1.' . $extensionImg1; // crée un nom de fichier unique pour l'image 1
            $infoImg1->move($this->getParameter('category_image_dir'), $nomImg1); // télécharge le fichier dans le dossier adéquat
            $category->setImg1($nomImg1); // définit le nom de l'image à mettre ne base de données

            $infoImg2 = $form['img2']->getData();
            if ($infoImg2 !== null) {
                $extensionImg2 = $infoImg2->guessExtension();
                $nomImg2 = time() . '-2.' . $extensionImg2;
                $infoImg2->move($this->getParameter('category_image_dir'), $nomImg2);
                $category->setImg2($nomImg2);
            }

            $infoImg3 = $form['img3']->getData();
            if ($infoImg3 !== null) {
                $extensionImg3 = $infoImg3->guessExtension();
                $nomImg3 = time() . '-3.' . $extensionImg3;
                $infoImg3->move($this->getParameter('category_image_dir'), $nomImg3);
                $category->setImg3($nomImg3);
            }

            $slugger = new AsciiSlugger();
            $category->setSlug(strtolower($slugger->slug($form['name']->getData()))); // génère un slug à partir du titre renseigné dans le formulaire
            $category->setCreatedAt(new DateTimeImmutable());

            $manager = $managerRegistry->getManager();
            $manager->persist($category);
            $manager->flush();

            $this->addFlash('success', 'Le produit a bien été créé'); // message de succès
            return $this->redirectToRoute('admin_categorys');
        }

        return $this->render('category/form.html.twig', [
            'categoryForm' => $form->createView()
        ]);
    }
   
}
