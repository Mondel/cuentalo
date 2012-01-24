<?php
/**
 * Clase para los soportes de eXponsor. Con ella podremos solicitar
 * enlaces y presentarlos posteriormente en la web.
 * 
 * Version PHP >= 4
 * 
 * @author Antonio Teixeira, Victor Caballero
 * @version 2.1
 * @since 12-05-2009
 * @link http://www.exponsor.com
 */

require_once(dirname(__FILE__).'/eXp-config.inc.php');

class eXponsor extends eXp_config
{
  var $pagina;	
  var $ruta;
  var $v_enlaces;
  var $version;
  
  var $formato;
	
  
  function eXponsor()
  {
  	parent::eXp_config();
  	
  	$this->pagina	  = false;
  	$this->ruta 	  = false;
  	$this->v_enlaces  = false;
  	$this->version    = '2.1';
  	
  	$this->formato	  = false;
  	
  	return;	
  }
  
  
  function verifica_vars()
  {
  	if( empty($this->EXP_COD_SOPORTE) || empty($this->EXP_COD_WEB) ) return $this->set_error("ERROR de configuraci&oacute;n");
  	
  	return true;
  }
  
  
  function define_variables_get()
  {
  	if(empty($this->EXP_VARIABLES_GET)) return false;
  	
  	if(is_string($this->EXP_VARIABLES_GET))
  	{
  		$this->EXP_VARIABLES_GET = str_replace("\\","",trim($this->EXP_VARIABLES_GET));
  		$this->EXP_VARIABLES_GET = eregi_replace("'|\"|array|\(|\)","",$this->EXP_VARIABLES_GET);
		$this->EXP_VARIABLES_GET = explode(",",$this->EXP_VARIABLES_GET);	
  	}
  	return true;
  }
  
  
  function define_pagina()
  {
  	$this->pagina  = $_SERVER['HTTP_HOST'];
  	$this->pagina .= isset($this->EXP_REDIRECT) && $this->EXP_REDIRECT===1 ? $_SERVER['REDIRECT_URL'] : $_SERVER['REQUEST_URI'];
  	
  	@list($this->pagina,$resto) = explode("?",$this->pagina);
  	
  	if( empty($resto) || !$this->define_variables_get() ) return;
	
	$v_get	  = isset($_GET) ? $_GET : $_REQUEST;
  	$v_param  = false;	
	foreach($v_get as $key => $valor) 
	{
		if( !in_array($key,$this->EXP_VARIABLES_GET) ) continue;
		
		$v_param[$key] = "$key=$valor";
	}
	
	if( $v_param )
	{
		ksort($v_param);
		$this->pagina .= "?".implode('&',$v_param); 	
	}
	return;
  }
  
  
  function set_ruta_enlaces()
  {
   	$ruta  = "http://w3.exponsor.com/enlaces.php?";
   	$ruta .= "pagina=".urlencode($this->pagina);
    $ruta .= "&cod_web=".$this->EXP_COD_WEB;
    $ruta .= "&cod_soporte=".$this->EXP_COD_SOPORTE;
    $ruta .= "&ip=".$_SERVER['REMOTE_ADDR'];
    $ruta .= "&cantidad=".$this->EXP_NUM_ENLACES;
	$ruta .= "&version=".$this->version;
	
	$ruta .= $this->EXP_MODO ? "&modo=".$this->EXP_MODO : "";
  	
  	$this->ruta = $ruta;
  	
    return;
  }
  
  
  function obtiene_enlaces()
  {
  	if( !$enlaces = $this->file_get_contents() ) return false;
  		
  	return $this->xml_enlaces($enlaces);
  }
  
  
  function pinta_enlaces()
  {
  	if( !$this->verifica_vars() ) return false;
  	
  	$this->define_pagina();
  	
  	$this->set_ruta_enlaces();
  	
  	if( !$this->v_enlaces = $this->obtiene_enlaces() ) return false;
  	
	$this->define_separador();
  	
  	$this->define_formato();
	
	return $this->pinta_enlaces_formato();
  }
  
  
  function define_separador()
  {
  	switch($this->EXP_SEPARADOR)
  	{
  		case 1: 	$this->EXP_SEPARADOR = '&nbsp;';	break;
  		case 2: 	$this->EXP_SEPARADOR = '-';			break;
  		case 3: 	$this->EXP_SEPARADOR = '|';			break;
  		case 4:		$this->EXP_SEPARADOR = '||';		break;
  		default:	$this->EXP_SEPARADOR = false;		break;
  	}	
  }
  
  
  function define_formato()
  {
  	$c_padre = $this->EXP_C_PADRE ? " class=\"".$this->EXP_C_PADRE."\"" : false;
  	$c_hija  = $this->EXP_C_HIJA  ? " class=\"".$this->EXP_C_HIJA."\""  : false;
  	
  	switch($this->EXP_BLOQUE)
  	{
  		case 'table':
  			if($this->EXP_FORMATO=='vertical')
  			{
  				$this->formato['b_padre_ini'] = "<table{$c_padre}>";
	  			$this->formato['b_hijo_ini']  = "<tr><td{$c_hija}>";
	  			$this->formato['b_hijo_fin']  = "</td></tr>";
	  			$this->formato['b_padre_fin'] = "</table>";
	  			
	  			if($this->EXP_SEPARADOR) $this->EXP_SEPARADOR = "<tr{$c_padre}><td{$c_hija}>{$this->EXP_SEPARADOR}</td></tr>";
	  			
  			}else{
  				$this->formato['b_padre_ini'] = "<table{$c_padre}><tr>";
	  			$this->formato['b_hijo_ini']  = "<td{$c_hija}>";
	  			$this->formato['b_hijo_fin']  = "</td>";
	  			$this->formato['b_padre_fin'] = "</tr></table>";
	  			
	  			if($this->EXP_SEPARADOR) $this->EXP_SEPARADOR = "<td{$c_hija}>{$this->EXP_SEPARADOR}</td>";
  			}
  		break;
  		
  		case 'div':
  			$this->formato['b_padre_ini'] = "<div{$c_padre}>";
	  		$this->formato['b_hijo_ini']  = "";
	  		$this->formato['b_hijo_fin']  = "";
	  		$this->formato['b_padre_fin'] = "</div>";
	  		
	  		if($this->EXP_SEPARADOR) $this->EXP_SEPARADOR = " {$this->EXP_SEPARADOR} ";
	  		if($this->EXP_FORMATO=='vertical') $this->EXP_SEPARADOR .= "<br />";
  		break;
  		
  		case 'ul':
  			$this->formato['b_padre_ini'] = "<ul{$c_padre}>";
	  		$this->formato['b_hijo_ini']  = "<li{$c_hija}>";
	  		$this->formato['b_hijo_fin']  = "</li>";
	  		$this->formato['b_padre_fin'] = "</ul>";
	  		
	  		if($this->EXP_SEPARADOR) $this->EXP_SEPARADOR = "<li{$c_hija}>{$this->EXP_SEPARADOR}</li>";
  		break;
  		
  		default:
  			$this->formato['b_padre_ini'] = "";
	  		$this->formato['b_hijo_ini']  = "";
	  		$this->formato['b_hijo_fin']  = "";
	  		$this->formato['b_padre_fin'] = "";
	  		
	  		if($this->EXP_SEPARADOR) $this->EXP_SEPARADOR = " {$this->EXP_SEPARADOR} ";
  		break;
  		
  	}
  	
  	$this->formato['target'] = $this->EXP_T_BLANK===1 ? " target=\"_blank\"" : false;
  	
  	return;
  }
  
  
  function codifica_enlaces($html)
  {
  	switch($this->EXP_CODIFICACION)
  	{
  		case 1: $html = utf8_decode($html);		break;	
  		case 2: $html = utf8_encode($html);		break;	
  	}
  	return $html;
  }
  
  
  function pinta_enlaces_formato()
  {
  	$html = array();
    foreach($this->v_enlaces as $enlaces)
    {
		$html[] = $this->formato['b_hijo_ini']."<a href=\"".$enlaces['url']."\"{$this->formato['target']}>".ucfirst($enlaces['titulo'])."</a>".$this->formato['b_hijo_fin'];
    }
    $html = trim($this->formato['b_padre_ini'].implode($this->EXP_SEPARADOR,$html).$this->formato['b_padre_fin']);
    
    return $this->codifica_enlaces($html);
  }
    
  
  function file_get_contents()
  {
  	if(!$this->ruta) return false;
	
  	if($this->EXP_CURL===1) 
  	{
  		$curl_handle = curl_init();
  		curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, $this->EXP_TIMEOUT);
		curl_setopt($curl_handle, CURLOPT_TIMEOUT, $this->EXP_TIMEOUT);
		curl_setopt($curl_handle, CURLOPT_SSL_VERIFYPEER,0);
		curl_setopt($curl_handle, CURLOPT_SSL_VERIFYHOST,0);
		curl_setopt($curl_handle, CURLOPT_URL, $this->ruta);
		$file_contents = curl_exec($curl_handle);
	
		if (curl_errno($curl_handle)) $this->set_error("ERROR en CURL: ".curl_error($curl_handle));
		
    	curl_close($curl_handle);
    	
    	$metodo = "CURL";
  	}else{
		$file_contents = function_exists('file_get_contents') ? @file_get_contents($this->ruta) : implode('',@file($this->ruta));	
		$metodo 	   = function_exists('file_get_contents') ? "file_get_contents" 			: "file";
 	}
 	
 	if(!$file_contents) $this->set_error("{$metodo} no obtiene: {$this->ruta}");
 	
 	return $file_contents;
  }
  
  
  function xml_enlaces($enlaces)
  {
	$v_enlaces = array();
	
	preg_match_all("|<enlaces>(.*)</enlaces>|Usim", $enlaces, $nodos, PREG_SET_ORDER);
	 
	if(!$nodos || !is_array($nodos)) return;
	
	foreach($nodos as $nodo)
	{
		preg_match_all("|<url>(.*)</url>|Usim", $nodo[1], $url, PREG_SET_ORDER);
	  	$vdatos['url'] = trim($url[0][1]);
	  
	  
	  	preg_match_all("|<titulo>(.*)</titulo>|Usim", $nodo[1], $titulo, PREG_SET_ORDER);
	  	$vdatos['titulo'] = trim($titulo[0][1]);
	  
	  	$v_enlaces[] = $vdatos;
	}
	return $v_enlaces;
  }
  
   
  function set_error($error)
  {
	if($this->EXP_DEBUG===1)  echo "<p>$error</p>";
	
	return false;
  }
  
  
}

?>