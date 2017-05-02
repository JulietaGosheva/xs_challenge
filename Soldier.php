<?php

class Soldier
{
    private $costs, $loadability;

    public function __construct($costs, $loadability)
    {
        $this->costs = $costs;
        $this->loadability = $loadability;
    }

    public function getCosts() {
        return $this->costs;
    }

    public function getLoadability() {
        return $this->loadability;
    }

    public function __toString()
    {
     return '<br>Costs: ' . $this->costs . ' Loadability: '. $this->loadability;
    }
}


?>