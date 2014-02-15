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
use App\PortfolioBundle\Entity\Message;
use App\PortfolioBundle\Form\MessageType;
use App\PortfolioBundle\Entity\Projet;
use App\PortfolioBundle\AppPortfolioEvents;
use App\PortfolioBundle\Event\FilterMessageEvent;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

/**
 * Message controller.
 *
 * @Route("/message")
 */
class MessageController extends Controller
{

    /**
     * @Secure(roles="ROLE_CLIENT")
     * 
     * Lists all Message entities.
     *
     * @Route("/", name="message")
     * @Method("GET")
     * @Template()
     */
    public function indexAction(Request $request, Projet $projet, $page)
    {
        //  On vérifie que l'utilisateur a les droits necessaires
        if( ! $this->isAuthorised( $projet->getUser()->getId() ) )
        {        
            return $this->redirect($this->generateUrl('projet'));
        }
        //  On recupère le formulaire d'ajout de message
        $newAction = $this->newAction($request, $projet);
        //  On recupère doctrine Manager
        $em = $this->getDoctrine()->getManager();
        //  On recupère toutes les entités
        $nombreParPage = $this->container->getParameter('message');         
        $messages =   $em->getRepository('AppPortfolioBundle:Message')->getMessages($nombreParPage, $page, $projet);
        //  Le nombre de page à afficher
        $nb_pages = (count($messages) > 0) ? ceil(count($messages)/$nombreParPage) : 1;
        //  On affiche la vue
        return array(
            'entities' => $messages,
            'projet' => $projet,
            'page' => $page,
            'nb_pages' => $nb_pages,
            'form'   => $newAction['form'],
        );
    }

