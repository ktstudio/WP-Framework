<?php

/**
 * Pomocná třída pro měření času (dlouho trvajících) operací apod.
 * 
 * @author Martin Hlaváč
 * @link http://www.ktstudio.cz
 */
class KT_Stopwatch {

    private $start;
    private $end;
    private $elapsed;

    /**
     * Založení měření času s možností automatického startu (hned měříme)
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @param boolean $autostart
     */
    function __construct($autostart = false) {
        if ($autostart) {
            $this->start();
        }
    }

    // --- getry & setry ---------------------------

    /**
     * Vrátí časovou známku začátku, pokud došlo ke spuštění
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @return float
     */
    public function getStart() {
        return $this->start;
    }

    /**
     * Vrátí časovou známku konce, pokud došlo k ukončení
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @return float
     */
    public function getEnd() {
        return $this->end;
    }

    /**
     * Vrátí časovou známku rozdílu staru a konce, pokud došlo ke spuštění i ukončení
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @return float
     */
    public function getElapsed() {
        return $this->elapsed;
    }

    /**
     * Vrátí datum rozdílu staru a konce, pokud došlo ke spuštění i ukončení
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @return \DateTime
     */
    public function getElapsedDateTime() {
        $elapsed = $this->getElapsed();
        if (KT::issetAndNotEmpty($elapsed)) {
            $time = number_format($this->getElapsed(), 6, ".", "");
            $dateTime = DateTime::createFromFormat("U.u", $time);
            return $dateTime;
        }
        return null;
    }

    /**
     * Vrátí čas rozdílu staru a konce, pokud došlo ke spuštění i ukončení
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @return string
     */
    public function getElapsedTime() {
        $dateTime = $this->getElapsedDateTime();
        if (KT::issetAndNotEmpty($dateTime)) {
            return $dateTime->format("H:i:s.u");
        }
        return KT_EMPTY_SYMBOL;
    }

    // --- veřejné metody ---------------------------

    /**
     * Spustí měření (pouze při prvním volání)
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @return boolean
     */
    public function start() {
        if (KT::issetAndNotEmpty($this->start)) {
            return false;
        }
        $this->start = microtime(true);
        return true;
    }

    /**
     * Ukončí měření (pouze při prvním volání)
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @return boolean
     */
    public function stop() {
        if (KT::issetAndNotEmpty($this->end)) {
            return false;
        }
        if (KT::notIssetOrEmpty($this->start)) {
            return false;
        }
        $this->end = microtime(true);
        $this->elapsed = ($this->end - $this->start);
        return true;
    }

}
