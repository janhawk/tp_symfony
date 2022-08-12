<?php

namespace App\Controller;

use DateTimeImmutable;
use App\Entity\Product;
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
    public function index(): Response
    {
        return $this->render('category/index.html.twig', [
            'controller_name' => 'CategoryController',
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

            // $categorys = $categoryRepository->findAll(); // récupère tous les produits en base de données
            // $categoryNames = []; // initialise un tableau pour les noms de produits
            // foreach ($categorys as $category) { // pour chaque produit récupéré
            //     $categoryNames[] = $category->getName(); // stocke le nom du produit dans le tableau
            // }
            // if (in_array($form['name']->getData(), $categoryNames)) { // vérifie qsi le nom du produit à créé n'est pas déjà utilisé en base de données
            //     $this->addFlash('danger', 'Le produit n\'a pas pu être créé : le nom de produit est déjà utilisé');
            //     return $this->redirectToRoute('admin_categorys');
            // }

            // $infoImg1 = $form['img1']->getData(); // récupère les données du champ img1 du formulaire

            // if (empty($infoImg1)) { // vérifie la présence de l'image principale dans le formulaire
            //     $this->addFlash('danger', 'Le produit n\'a pas pu être créé : l\'image principale est obligatoire mais n\'a pas été renseignée');
            //     return $this->redirectToRoute('admin_categorys');
            // }
            $infoImg1 = $form['img1']->getData();
            $extensionImg1 = $infoImg1->guessExtension(); // récupère l'extension de fichier de l'image 1
            $nomImg1 = time() . '-1.' . $extensionImg1; // crée un nom de fichier unique pour l'image 1
            $infoImg1->move($this->getParameter('category_image_dir'), $nomImg1); // télécharge le fichier dans le dossier adéquat
            $category->setImg1($nomImg1); // définit le nom de l'image à mettre ne base de données

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
    #[Route('/admin/category/update/{id}', name: 'category_update')]
    public function update(Category $category, CategoryRepository $productRepository, Request $request, ManagerRegistry $managerRegistry): Response
    {
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $categorys = $categoryRepository->findAll(); // récupère tous les produits en base de données
            $categoryNames = []; // initialise un tableau pour les noms de produits
            foreach ($categorys as $category) { // pour chaque produit récupéré
                $categoryNames[] = $category->getName(); // stocke le nom du produit dans le tableau
            }
            if (in_array($form['name']->getData(), $categoryNames)) { // vérifie qsi le nom du produit à créé n'est pas déjà utilisé en base de données
                $this->addFlash('danger', 'Le produit n\'a pas pu être modifié : le nom de produit est déjà utilisé');
                return $this->redirectToRoute('admin_categorys');
            }

            $infoImg1 = $form['img1']->getData(); // récupère les informations de l'image 1 dans le formulaire
            if ($infoImg1 !== null) { // s'il y a bien une image donnée dans le formulaire
                $oldImg1Name = $category->getImg1(); // récupère le nom de l'ancienne image
                $oldImg1Path = $this->getParameter('category_image_dir') . '/' . $oldImg1Name; // récupère le chemin de l'ancienne image 1
                if (file_exists($oldImg1Path)) {
                    unlink($oldImg1Path); // supprime l'ancienne image 1
                }
                $extensionImg1 = $infoImg1->guessExtension(); // récupère l'extension de fichier de l'image 1
                $nomImg1 = time() . '-1.' . $extensionImg1; // crée un nom de fichier unique pour l'image 1
                $infoImg1->move($this->getParameter('product_image_dir'), $nomImg1); // télécharge le fichier dans le dossier adéquat
                $category->setImg1($nomImg1); // définit le nom de l'image à mettre ne base de données
            }
            
            $slugger = new AsciiSlugger();
            $category->setSlug(strtolower($slugger->slug($form['name']->getData())));
            $manager = $managerRegistry->getManager();
            $manager->persist($category);
            $manager->flush();

            $this->addFlash('success', 'Le produit a bien été modifié');
            return $this->redirectToRoute('admin_categorys');
        }

        return $this->render('category/form.html.twig', [
            'productForm' => $form->createView()
        ]);
    }
    #[Route('/admin/category/delete/{id}', name: 'category_delete')]
    public function delete(Product $category, ManagerRegistry $managerRegistry): Response
    {
        $img1path = $this->getParameter('category_image_dir') . '/' . $category->getImg1();
        if (file_exists($img1path)) {
            unlink($img1path);
        }

        // if ($category->getImg2() !== null) {
        //     $img2path = $this->getParameter('category_image_dir') . '/' . $category->getImg2();
        //     if (file_exists($img2path)) {
        //         unlink($img2path);
        //     }
        // }
        
        // if ($category->getImg3() !== null) {
        //     $img3path = $this->getParameter('category_image_dir') . '/' . $category->getImg3();
        //     if (file_exists($img3path)) {
        //         unlink($img3path);
        //     }
        // }

        $manager = $managerRegistry->getManager();
        $manager->remove($category);
        $manager->flush();

        $this->addFlash('success', 'Le category a bein été supprimé');
        return $this->redirectToRoute('admin_categorys');
    }
}