    /**
     * Displays a form to create a new Message entity.
     *
     */
    public function newAction(Request $request, Projet $projet)
    {
        //  On vérifie que l'utilisateur a les droits necessaires
        if( ! $this->isAuthorised( $projet->getUser()->getId() ) )
        {        
            return $this->redirect($this->generateUrl('projet'));
        }
        //  On crée une instance de la classe Message
        $message = new Message();
        //  On déclare le formulaire de création de l'entité Message
        $form   = $this->createCreateForm($message);
        //  On gère la soumissoon du formulire
        if( $request->getMethod() == 'POST')
        {
            $form->handleRequest($request);

            if ($form->isValid()) {                
                $em = $this->getDoctrine()->getManager();
                //  On définie l'auteur du message et le projet associé
                $user = $this->getUser();
                $message->setUser($user);
                $message->setProjet($projet);
                //  On enregistre le message
                $em->persist($message);
                $em->flush();
                // On crée l'évènement FilterMessageEvent avec son 2 argument
                $event = new FilterMessageEvent($message);
                // On déclenche l'évènement
                $this->get('event_dispatcher')->dispatch(AppPortfolioEvents::onMessagePost, $event);
                //  On récupère le service translator
                $translator = $this->get('translator');
                //  On affiche un message à l'utilisateur
                $texteTraduit = $translator->trans('rapport.index.add', array(), 'FOSUserBundle');
                $this->get('session')->getFlashBag()->add('notice', $texteTraduit);
            }
        }
        //  On affiche la vue
        return array(
            'entity' => $message,
            'form'   => $form->createView(),
        );
    }
    /**
    * Creates a form to create a Message entity.
    *
    * @param Message $message The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(Message $message)
    {
        //  On définit le formulaire de création de formulaire
        $form = $this->createForm(new MessageType(), $message, array(
            'method' => 'POST',
            'attr' => array('class' => 'form-horizontal well'),
        ));
        //  On crée un bouton pour la soumission de ce formulaire
        $form->add('submit', 'submit', array('translation_domain' => 'FOSUserBundle', 'label' => 'actions.create', 'attr' => array( 'class' => 'btn btn-primary' )));
        //  On retourne le formulaire
        return $form;
    }
    
    /**
     * @Secure(roles="ROLE_CLIENT")
     * 
     * Displays a form to edit an existing Message entity.
     *
     * @Route("/{id}/edit", name="message_edit")
     * @Method("GET")
     * @Template()
     * 
     * @ParamConverter("message", options={"mapping": {"id": "id"}} )
     * @ParamConverter("projet", options={"mapping": {"slug": "slug"}} )
     */
    public function editAction(Request $request, Message $message, Projet $projet)
    {
        //  On vérifie que l'utilisateur a les droits necessaires
        if( ! $this->isAuthorised( $projet->getUser()->getId() ) )
        {        
            return $this->redirect($this->generateUrl('projet'));
        }
        if( ! $this->isAuthorised( $message->getUser()->getId() ) )
        {        
            return $this->redirect($this->generateUrl('message'));
        }
        //  On déclarre les formulaires de d'édition et de suppresion de l'entité Message
        $editForm = $this->createEditForm($message, $projet);
        $deleteForm = $this->createDeleteForm($message->getId(), $projet);
        //  On gère ici la soumission du formulaire d'édition
        if( $request->getMethod() == 'PUT')
        {
            $editForm->handleRequest($request);

            if ($editForm->isValid()) {
                $em = $this->getDoctrine()->getManager();
                //  On sauvegarde les modifications effectuées sur le message
                $em->flush();
                //  On récupère le service translator
                $translator = $this->get('translator');
                //  On affiche un message à l'utilisateur
                $texteTraduit = $translator->trans('message.notice');
                $this->get('session')->getFlashBag()->add('notice', $texteTraduit);                
                // On fait une redirection vers la page du rapport sur le projet $projet                
                return $this->redirect($this->generateUrl('message', array('slug' => $projet->getSlug())));
            }
        }
        //  On affiche la vue
        return array(
            'entity'      => $message,
            'projet'      => $projet,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
    * Creates a form to edit a Message entity.
    *
    * @param Message $message The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Message $message, Projet $projet)
    {
        //  On crée le formulaire d'édition du message
        $form = $this->createForm(new MessageType(), $message, array(
            'action' => $this->generateUrl('message_edit', array('id' => $message->getId(), 'slug' => $projet->getSlug())),
            'method' => 'PUT',
            'attr' => array('class' => 'form-horizontal well'),
        ));
        //  On ajoute un bouton pour la submittion du formulaire
        $form->add('submit', 'submit', array('translation_domain' => 'FOSUserBundle', 'label' => 'actions.update', 'attr' => array( 'class' => 'btn btn-primary' )));

        return $form;
    }
    /**
     * @Secure(roles="ROLE_CLIENT")
     * 
     * Deletes a Message entity.
     *
     * @Route("/{id}", name="message_delete")
     * @Method("DELETE")
     * 
     * @ParamConverter("message", options={"mapping": {"id": "id"}} )
     * @ParamConverter("projet", options={"mapping": {"slug": "slug"}} )
     */
    public function deleteAction(Request $request, Message $message, Projet $projet)
    {
        //  On vérifie que l'utilisateur a les droits necessaires
        if( ! $this->isAuthorised( $projet->getUser()->getId() ) )
        {        
            return $this->redirect($this->generateUrl('projet'));
        }
        if( ! $this->isAuthorised( $message->getUser()->getId() ) )
        {        
            return $this->redirect($this->generateUrl('message'));
        }
        //  On défnit le formulaire de suppression du message
        $form = $this->createDeleteForm($message->getId(), $projet);
        $form->handleRequest($request);
        //  On gère la soumission du formulaire
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            //  On supprime le message
            $em->remove($message);
            $em->flush();
        }
        // On fait une redirection vers la page de rapport de l'entité $projet
        return $this->redirect($this->generateUrl('message', array('slug' => $projet->getSlug())));
    }

    /**
     * Creates a form to delete a Message entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id, Projet $projet)
    {
        //  On crée ici le formulaire de uppression de l'entité.
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('message_delete', array('id' => $id, 'slug' => $projet->getSlug())))
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
