<?php



Class CPRHandler {

    /**
     * Class constructor
     * 
     * @param string $cpr The CPR number. Accepts numbers with and without leading 0 and/or dash.
     */
    public function __construct(string $cpr) {
        $this->cprTrimmed = str_replace('-', '', $cpr);
        if(!ctype_digit($this->cprTrimmed)) {
            throw new exception("CPR not in a recognizable format");
        }

        if(strlen($this->cprTrimmed) == 9) {
            $this->cprNoDash = "0".$this->cprTrimmed;
        }
        if(strlen($this->cprTrimmed) == 10) {
            $this->cprNoDash = $this->cprTrimmed;
        }

        $this->cprWithDash = substr($this->cprNoDash, 0, 6)."-".substr($this->cprNoDash, 5, 4);
    }

    /**
     * getter for CPR number without dash
     * 
     * @return string CPR number without dash
     */
    public function getCPRNoDash() {
        return $this->cprNoDash;
    }

    /**
     * getter for CPR number with dash
     * 
     * @return string CPR number with dash
     */
    public function getCPRWithDash() {
        return $this->cprWithDash;
    }

    /**
     * Calculate the birthday from CPR number.
     * 
     * @return string with date with format: YYYY-MM-DD
     */
    public function getBirthday() {
        $this->day = substr($this->cprNoDash, 0, 2);
        $this->month = substr($this->cprNoDash, 2, 2);
        $this->year = substr($this->cprNoDash, 4, 2);

        $this->century = 19;
        $this->seven = substr($this->cprNoDash, 6, 1);

        if(($this->seven == 4 | $this->seven == 9) & $this->year <= 36) {
            $this->century = 20;
        }
        if($this->seven >= 5 & $this->seven <= 8) {
            if($this->year <= 57) {
                $this->century = 20;
            } else {
                $this->century = 18;
            }            
        }
        return $this->century.$this->year."-".$this->month."-".$this->day;
    }

    /**
     * Check of CPR number passes the modulus 11 test
     * 
     * @return bool true if the number passes the test, false if not
     */
    public function passModulus(): bool {
        /** Indtil 1. oktober 2007 kunne man ved hjælp af det såkaldte kontrolciffer udføre 
         * en beregning og afgøre om personnummeret var korrekt angivet.
         * CPR-kontoret opfordrer derfor alle som bygger computersystemer til at kunne håndtere 
         * personnumre uden modulus-kontrollen. En konsekvens ved ikke at håndtere personnumre 
         * uden modulus-kontrollen er at nogle personer kan blive nægtet adgang til systemet 
         * uden at det er hensigten.
         */

        $this->factor = array(4, 3, 2, 7, 6, 5, 4, 3, 2);

        $this->cprSplit = str_split($this->cprNoDash);

        $this->ctrlNumber = substr($this->cprNoDash, 9, 1);

        $this->sum = 0;

        for($this->i = 0; $this->i < sizeof($this->factor); $this->i++) {
            $this->sum += $this->factor[$this->i] * $this->cprSplit[$this->i];
        }
        if((($this->sum + $this->ctrlNumber) % 11) == 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Checks the gender of the CPR number
     * 
     * @return bool true if male, false if female
     */
    public function isMale(): bool {
        return substr($this->cprNoDash, 9, 1) % 2 !== 0;
    }
}

?>