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
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use App\PortfolioBundle\Entity\Paiement;
use App\PortfolioBundle\Form\PaiementType;
use App\PortfolioBundle\Entity\Projet;
use App\PortfolioBundle\AppPortfolioEvents;
use App\PortfolioBundle\Event\FilterPaiementEvent;
use JMS\SecurityExtraBundle\Annotation\Secure;

/**
 * Paiement controller.
 *
 * @Route("/paiement")
 */
class PaiementController extends Controller
{
    /**
     * @Secure(roles="ROLE_ADMIN")
     * 
     * Lists all Paiement entities.
     *
     * @Route("/", name="paiement")
     * @Method("GET")
     * @Template()
     */
    public function indexAction($page)
    {
        $em = $this->getDoctrine()->getManager();
        //  On recupère toutes les entités
        $nombreParPage = $this->container->getParameter('paiement');         
        $paiements =   $em->getRepository('AppPortfolioBundle:Paiement')->getPaiements($nombreParPage, $page);
        //  Le nombre de page à afficher
        $nb_pages = (count($paiements) > 0) ? ceil(count($paiements)/$nombreParPage) : 1;
        //  On affiche la vue
        return array(
            'entities' => $paiements,
            'page' => $page,
            'nb_pages' => $nb_pages,
        );
    }

    /**
     * @Secure(roles="ROLE_ADMIN")
     * 
     * Displays a form to create a new Paiement entity.
     *
     * @Route("/new", name="paiement_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction(Request $request)
    {
        //  On crée une instance de l'entité Paiement
        $paiement = new Paiement();
        //  On déclare le formulaire de création de l'entité Paiement
        $form   = $this->createCreateForm($paiement);
        //  On gère la soumissoon du formulire
        if( $request->getMethod() == 'POST')
        {
            $form->handleRequest($request);

            if ($form->isValid()) {
                //  On sauvegarde le paiement
                $em = $this->getDoctrine()->getManager();
                $em->persist($paiement);
                $em->flush();
                // On crée l'évènement FilterPaiementEvent avec son argument
                $event = new FilterPaiementEvent($paiement);
                // On déclenche l'évènement
                $this->get('event_dispatcher')->dispatch(AppPortfolioEvents::onPaiementPost, $event);
                //  On redirige vers la page de visualisation de l'entité crée
                return $this->redirect($this->generateUrl('paiement_show', array('id' => $paiement->getId())));
            }
        }
        //  On affiche la vue
        return array(
            'entity' => $paiement,
            'form'   => $form->createView(),
        );
    }
    /**
    * Creates a form to create a Paiement entity.
    *
    * @param Paiement $paiement The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(Paiement $paiement)
    {
        //  On crée le formulaire d'ajout de paiement
        $form = $this->createForm(new PaiementType(), $paiement, array(
            'action' => $this->generateUrl('paiement_new'),
            'method' => 'POST',
            'attr' => array('class' => 'form-horizontal'),
        ));
        //  on définit un bouton pour la soumission du formulaire
        $form->add('submit', 'submit', array('translation_domain' => 'FOSUserBundle', 'label' => 'actions.create', 'attr' => array( 'class' => 'btn btn-primary' )));
        //  On retourne le formulaire
        return $form;
    }

    /**
     * @Secure(roles="ROLE_ADMIN")
     * 
     * Finds and displays a Paiement entity.
     *
     * @Route("/{id}", name="paiement_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction(Paiement $paiement)
    {        
        //  On définie le formulaire de suppression du paiement
        $deleteForm = $this->createDeleteForm($paiement->getId());
        //  On affiche la vue
        return array(
            'entity'      => $paiement,
            'delete_form' => $deleteForm->createView(),
        );
    }
    
    /**
     * @Secure(roles="ROLE_ADMIN")
     * 
     * Make Paiement By Wire Transfert
     *
     * @Route("/{id}", name="paiement_wire_transfert")
     * @Method("GET")
     * @Template()
     */
    public function virementBancaireAction(Projet $projet)
    {        
        //  On vérifie que l'utilisateur a les droits necessaires
        if( ! $this->isAuthorised( $projet->getUser()->getId() ) )
        {
            return $this->redirect($this->generateUrl('projet'));
        }
        //  On affiche la vue
        return $this->render( 'AppPortfolioBundle:Paiement:wire-transfert.html.twig', array('entity' => $projet));
    }
    
