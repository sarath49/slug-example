<?php

namespace Drupal\slugify\Service;

use Cocur\Slugify\Slugify;

class SlugifyService {

    /**
     * Slug \Cocur\Slugify\Slugify
     */
    protected $slugify;

    /**
     * Class constructor.
     */
    public function __construct() {
        $this->slugify = new Slugify();
    }

   /**
     * Return a slug of a text string provided.
     *
     * @param $string
     * @param $seperator
     */
    public function slugify($string, $seperator = '-') {
        return $this->slugify->slugify($string, $seperator);
    }

}