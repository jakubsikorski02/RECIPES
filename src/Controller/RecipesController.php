<?php

namespace App\Controller;

use App\Entity\Recipe;
use App\Entity\Review;
use App\Repository\RecipeRepository;
use App\Form\RecipeFormType;
use App\Repository\ReviewRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Validator\Constraints\Valid;

class RecipesController extends AbstractController
{
    private $em;
    private $recipeRepository;
    private $security;

    public function __construct(RecipeRepository $recipeRepository, EntityManagerInterface $em, Security $security, ReviewRepository $reviewRepository)
    {
        $this->recipeRepository = $recipeRepository;
        $this->reviewRepository = $reviewRepository;
        $this->security = $security;
        $this->em = $em;
    }


    #[Route('/recipes', methods: ['GET'], name: 'recipes')]
    public function index(): Response
    {
        $recipes = $this->recipeRepository->findAll();

        return $this->render('recipes/index.html.twig', [
            'recipes' => $recipes
        ]);
    }

    #[Route('/recipes/contact', name: 'contact')]
    public function contact(): Response
    {

        return $this->render('recipes/contact.html.twig');
    }


    #[Route('/recipes/settings', name: 'settings')]
    public function settings(): Response
    {

        return $this->render('recipes/settings.html.twig');
    }



    #[Route('/recipes/my_recipes', methods: ['GET'], name: 'my_recipes')]
    public function myRecipes(): Response
    {
        $user = $this->security->getUser();

        $recipes = $user->getRecipes();

        return $this->render('recipes/my_recipes.html.twig', [
            'recipes' => $recipes
        ]);
    }


    #[Route('/recipes/create', name: 'create_recipe')]
    public function create(Request $request): Response
    {
        $recipe = new Recipe();
        $form = $this->createForm(RecipeFormType::class, $recipe);
        $user = $this->security->getUser();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $newRecipe = $form->getData();

            $imagePath = $form->get('imagePath')->getData();
            if ($imagePath) {
                $newFileName = uniqid() . '.' . $imagePath->guessExtension();

                try {
                    $imagePath->move(
                        $this->getParameter('kernel.project_dir') . '/public/uploads',
                        $newFileName
                    );
                } catch (FileException $e) {
                    return new Response($e->getMessage());
                }

                $newRecipe->setImagePath('/uploads/' . $newFileName);
            }

            $this->em->persist($newRecipe);
            $newRecipe->addUser($user);
            $this->em->flush();
            return $this->redirectToRoute('recipes');
        }

        return $this->render('recipes/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/recipes/saved', name: 'saved')]
    public function saved(): Response
    {
        $user = $this->security->getUser();
        $reviews = $this->reviewRepository->findAll();
        $savedRecipes = [];

        foreach ($reviews as $review) {
            $reviewUsers = $review->getUser();
            $reviewRecipes = $review->getRecipe();

            foreach ($reviewUsers as $reviewUser) {
                foreach ($reviewRecipes as $reviewRecipe) {
                    if ($reviewUser === $user) {
                        $savedRecipes[] = $reviewRecipe;
                    }
                }
            }
        }


        return $this->render('recipes/saved.html.twig', [
            'savedRecipes' => $savedRecipes,
        ]);
    }

    #[Route('/recipes/{id}', methods: ['GET'], name: 'show_recipe')]
    public function showRecipe($id): Response
    {
        $recipe = $this->recipeRepository->find($id);
        $author = $recipe->getUser()->first();
        $user = $this->security->getUser();
        $reviews = $this->reviewRepository->findAll();

        
        $matchingReviews = [];

        foreach ($reviews as $review) {
            $reviewRecipes = $review->getRecipe();
            

            foreach ($reviewRecipes as $reviewRecipe) {
                if ($reviewRecipe === $recipe) {
                    $matchingReviews[] = $review;
                }
            }
        }
        
        $isSaved = false;
        
        foreach ($reviews as $review) {
            $reviewUsers = $review->getUser();
            $reviewRecipes = $review->getRecipe();
    
            foreach ($reviewUsers as $reviewUser) {
                foreach ($reviewRecipes as $reviewRecipe) {
                    if ($reviewUser === $user && $reviewRecipe === $recipe) {
                        $isSaved = true;
                    }
                }
            }
        }
        
        return $this->render('recipes/show_recipe.html.twig', [
            'recipe' => $recipe,
            'isSaved' => $isSaved,
            'author' => $author,
            'reviews' => $matchingReviews
        ]);
    }

    #[Route('/recipes/my_recipes/edit/{id}', name: 'edit')]
    public function edit($id, Request $request): Response
    {
        $recipe = $this->recipeRepository->find($id);
        $form = $this->createForm(RecipeFormType::class, $recipe);

        $form->handleRequest($request);
        $imagePath = $form->get('imagePath')->getData();

        if ($form->isSubmitted() && $form->isValid()) {
            if ($imagePath) {
                if ($recipe->getImagePath() !== null) {
                    if (file_exists(
                        $this->getParameter('kernel.project_dir') . $recipe->getImagePath()
                    )) {
                        $this->GetParameter('kernel.project_dir') . $recipe->getImagePath();
                    }
                    $newFileName = uniqid() . '.' . $imagePath->guessExtension();

                    try {
                        $imagePath->move(
                            $this->getParameter('kernel.project_dir') . '/public/uploads',
                            $newFileName
                        );
                    } catch (FileException $e) {
                        return new Response($e->getMessage());
                    }

                    $recipe->setImagePath('/uploads/' . $newFileName);
                    $this->em->flush();


                    return $this->redirectToRoute('recipes');
                }
            } else {
                $recipe->setName($form->get('name')->getData());
                $recipe->setIngridients($form->get('ingridients')->getData());
                $recipe->setHowTo($form->get('howTo')->getData());

                $this->em->flush();

                return $this->redirectToRoute('recipes');
            }
        }

        return $this->render('recipes/edit.html.twig', [
            'recipe' => $recipe,
            'form' => $form->createView()
        ]);
    }


    #[Route('/recipes/my_recipes/delete/{id}', methods: ['GET', 'DELETE'], name: 'delete')]
    public function delete($id): Response
    {
        $recipe = $this->recipeRepository->find($id);
        $this->em->remove($recipe);
        $this->em->flush();

        return $this->redirectToRoute('recipes');
    }

    #[Route('/recipes/save/{id}', methods: ['GET'], name: 'save')]
    public function save($id): Response
    {
        $recipe = $this->recipeRepository->find($id);
        $user = $this->security->getUser();
        $reviews = $this->reviewRepository->findAll();

        foreach ($reviews as $review) {
            $reviewUsers = $review->getUser();
            $reviewRecipes = $review->getRecipe();

            foreach ($reviewUsers as $reviewUser) {
                foreach ($reviewRecipes as $reviewRecipe) {
                    if ($reviewUser === $user && $reviewRecipe === $recipe) {
                        return $this->redirectToRoute('show_recipe', ['id' => $id]);
                    }
                }
            }
        }

        $newReview = new Review();
        $this->em->persist($newReview);
        $newReview->addUser($user);
        $newReview->addRecipe($recipe);
        $this->em->flush();

        return $this->redirectToRoute('show_recipe', ['id' => $id]);
    }


    #[Route('/recipes/my_recipes/{id}', methods: ['GET'], name: 'show')]
    public function show($id): Response
    {
        $recipe = $this->recipeRepository->find($id);

        return $this->render('recipes/show.html.twig', [
            'recipe' => $recipe
        ]);
    }
}