    /**
     * @Secure(roles="ROLE_CLIENT")
     * 
     * Finds and displays a Projet Invoice.
     *
     * @Route("/{id}", name="invoice")
     * @Method("GET")
     * 
     */
    public function invoiceAction(Projet $projet, $action)
    {        
        //  On vérifie que l'utilisateur a les droits necessaires
        if( ! $this->isAuthorised( $projet->getUser()->getId() ) )
        {
            return $this->redirect($this->generateUrl('projet'));
        }
        //  On vérifie que le paiement a été définie
        $paiement = $projet->getPaiement();
        if ( !$paiement )
        {
            //  On récupère le service translator
            $translator = $this->get('translator');
            //  On affiche un message à l'utilisateur
            $texteTraduit = $translator->trans('invoice.error', array(), 'FOSUserBundle');
            $this->get('session')->getFlashBag()->add('error', $texteTraduit);
            
            return $this->redirect($this->generateUrl('projet'));
        }
        //  On recupère la vue de la facture
        $html = $this->invoiceHtmlAction($projet, $action);
        //  On retourne la vue de la facture au format HTML ou PDF
        if ($action === "view")
        {
            return $html;
        } else { 
            //  On récupère le service translator
            $translator = $this->get('translator');
            //  On affiche un message à l'utilisateur
            $texteFacture = $translator->trans('invoice.pdf.title', array(), 'FOSUserBundle');
            //  On recupère le service de génération de PDF
            $pdfGenerator = $this->get('spraed.pdf.generator');
            return new Response($pdfGenerator->generatePDF($html->getContent()),
                200,
                array(
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'inline; filename="'.$texteFacture.$projet->getId().'.pdf"'
                )
            );
        }
    }
    
    /**
     * @Secure(roles="ROLE_CLIENT")
     * 
     * Finds and displays a Projet HTML Invoice.
     *
     * @Route("/{id}", name="invoice")
     * @Method("GET")
     * 
     */
    public function invoiceHtmlAction($projet, $action)
    {   
        if ($action === "view")
        {
            return $this->render( 'AppPortfolioBundle:Paiement:invoice.html.twig', array('entity' => $projet));
        }
        
        return $this->render( 'AppPortfolioBundle:Paiement:invoice.pdf.twig', array('entity' => $projet));                
    }
    
    /**
     * @Secure(roles="ROLE_ADMIN")
     * 
     * Displays a form to edit an existing Paiement entity.
     *
     * @Route("/{id}/edit", name="paiement_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction(Request $request, Paiement $paiement)
    {
        $em = $this->getDoctrine()->getManager();
        //  On déclarre les formulaires de d'édition et de suppresion de l'entité Paiement
        $editForm = $this->createEditForm($paiement);
        $deleteForm = $this->createDeleteForm($paiement->getId());
        //  On gère ici la soumission du formulaire d'édition
        if( $request->getMethod() == 'PUT')
        {
            $editForm->handleRequest($request);

            if ($editForm->isValid()) {
                //  on sauvegarde les modifications effectuées sur le paiement
                $em->flush();
                //  On récupère le service translator
                $translator = $this->get('translator');
                //  On affiche un message à l'utilisateur
                $texteTraduit = $translator->trans('message.notice');
                $this->get('session')->getFlashBag()->add('notice', $texteTraduit);                
                // On fait une redirection vers la page de liste de l'entité Paiement                
                return $this->redirect($this->generateUrl('paiement'));
            }
        }
        //  On affiche la vue
        return array(
            'entity'      => $paiement,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
    * Creates a form to edit a Paiement entity.
    *
    * @param Paiement $paiement The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Paiement $paiement)
    {
        //  On crée le formulare d'édition de paiement
        $form = $this->createForm(new PaiementType(), $paiement, array(
            'action' => $this->generateUrl('paiement_edit', array('id' => $paiement->getId())),
            'method' => 'PUT',
            'attr' => array('class' => 'form-horizontal'),
        ));
        //  on crée un bouton pour la soumission du formulaire
        $form->add('submit', 'submit', array('translation_domain' => 'FOSUserBundle', 'label' => 'actions.update', 'attr' => array( 'class' => 'btn btn-primary' )));
        //  On retourne le formulaire
        return $form;
    }
    /**
     * @Secure(roles="ROLE_ADMIN")
     * 
     * Deletes a Paiement entity.
     *
     * @Route("/{id}", name="paiement_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Paiement $paiement)
    {
        //  On définie les formulaires d'édition et de suppression
        $form = $this->createDeleteForm($paiement->getId());
        $form->handleRequest($request);
        //  On gère la soumission du formulaire
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            //  On supprime le paiement
            $em->remove($paiement);
            $em->flush();
        }
        // On fait une redirection vers la page de liste de l'entité Paiement  
        return $this->redirect($this->generateUrl('paiement'));
    }

    /**
     * Creates a form to delete a Paiement entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        //  On crée le formulaire de suppression du paiement
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('paiement_delete', array('id' => $id)))
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
