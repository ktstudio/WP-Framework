<?php

/**
 * Základní společný (KT) interface pro všechny modely typu (WP) term
 * Pozn.: je velmi vhodné implemenovat minimálně pro každý term basový model
 * 
 * @author Martin Hlaváč
 * @link http://www.ktstudio.cz
 */
interface KT_Termable extends KT_Modelable {

    /**
     * @return int
     */
    public function getId();

    /**
     * @return string
     */
    public function getName();

    /**
     * @return string
     */
    public function getSlug();

    /**
     * @return int
     */
    public function getTermTaxonomyId();

    /**
     * @return string
     */
    public function getTaxonomy();

    /**
     * @return string
     */
    public function getDescription();

    /**
     * @return int
     */
    public function getParentId();

    /**
     * @return int
     */
    public function getPostCount();

    /**
     * @return string
     */
    public function getPermalink();
}
