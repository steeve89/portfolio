<?php

/*
 * This file is part of the PortfolioBundle.
 *
 * (c) LOKO Steeve <loko.steeve@yahoo.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\PortfolioBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use App\PortfolioBundle\Entity\Projet;
use App\PortfolioBundle\AppPortfolioEvents;
use App\PortfolioBundle\Event\FilterProjetEvent;
use JMS\SecurityExtraBundle\Annotation\Secure;

/**
 * Projet controller.
 *
 * @Route("/projet")
 */
class ProjetController extends Controller
{
    /**
     * @Secure(roles="ROLE_CLIENT")
     * 
     * Lists all Projet entities.
     *
     * @Route("/", name="projet")
     * @Method("GET")
     * @Template()
     */
    public function indexAction($page)
    {
        $em = $this->getDoctrine()->getManager();
        //  On recupère toutes les entités
        $nombreParPage = $this->container->getParameter('projet');      
        if( $this->get('security.context')->isGranted('ROLE_ADMIN') ):
            $projets =   $em->getRepository('AppPortfolioBundle:Projet')->getProjets($nombreParPage, $page);
        else:
            $projets =   $em->getRepository('AppPortfolioBundle:Projet')->getProjets($nombreParPage, $page, $this->getUser());
        endif;        
        //  Le nombre de page à afficher
        $nb_pages = (count($projets) > 0) ? ceil(count($projets)/$nombreParPage) : 1;
        //  On affiche la vue
        return array(
            'entities' => $projets,
            'page' => $page,
            'nb_pages' => $nb_pages,
        );
    }

