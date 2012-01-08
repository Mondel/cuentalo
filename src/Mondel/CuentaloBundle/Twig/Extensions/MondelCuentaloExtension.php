<?php
// src/Mondel/CuentaloBundle/Twig/Extensions/MondelCuentaloExtension.php

namespace Mondel\CuentaloBundle\Twig\Extensions;

class MondelCuentaloExtension extends \Twig_Extension
{
    public function getFilters()
    {
        return array(
            'created_ago'      => new \Twig_Filter_Method($this, 'createdAgo'),
			'cut_title'        => new \Twig_Filter_Method($this, 'cutTitle'),
            'cut_description'  => new \Twig_Filter_Method($this, 'cutDescription'),
        );
    }

    public function cutTitle($title)
    {
    	$title = trim($title);
    	$title_length = strlen($title);
    	if ($title_length > 50)
    		$title = substr($title, 0, 47) . '...';
    	else if ($title_length == 0)
    		$title = "Una Red Social Diferente, Desahogate Y Comparte Lo Que Te Gusta Anonimamente";
    	return $title;	
    }

    public function cutDescription($description)
    {
        $description = trim($description);
        $description_length = strlen($description);
        if ($description_length > 155)
            $description = substr($description, 0, 152) . '...';
        else if ($description_length == 0)
            $description = "Cuentalo es una red social uruguaya para compartir frases, mensajes, anecdotas, chistes, videos y más. Lo mejor es que todo puede ser anonimamente.";
        return $description;  
    }
    
    public function createdAgo(\DateTime $dateTime)
    {
        $delta = time() - $dateTime->getTimestamp();
        if ($delta < 0)
            throw new \Exception("createdAgo is unable to handle dates in the future");

        $duration = "";
        if ($delta < 60)
        {
            // Segundos
            $time = $delta;
            $duration = "hace " . $time . " segundo" . (($time > 1) ? "s" : "");
        }
        else if ($delta <= 3600)
        {
            // Mins
            $time = floor($delta / 60);
            $duration = "hace " . $time . " minuto" . (($time > 1) ? "s" : "");
        }
        else if ($delta <= 86400)
        {
            // Hours
            $time = floor($delta / 3600);
            $duration = "hace " . $time . " hora" . (($time > 1) ? "s" : "");
        }
        else
        {
            // Days
            $time = floor($delta / 86400);
            $duration = "hace " . $time . " día" . (($time > 1) ? "s" : "");
        }

        return $duration;
    }

    public function getName()
    {
        return 'mondel_cuentalo_extension';
    }
}