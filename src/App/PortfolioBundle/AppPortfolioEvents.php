<?php

namespace App\PortfolioBundle;

class AppPortfolioEvents
{
    /**
     * L'évènement app_portfolio.projet.post_projet est lancé chaque fois qu'un
     * projet est créé dans le système.
     *
     * Le « listener » de l'évènement reçoit une instance de
     * App\PortfolioBundle\Event\FilterProjetEvent
     *
     * @var string
     */
    const onProjetPost = 'app_portfolio.projet.post_projet';
    
    /**
     * L'évènement app_portfolio.message.post_message est lancé chaque fois qu'un
     * message est créé dans le système.
     *
     * Le « listener » de l'évènement reçoit une instance de
     * App\PortfolioBundle\Event\FilterMessageEvent
     *
     * @var string
     */
    const onMessagePost = 'app_portfolio.message.post_message';
    
    /**
     * L'évènement app_portfolio.paiement.post_paiement est lancé chaque fois qu'un
     * devis est disponible pour un projet dans le système.
     * Le « listener » de l'évènement reçoit une instance de
     * App\PortfolioBundle\Event\FilterPaiementEvent
     *
     * @var string
     */
    const onPaiementPost = 'app_portfolio.paiement.post_paiement';
    
}
