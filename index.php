<?php
    include 'Soldier.php';

    $counter = 1;
    $removed = null;
    $capacity = null;

    $soldiers = array();

    $lineCounter = 0;

    $fp = fopen($_FILES['fileToUpload']['tmp_name'], 'rb');
    while (($line = fgets($fp)) !== false) {
        if($lineCounter === 0) {
            $capacity = intval($line);
            $lineCounter++;
        } else {
            $lineExplode = explode(",",$line);
            array_push($soldiers, new Soldier($lineExplode[0], $lineExplode[1]));
        }
    }

    $allowedCapacity = 0;
    foreach ($soldiers as $soldier) {
        $allowedCapacity += $soldier->getCosts();
    }

    if ($capacity > $allowedCapacity) {
        echo "The capacity that is specified overflow the capacity of the soldiers, so all soldiers could be used!<br>";
        echo "Soldiers list:";
        foreach ($soldiers as $soldier) {
            echo $soldier;
        }

        exit;
    }

    function soldiersComparator(Soldier $firstSoldier, Soldier $secondSoldier) {
        if($firstSoldier->getCosts() < $secondSoldier->getCosts()) {
            $comparatorCoefficient = (float)($secondSoldier->getCosts() / $firstSoldier->getCosts());
            $soldierLoadability = (float)($comparatorCoefficient * $firstSoldier->getLoadability());
            if($soldierLoadability == $secondSoldier->getLoadability()) {
                return 0;
            }
            return ($soldierLoadability < $secondSoldier->getLoadability()) ? 1 : -1;
        }

        if($firstSoldier->getCosts() > $secondSoldier->getCosts()) {
            $comparatorCoefficient = (float)($firstSoldier->getCosts() / $secondSoldier->getCosts());
            $soldierLoadability = (float)($comparatorCoefficient * $secondSoldier->getLoadability());
            if($soldierLoadability == $firstSoldier->getLoadability()) {
                return 0;
            }
            return ($firstSoldier->getLoadability() < $soldierLoadability ) ? 1 : -1;
         }

        if($firstSoldier->getLoadability() == $secondSoldier->getLoadability()) {
            return 0;
        }

        return ($firstSoldier->getLoadability() < $secondSoldier->getLoadability()) ? 1 : -1;
    }

    function findMaximumLoadability(&$localSoldierList) {
        usort($localSoldierList, "soldiersComparator");

        $totalCosts = 0;
        $maxLoadability = 0;
        $soldierIndexes = array();

        for($i = 0; $i < count($localSoldierList); $i++) {
            if(($totalCosts + $localSoldierList[$i]->getCosts()) === $GLOBALS['capacity']) {
                $totalCosts += $localSoldierList[$i]->getCosts();
                $maxLoadability += $localSoldierList[$i]->getLoadability();
                array_push($soldierIndexes, $i);
                break;
            } else if(($totalCosts + $localSoldierList[$i]->getCosts()) < $GLOBALS['capacity']) {
                $totalCosts += $localSoldierList[$i]->getCosts();
                $maxLoadability += $localSoldierList[$i]->getLoadability();
                array_push($soldierIndexes, $i);
            }
        }

        if ($totalCosts != $GLOBALS['capacity']) {
            if ($GLOBALS['removed'] != null) {
                array_push($localSoldierList, $GLOBALS['removed']);
            }

            $copiedSoldierList = $localSoldierList;
            if ($GLOBALS['counter'] > count($soldierIndexes)) {
                $GLOBALS['soldiers'] = $localSoldierList;
                echo '<br> Max Loadability was not found';
                return $soldierIndexes;
            }

            for ($i = 0 ; $i < $GLOBALS['counter'] ; $i++) {
                $key = array_pop($soldierIndexes);
                if ($i == $GLOBALS['counter'] - 1) {
                    $GLOBALS['removed'] = $copiedSoldierList[$key];

                    unset($copiedSoldierList[$key]);
                }
            }

            $GLOBALS['counter']++;
            return findMaximumLoadability($copiedSoldierList);
        }

        $GLOBALS['soldiers'] = $localSoldierList;

        echo '<br> Max Loadability = '.$maxLoadability;

        return $soldierIndexes;
    }

    $soldierIndexes = findMaximumLoadability($soldiers);

    foreach ($soldierIndexes as $value) {
        echo $soldiers[$value];
    }

?>