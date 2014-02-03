<?php

/*
 * This file is part of the BlogBundle.
 *
 * (c) LOKO Steeve <loko.steeve@yahoo.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\BlogBundle;

class AppBlogEvents
{
    /**
     * L'évènement app_blog.commentaire.post_commentaire est lancé chaque fois qu'un
     * commentaire est créé dans le système.
     *
     * Le « listener » de l'évènement reçoit une instance de
     * App\BlogBundle\Event\FilterCommentaireEvent
     *
     * @var string
     */
    const onCommentairePost = 'app_blog.commentaire.post_commentaire';
    
    /**
     * L'évènement app_blog.article.post_article est lancé chaque fois qu'un
     * article est créé dans le système.
     *
     * Le « listener » de l'évènement reçoit une instance de
     * App\BlogBundle\Event\FilterArticleEvent
     *
     * @var string
     */
    const onArticlePost = 'app_blog.article.post_article';
}
