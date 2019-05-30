<?php 

namespace App\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

Class FrToDatetimeTransformer implements DataTransformerInterface{

    // Tranforme les données originelles pour qu'elles s'afficher dans un formulaire
    public function transform($date){

        if($date === null){
            return '';
        }

        // -> date format Fr
        return $date->format('d/m/Y');

    }

    //C'est l'inverse 
    public function reverseTransform($dateFr){

        if($dateFr === null){
            //exception
            throw new TransformationFailedException("fournir une date SVP");
        }

        $date = \DateTime::createFromFormat('d/m/Y',$dateFr);

        if($date === false){

            //exception
            throw new TransformationFailedException("Le format de la date est incorrect");

        }
        
        return $date;
    }

}



?>