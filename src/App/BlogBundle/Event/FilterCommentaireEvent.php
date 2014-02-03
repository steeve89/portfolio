<?php

/*
 * This file is part of the UserBundle.
 *
 * (c) LOKO Steeve <loko.steeve@yahoo.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\BlogBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use App\BlogBundle\Entity\Commentaire;

class FilterCommentaireEvent extends Event
{
    protected $commentaire;

    public function __construct(Commentaire $commentaire)
    {
        $this->commentaire = $commentaire;
    }

    public function getCommentaire()
    {
        return $this->commentaire;
    }
}
