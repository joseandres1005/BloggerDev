<?php
// src/Blogger/BlogBundle/Repository/BlogRepository.php

namespace Blogger\BlogBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * BlogRepository
 *
 * Esta clase fue generada por el ORM de Doctrine. Abajo añade
 * tu propia personalización a los métodos del repositorio.
 */
class BlogRepository extends EntityRepository
{
  public function getLatestBlogs($limit = null)
    {
        $qb = $this->createQueryBuilder('b')
                   ->select('b, c')
                   ->leftjoin('b.comments', 'c')
                   ->addOrderBy('b.created', 'DESC');

        if (false === is_null($limit))
            $qb->setMaxResults($limit);

        return $qb->getQuery()
                  ->getResult();
    }

  public function getTags()
    {
      $blogTags = $this->createQueryBuilder('b')
                       ->select('b.tags')
                       ->getQuery()
                       ->getResult();

      $tags=array();
      foreach($blogTags as $blogTag)
      {
          $tags = array_merge(explode(",", $blogTag['tags']), $tags);
      } 

      foreach ($tags as &$tag)
      {
        $tag = trim($tag);
      }

    return $tags;

    }

    public function getTagWeights($tags)
    {
        $tagWeights = array();
        if (empty($tags))
            return $tagWeights;

        foreach ($tags as $tag)
        {
            $tagWeights[$tag] = (isset($tagWeights[$tag])) ? $tagWeights[$tag] + 1 : 1;
        }
        // Revuelve las etiquetas
        uksort($tagWeights, function() {
            return rand() > rand();
        });

        $max = max($tagWeights);

        // un peso máximo de 5
        $multiplier = ($max > 5) ? 5 / $max : 1;
        foreach ($tagWeights as &$tag)
        {
            $tag = ceil($tag * $multiplier);
        }

        return $tagWeights;
    }
}