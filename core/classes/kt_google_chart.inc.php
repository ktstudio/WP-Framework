<?php

/**
 * Jednoduchý graf na základě Google Chart API
 *
 * @author Martin Hlaváč
 * @link http://www.ktstudio.cz
 * Založeno na:
 * @url https://code.google.com/p/php-class-for-google-chart-tools/people/list
 */
class KT_Google_Chart {

    private static $first = true;
    private static $count = 0;
    private $chartType;
    private $data;
    private $columns = array();
    private $rows = array();
    private $div;
    private $title;
    private $colors;
    private $theme = "maximized";
    private $width = 720;
    private $height = 300;

    public function __construct($chartType) {
        $this->chartType = $chartType;
        self::$count ++;
    }

    public function clearColumns() {
        $this->columns = array();
        return $this;
    }

    public function clearRows() {
        $this->rows = array();
        return $this;
    }

    public function addColumn($label, $type, $id = "") {
        array_push($this->columns, array("id" => $id, "label" => $label, "type" => $type));
        return $this;
    }

    public function insertRows(array $rows) {
        foreach ($rows as $key => $value) {
            array_push($this->rows, array("c" => array(array("v" => $key), array("v" => $value))));
        }
        return $this;
    }

    public function initData() {
        $this->data = json_encode(array(
            "cols" => $this->columns,
            "rows" => $this->rows
                ));
        return $this;
    }

    public function getDiv() {
        return $this->div;
    }

    public function getTitle() {
        return $this->title;
    }

    public function getColors() {
        return $this->colors;
    }

    public function getTheme() {
        return $this->theme;
    }

    public function getWidth() {
        return $this->width;
    }

    public function getHeight() {
        return $this->height;
    }

    public function setDiv($div) {
        $this->div = $div;
        return $this;
    }

    public function setTitle($title) {
        $this->title = $title;
        return $this;
    }

    public function setColors($colors) {
        $this->colors = $colors;
        return $this;
    }

    public function setTheme($theme) {
        $this->theme = $theme;
        return $this;
    }

    public function setWidth($width) {
        $this->width = $width;
        return $this;
    }

    public function setHeight($height) {
        $this->height = $height;
        return $this;
    }

    public function draw() {
        $output = "";
        if (self::$first) {
            self::$first = false;
            $output .= '<script type="text/javascript" src="https://www.google.com/jsapi"></script>' . "\n";
            $output .= '<script type="text/javascript">google.load(\'visualization\', \'1.0\', {\'packages\':[\'corechart\']});</script>' . "\n";
        }
        $output .= '<script type="text/javascript">';
        $output .= 'google.setOnLoadCallback(drawChart' . self::$count . ');';
        $output .= 'function drawChart' . self::$count . '(){';
        $output .= 'var data = new google.visualization.DataTable(' . $this->data . ');';
        $output .= 'var options = ' . json_encode($this->getOptions()) . ';';
        $output .= 'var chart = new google.visualization.' . $this->chartType . '(document.getElementById(\'' . $this->div . '\'));';
        $output .= 'chart.draw(data, options);';
        $output .= '} </script>' . "\n";
        return $output;
    }

    private function getOptions() {
        $options = array();
        if (KT::issetAndNotEmpty($this->title)) {
            $options["title"] = $this->title;
        }
        if (KT::issetAndNotEmpty($this->colors)) {
            $options["colors"] = $this->colors;
        }
        if (KT::issetAndNotEmpty($this->theme)) {
            $options["theme"] = $this->theme;
        }
        if (KT::issetAndNotEmpty($this->width)) {
            $options["width"] = $this->width;
        }
        if (KT::issetAndNotEmpty($this->height)) {
            $options["height"] = $this->height;
        }
        return $options;
    }

}
