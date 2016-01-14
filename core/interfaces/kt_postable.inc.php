<?php

/**
 * Základní společný (KT) interface pro všechny modely typu (WP) Post
 * Pozn.: je velmi vhodné implemenovat minimálně pro každý post basový model
 * 
 * @author Martin Hlaváč
 * @link http://www.ktstudio.cz
 */
interface KT_Postable extends KT_Modelable {

    /**
     * @return \WP_Post
     */
    public function getPost();

    /**
     * @return string
     */
    public function getTitle();

    /**
     * @return string
     */
    public function getContent();

    /**
     * @return string
     */
    public function getExcerpt($withTheFilter = true, $customExcerptLength = null, $customExcerptMore = null);

    /**
     * @return string
     */
    public function getPermalink();

    /**
     * @return string
     */
    public function getTitleAttribute();

    /**
     * @return int
     */
    public function getAuthorId();

    /**
     * @return int
     */
    public function getThumbnailId();

    /**
     * @return string
     */
    public function getPublishDate($dateFormat = "d.m.Y");

    /**
     * @return string
     */
    public function getPostType();

    /**
     * @return boolean
     */
    public function hasExcerpt();

    /**
     * @return boolean
     */
    public function hasThumbnail();
}
