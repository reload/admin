<?php

/**
 * This file is part of the <name> project.
 *
 * (c) <yourname> <youremail>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Application\Sonata\MediaBundle\Entity;

use Sonata\MediaBundle\Entity\BaseGallery as BaseGallery;

/**
 * This file has been generated by the Sonata EasyExtends bundle ( http://sonata-project.org/bundles/easy-extends )
 *
 * References :
 *   working with object : http://www.doctrine-project.org/projects/orm/2.0/docs/reference/working-with-objects/en
 *
 * @author <yourname> <youremail>
 */
class Gallery extends BaseGallery
{
    /**
     * @var integer $id
     */
    protected $id;

    /**
     * Get id
     *
     * @return integer $id
     */
    public function getId()
    {
        return $this->id;
    }
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $galleryHasMedias;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->galleryHasMedias = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add galleryHasMedias
     *
     * @param \Application\Sonata\MediaBundle\Entity\GalleryHasMedia $galleryHasMedias
     * @return Gallery
     */
    public function addGalleryHasMedia(\Application\Sonata\MediaBundle\Entity\GalleryHasMedia $galleryHasMedias)
    {
        $this->galleryHasMedias[] = $galleryHasMedias;

        return $this;
    }

    /**
     * Remove galleryHasMedias
     *
     * @param \Application\Sonata\MediaBundle\Entity\GalleryHasMedia $galleryHasMedias
     */
    public function removeGalleryHasMedia(\Application\Sonata\MediaBundle\Entity\GalleryHasMedia $galleryHasMedias)
    {
        $this->galleryHasMedias->removeElement($galleryHasMedias);
    }

    /**
     * Get galleryHasMedias
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getGalleryHasMedias()
    {
        return $this->galleryHasMedias;
    }
}
