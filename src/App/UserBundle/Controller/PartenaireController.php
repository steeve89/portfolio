<?php

/*
 * This file is part of the UserBundle.
 *
 * (c) LOKO Steeve <loko.steeve@yahoo.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\UserBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use App\UserBundle\Entity\User;
use App\UserBundle\Form\UserType;
use JMS\SecurityExtraBundle\Annotation\Secure;

/**
 * User controller.
 *
 * @Route("/partenaire")
 */
class PartenaireController extends Controller
{

    /**
     * @Secure(roles="ROLE_SUPER_ADMIN")
     * 
     * Lists all User entities.
     *
     * @Route("/", name="partenaire")
     * @Method("GET")
     * @Template()
     */
    public function indexAction($page)
    {
        $em = $this->getDoctrine()->getManager();
        //  On recupère toutes les entités
        $nombreParPage = $this->container->getParameter('user');         
        $partenaires =   $em->getRepository('AppUserBundle:User')->getUsers('ROLE_PARTENAIRE', $nombreParPage, $page);
        //  Le nombre de page à afficher
        $nb_pages = (count($partenaires) > 0) ? ceil(count($partenaires)/$nombreParPage) : 1;
        //  On affiche la vue
        return array(
            'entities' => $partenaires,
            'page' => $page,
            'nb_pages' => $nb_pages,
        );
    }

    /**
     * @Secure(roles="ROLE_SUPER_ADMIN")
     * 
     * Displays a form to create a new User entity.
     *
     * @Route("/new", name="partenaire_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction(Request $request)
    {
        //  On crée une instance de la classe user
        $partenaire = new User();
        //  On déclare le formulaire de création de l'entité User
        $form   = $this->createCreateForm($partenaire);
        //  On gère la soumissoon du formulire
        if( $request->getMethod() == 'POST')
        {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                //  On active le compte
                $partenaire->setEnabled(true);
                //  On définit le rôle du partenaire
                $roles = array('ROLE_PARTENAIRE');
                $partenaire->setRoles($roles);  
                //  On persiste et on sauvegarde
                $em->persist($partenaire);
                $em->flush();
                //  On redirige vers la page de visualisation de l'entité crée
                return $this->redirect($this->generateUrl('partenaire_show', array('id' => $partenaire->getId())));
            }
        }
        //  On affiche la vue
        return array(
            'entity' => $partenaire,
            'form'   => $form->createView(),
        );
    }
    /**
    * Creates a form to create a User entity.
    *
    * @param User $partenaire The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(User $partenaire)
    {
        //  On crée un formulaire de création de partenaire
        $form = $this->createForm(new UserType('partenaire'), $partenaire, array(
            'action' => $this->generateUrl('partenaire_new'),
            'method' => 'POST',
            'attr' => array('class' => 'form-horizontal well'),
        ));
        //  On crée un bouton pour soumettre le formulaire
        $form->add('submit', 'submit', array('translation_domain' => 'FOSUserBundle', 'label' => 'actions.create', 'attr' => array( 'class' => 'btn btn-primary' )));
        //  On retourne le formulaire
        return $form;
    }

    /**
     * @Secure(roles="ROLE_SUPER_ADMIN")
     * 
     * Finds and displays a User entity.
     *
     * @Route("/{id}", name="partenaire_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction(User $partenaire)
    {
        //  Formulaire de desactivation du partenaire
        $deleteForm = $this->createDeleteForm($partenaire->getId());
        //  On affiche la vue
        return array(
            'entity'      => $partenaire,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * @Secure(roles="ROLE_SUPER_ADMIN")
     * 
     * Displays a form to edit an existing User entity.
     *
     * @Route("/{id}/edit", name="partenaire_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction(Request $request, User $partenaire)
    {
        $em = $this->getDoctrine()->getManager();
        //  On déclarre les formulaires de d'édition et de suppresion de l'entité User
        $editForm = $this->createEditForm($partenaire);
        $deleteForm = $this->createDeleteForm($partenaire->getId());
        //  On gère ici la soumission du formulaire d'édition
        if( $request->getMethod() == 'PUT')
        {
            $editForm->handleRequest($request);

            if ($editForm->isValid()) {
                //  On met à jour les informations
                $em->flush();
                //  On récupère le service translator
                $translator = $this->get('translator');
                //  On affiche un message à l'utilisateur
                $texteTraduit = $translator->trans('message.notice');
                $this->get('session')->getFlashBag()->add('notice', $texteTraduit);                
                // On fait une redirection vers la page de liste de l'entité User                
                return $this->redirect($this->generateUrl('partenaire'));
            }
        }

        //  On affiche la vue
        return array(
            'entity'      => $partenaire,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
    * Creates a form to edit a User entity.
    *
    * @param User $partenaire The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(User $partenaire)
    {
        //  On crée un formulaire de modification des informations du partenaire
        $form = $this->createForm(new UserType('partenaire'), $partenaire, array(
            'action' => $this->generateUrl('partenaire_edit', array('id' => $partenaire->getId())),
            'method' => 'PUT',
            'attr' => array('class' => 'form-horizontal well'),
        ));
        //  On crée un bouton pour soumettre le formulaire
        $form->add('submit', 'submit', array('translation_domain' => 'FOSUserBundle', 'label' => 'actions.update', 'attr' => array( 'class' => 'btn btn-primary' )));
        //  On retourne le formulaire
        return $form;
    }
    /**
     * @Secure(roles="ROLE_SUPER_ADMIN")
     * 
     * Deletes a User entity.
     *
     * @Route("/{id}", name="partenaire_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, User $partenaire)
    {
        //  On définit le formulaire de desactivaton du partenaire
        $form = $this->createDeleteForm($partenaire->getId());
        $form->handleRequest($request);
        //  On contrôle les données du formulaires
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            //$em->remove($partenaire);
            //  On bloque ou débloque l'utilisateur
            if ( $partenaire->isAccountNonLocked() )
            {
                $partenaire->setLocked(true);
            } else { 
                $partenaire->setLocked(false);
            }
            $em->persist($partenaire);
            $em->flush();
        }
        // On fait une redirection vers la page de liste de l'entité User  
        return $this->redirect($this->generateUrl('partenaire'));
    }

    /**
     * Creates a form to delete a User entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        //  On crée le formulaire de désactivation du partenaire
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('partenaire_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('translation_domain' => 'FOSUserBundle', 'label' => 'actions.lock_unlock', 'attr' => array( 'class' => 'btn btn-danger' )))
            ->getForm()
        ;
    }
}
