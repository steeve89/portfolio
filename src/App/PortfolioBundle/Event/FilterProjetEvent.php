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
use App\PortfolioBundle\Entity\Projet;

class FilterProjetEvent extends Event
{
    protected $projet;

    public function __construct(Projet $projet)
    {
        $this->projet = $projet;
    }

    public function getProjet()
    {
        return $this->projet;
    }
}
