<?php

/*
 * This file is part of the BlogBundle.
 *
 * (c) LOKO Steeve <loko.steeve@yahoo.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use App\BlogBundle\Entity\Commentaire;
use App\BlogBundle\AppBlogEvents;
use App\BlogBundle\Event\FilterCommentaireEvent;
use App\PortfolioBundle\Form\ContactType;
use App\PortfolioBundle\Form\ContactHandler;
use App\BlogBundle\Form\SearchFormType;

class BlogController extends Controller
{
    /**
     * Page Acuueil du Blog
     * 
     */
    public function indexAction()
    {
        //  On recupère le locale
        $langue = $this->getRequest()->getLocale();
         //  On recupère la liste des categories de ce locale
        $em = $this->getDoctrine()->getManager();       
        $categories =   $em->getRepository('AppBlogBundle:Categorie')->getBlogCategories($langue);
        //  On recupère un nombre définie d'article pour chaque catégorie
        $articles = array();
        $nombreArticleParPage = $this->container->getParameter('blog_accueil_article_par_categorie');  
        foreach( $categories as $categorie )
        {
            $articles[] = $this->getNbArticles($categorie->getArticles(), $nombreArticleParPage);
        }
        //  On affiche la vue
        return $this->render('AppBlogBundle:Blog:index.html.twig', array( 'categories' => $categories, 'articles' => $articles));
    }
    
    /**
     * Menu du Blog 
     *
     */
    public function menuAction()
    {
        //  On recupère le locale
        $langue = $this->getRequest()->getLocale();
        //  On recupère la liste des categories de ce locale
        $em = $this->getDoctrine()->getManager();
        $categories =   $em->getRepository('AppBlogBundle:Categorie')->getBlogCategories($langue);
        //  On retourne la vue
        return $this->render('AppBlogBundle:Blog:menu.html.twig', array('categories' => $categories));
    }
    
    /**
     * Sidebar du Blog
     * 
     */
    public function sidebarAction()
    {        
        //  On recupère le formulaire de recherche 
        $searchForm = $this->createForm(new SearchFormType(), null, array(
                'action' => $this->generateUrl('app_blog_rechercher'),
                'method' => 'POST',
                'attr' => array('class' => 'form-search'),
            )
        );
        //  On retourne la vue
        return $this->render('AppBlogBundle:Blog:sidebar.html.twig', array('form' => $searchForm->createView()));
    }
    
    /**
     * Sidebar du Blog
     * 
     */
    public function searchAction( Request $request )
    {        
        //  On recupère le formulaire de recherche 
        $searchForm = $this->createForm(new SearchFormType(), null, array(
                'action' => $this->generateUrl('app_blog_rechercher'),
                'method' => 'POST',
                'attr' => array('class' => 'form-search'),
            )
        );
        $word = "";
        $articles = "";
        if( $request->getMethod() == 'POST' )
        {
            $searchForm->handleRequest($request);
            if( $searchForm->isValid() )
            {
                $data = $searchForm->getData();
                $word = $data["search"];
                //  On recherche les articles associés à ce mot
                $em = $this->getDoctrine()->getManager(); 
                $articles = $em->getRepository('AppBlogBundle:Article')->getArticlesByWord($word);
                //var_dump($articles);
            }
        }
        //  On retourne la vue
        return $this->render('AppBlogBundle:Blog:search.html.twig', array(
                'form' => $searchForm->createView(), 
                'word' => $word,
                'articles' => $articles,
            )
        );
    }
    
    /**
     * Page Contact du Blog
     * 
     */
    public function contactAction()
    {
        //  On définit le formulaire de contact
        $form = $this->createForm(new ContactType());
        //  On définit la classe pour gérer les informations saisies dans le formulaire
        $formHandler = new ContactHandler($form, $this->getRequest(), $this->container);
        //  On exécute le traitement du formulaire. S'il retourne true, alors le formulaire a bien été traité
        if( $formHandler->process() )
        {       
            //  On fait une redirection vers la page d'accueil
            return $this->redirect($this->generateUrl('app_blog_homepage'));
        }
        //  On affiche la vue
        return $this->render('AppBlogBundle:Blog:contact.html.twig', array( 'form' => $form->createView() ));
    }
    
    /**
     * Permet d'afficher une catégorie d'article du Blog
     * 
     * @param type $categorie: la valeur de la propriété slug de l'entité Categorie
     * 
     */
    public function categorieAction($categorie)
    {
        //  On recupère la catégorie
        $em = $this->getDoctrine()->getManager();       
        $categorie =   $em->getRepository('AppBlogBundle:Categorie')->getCategorie($categorie);
        //  Si la catégorie n'existe pas, on redirige vers la page d'accueil du blog
        if (!$categorie)
        {
            return $this->redirect($this->generateUrl('app_blog_homepage'));
        }
        return $this->render('AppBlogBundle:Blog:categorie.html.twig', array( 'categorie' => $categorie ));
    }
    
    /**
     * Permet d'afficher un article
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param type $categorie : la valeur de la propriété slug de l'entité categorie
     * @param type $article: la valeur de la propriété slug de l'entité Article
     * 
     */
    public function articleAction(Request $request, $categorie, $article)
    {
        //  On recupère la catégorie
        $em = $this->getDoctrine()->getManager();       
        $categorie =   $em->getRepository('AppBlogBundle:Categorie')->getCategorie($categorie);
        //  Si la catégorie n'existe pas, on redirige vers la page d'accueil du blog
        if (!$categorie)
        {
            return $this->redirect($this->generateUrl('app_blog_homepage'));
        }
        //  On recupère le formulaire d'ajout de commentaire
        $newCommentaireAction = $this->newCommentaireAction($request, $article);
        //  On recupère l'article et ses commentaires
        $article =   $em->getRepository('AppBlogBundle:Article')->getArticleBySlug($article);
        if (!$article)
        {
            return $this->redirect($this->generateUrl('app_blog_categorie', array('categorie' => $categorie->getSlug())));
        }
        //  On affiche la vue
        return $this->render('AppBlogBundle:Blog:article.html.twig', array( 
                'categorie' => $categorie, 
                'article' => $article,
                'form'   => $newCommentaireAction['form'],
            )
        );
    }
    
    /**
     * Permet de retourner un nombre précis d'article à partir d'une collection d'article
     * 
     * @param array $articles : contient une collection d'article
     * @param interger $nb : Le nombre d'article à retourner 
     * @return array: Retourne une collection d'article contenant $nb article
     */
    public function getNbArticles( $articles, $nb)
    {
        //  Si le nombre d'article de cette catégorie est inférieur au nombre voulue
        //  On retourne ces articles
        if( count($articles) <= $nb ) return $articles;
        //  On retourne la nouvelle liste d'article
        $newArticles = array();
        for( $i = 0; $i < $nb; $i++)
        {
            $newArticles[] = $articles[$i];
        }
        //  On retourne la nouvelle liste d'article
        return $newArticles;
    }
    
    /**
     * 
     * Displays a form to create a new Commentaire entity.
     *
     */
    public function newCommentaireAction(Request $request, $article)
    {
        //  On crée une instance de cla classe
        $commentaire = new Commentaire();
        //  S'il s'agit d'un utilisateur connecté, 
        //  on fournit le formulaire de création de commentaire
        $user = $this->getUser();
        if($user) 
        {
            $commentaire->setNom($user->getNom());
            $commentaire->setPrenom($user->getPrenom());
            $commentaire->setEmail($user->getEmail());
        }
        //  On déclare le formulaire de création de l'entité Commentaire
        $form   = $this->createCreateCommentaireForm($commentaire);
        //  On gère la soumissoon du formulire
        if( $request->getMethod() == 'POST')
        {
            $form->handleRequest($request);

            if ($form->isValid()) {
                //  On enregistre l'entité
                $em = $this->getDoctrine()->getManager();
                //  O définit le status de l(utilisateur, membre ou admin
                if ( $this->get('security.context')->isGranted('ROLE_ADMIN') )
                { 
                    $commentaire->setIsAdmin(true);
                }
                $article =   $em->getRepository('AppBlogBundle:Article')->findOneBySlug($article);
                $commentaire->setArticle($article);
                $em->persist($commentaire);
                $em->flush();
                // On crée l'évènement FilterCommentaireEvent avec son argument
                $event = new FilterCommentaireEvent($commentaire);
                // On déclenche l'évènement
                $this->get('event_dispatcher')->dispatch(AppBlogEvents::onCommentairePost, $event);
                //  On récupère le service translator
                $translator = $this->get('translator');
                //  On affiche un message à l'utilisateur
                $texteTraduit = $translator->trans('commentaire.index.add', array(), 'FOSUserBundle');
                $this->get('session')->getFlashBag()->add('notice', $texteTraduit);
            }
        }
        //  On affiche la vue
        return array(
            'entity' => $commentaire,
            'form'   => $form->createView(),
        );
    }
    /**
    * 
    * Creates a form to create a Commentaire entity.
    *
    * @param Commentaire $commentaire The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateCommentaireForm(Commentaire $commentaire)
    {
        //  On crée un formulaire de crétion de l'entité
        $form = $this->createForm('commentaire', $commentaire, array(
            'action' => '',
            'method' => 'POST',
            'attr' => array('class' => 'form-horizontal well'),
        ));
        //  On crée un formulaire pour la soumission du formulaire
        $form->add('submit', 'submit', array('translation_domain' => 'FOSUserBundle', 'label' => 'actions.create', 'attr' => array( 'class' => 'btn btn-primary' )));
        //  On retourne le formulaire
        return $form;
    }
}
