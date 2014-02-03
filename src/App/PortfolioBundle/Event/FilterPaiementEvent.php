<?php

/*
 * This file is part of the UserBundle.
 *
 * (c) LOKO Steeve <loko.steeve@yahoo.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\PortfolioBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use App\PortfolioBundle\Entity\Paiement;

class FilterPaiementEvent extends Event
{
    protected $paiement;

    public function __construct(Paiement $paiement)
    {
        $this->paiement = $paiement;
    }

    public function getPaiement()
    {
        return $this->paiement;
    }
}