    /**
     * @Secure(roles="ROLE_CLIENT")
     * 
     * Displays a form to create a new Projet entity.
     *
     * @Route("/new", name="projet_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction(Request $request)
    {
        //  On crée une instance de la classe Projet
        $projet = new Projet();
        //  On déclare le formulaire de création de l'entité Projet
        $form   = $this->createCreateForm($projet);
        //  On gère la soumissoon du formulire
        if( $request->getMethod() == 'POST')
        {
            $form->handleRequest($request);

            if ($form->isValid()) {
                //  On définie l'auteur du projet
                $user = $this->getUser();
                $projet->setUser($user);
                //  On enregstre le projet
                $em = $this->getDoctrine()->getManager();
                $em->persist($projet);
                $em->flush();
                // On crée l'évènement FilterProjetEvent avec son argument
                $event = new FilterProjetEvent($projet);
                // On déclenche l'évènement
                $this->get('event_dispatcher')->dispatch(AppPortfolioEvents::onProjetPost, $event);
                //  On redirige vers la page de visualisation de l'entité crée
                return $this->redirect($this->generateUrl('projet_show', array('slug' => $projet->getSlug())));
            }
        }
        //  On affiche la vue
        return array(
            'entity' => $projet,
            'form'   => $form->createView(),
        );
    }
    /**
    * Creates a form to create a Projet entity.
    *
    * @param Projet $projet The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(Projet $projet)
    {
        //  On crée le formulaire de création de projet
        $form = $this->createForm('projet', $projet, array(
            'action' => $this->generateUrl('projet_new'),
            'method' => 'POST',
            'attr' => array('class' => 'form-horizontal well'),
        ));
        //  on créer un bouton pour la soumission du formulaire
        $form->add('submit', 'submit', array('translation_domain' => 'FOSUserBundle', 'label' => 'actions.create', 'attr' => array( 'class' => 'btn btn-primary' )));
        //  On retourne le formulaire
        return $form;
    }

    /**
     * @Secure(roles="ROLE_CLIENT")
     * 
     * Finds and displays a Projet entity.
     *
     * @Route("/{id}", name="projet_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction(Projet $projet)
    {
        //  On vérifie que l'utilisateur a les droits necessaires
        if( ! $this->isAuthorised( $projet->getUser()->getId() ) )
        {        
            return $this->redirect($this->generateUrl('projet'));
        }
        //  Formulaire de suppresion du projet
        $deleteForm = $this->createDeleteForm($projet->getSlug());
        //  On affiche la vue
        return array(
            'entity'      => $projet,
            'delete_form' => $deleteForm->createView(),
        );
    }
    
    /**
     * @Secure(roles="ROLE_CLIENT")
     * 
     * Displays a form to edit an existing Projet entity.
     *
     * @Route("/{id}/edit", name="projet_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction(Request $request, Projet $projet)
    {
        //  On vérifie que l'utilisateur a les droits necessaires
        if( ! $this->isAuthorised( $projet->getUser()->getId() ) )
        {        
            return $this->redirect($this->generateUrl('projet'));
        }
        //  On recupère le doctrine manager
        $em = $this->getDoctrine()->getManager();
        //  On déclarre les formulaires de d'édition et de suppresion de l'entité Projet
        $editForm = $this->createEditForm($projet);
        $deleteForm = $this->createDeleteForm($projet->getSlug());
        // On crée une copie de la liste des pièces jointes
        $originalPieceJointes = array();
        foreach ($projet->getPieceJointes() as $pieceJointe) $originalPieceJointes[] = $pieceJointe;
        //  On gère ici la soumission du formulaire d'édition
        if( $request->getMethod() == 'PUT')
        {
            $editForm->handleRequest($request);

            if ($editForm->isValid()) {
                //  On recupère la liste des pièces jointes supprmées
                foreach ($projet->getPieceJointes() as $pieceJointe) {
                    foreach ($originalPieceJointes as $key => $toDel) {
                        if ($toDel->getId() === $pieceJointe->getId()) {
                            unset($originalPieceJointes[$key]);
                        }
                    }
                }
                // On supprime les pieces jointes
                foreach ($originalPieceJointes as $pieceJointe) {
                    $em->remove($pieceJointe);
                }
                //  On sauvegarde
                $em->flush();
                //  On récupère le service translator
                $translator = $this->get('translator');
                //  On affiche un message à l'utilisateur
                $texteTraduit = $translator->trans('message.notice');
                $this->get('session')->getFlashBag()->add('notice', $texteTraduit);
                
                // On fait une redirection vers la page de liste de l'entité Projet                
                return $this->redirect($this->generateUrl('projet'));
            }
        }

        //  On affiche la vue
        return array(
            'entity'      => $projet,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
    * Creates a form to edit a Projet entity.
    *
    * @param Projet $projet The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Projet $projet)
    {
        //  On crée le formulaire d'édition du projet
        $form = $this->createForm('projet', $projet, array(
            'action' => $this->generateUrl('projet_edit', array('slug' => $projet->getSlug())),
            'method' => 'PUT',
            'attr' => array('class' => 'form-horizontal well'),
        ));
        //  on crée un bouton our la soumission du formulaire
        $form->add('submit', 'submit', array('translation_domain' => 'FOSUserBundle', 'label' => 'actions.update', 'attr' => array( 'class' => 'btn btn-primary' )));
        //  On retourne le formulaire
        return $form;
    }
    /**
     * @Secure(roles="ROLE_CLIENT")
     * 
     * Deletes a Projet entity.
     *
     * @Route("/{id}", name="projet_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Projet $projet)
    {
        //  On vérifie que l'utilisateur a les droits necessaires
        if( ! $this->isAuthorised( $projet->getUser()->getId() ) )
        {        
            return $this->redirect($this->generateUrl('projet'));
        }
        //  On crée le formulaire de suppression de l'entité
        $form = $this->createDeleteForm($projet->getSlug());
        $form->handleRequest($request);
        //  On vérifie que le formulare est valde
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            //  On supprime le projet ou on le désactive
            if( $this->get('security.context')->isGranted('ROLE_ADMIN') ):
                $em->remove($projet);
            else:
                $projet->setIsActived(false);
            endif;
            //  On sauvegarde
            $em->flush();
            //  On récupère le service translator
            $translator = $this->get('translator');
            //  On affiche un message à l'utilisateur
            $texteTraduit = $translator->trans('message.delete');
            $this->get('session')->getFlashBag()->add('notice', $texteTraduit);
        }

        // On fait une redirection vers la page de liste de l'entité Projet  
        return $this->redirect($this->generateUrl('projet'));
    }

    /**
     * Creates a form to delete a Projet entity by slug.
     *
     * @param mixed $slug The entity slug
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($slug)
    {
        //  On crée le formulaire de suppression du projet
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('projet_delete', array('slug' => $slug)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('translation_domain' => 'FOSUserBundle', 'label' => 'actions.delete', 'attr' => array( 'class' => 'btn btn-danger' )))
            ->getForm()
        ;
    }
    
    /**
     * Es-il autorisé à faire cette action
     */
    public function isAuthorised($id){
    //  L'administrateur a tous les droits
        if( $this->get('security.context')->isGranted('ROLE_ADMIN') ):
            return true;
        endif;
    //  On vérfie que l'utilisateur est bien propriétaire de ce projet
        if( $this->getUser()->getId() != $id ):
            //  On récupère le service translator
            $translator = $this->get('translator');
            //  On affiche un message à l'utilisateur
            $texteTraduit = $translator->trans('message.notAuthorized', array(), 'FOSUserBundle');
            $this->get('session')->getFlashBag()->add('error', $texteTraduit);
            return false;
        endif;
        
        return true;
    }
}
