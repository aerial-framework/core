<?php

class Hydrator_Amf_Collection extends Doctrine_Hydrator_Abstract
{
    public function hydrateResultSet($stmt)
    {
        //return $stmt->fetchAll(PDO::FETCH_ASSOC);
        return rand(1,1000);
    }
}

