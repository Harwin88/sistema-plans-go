<?php

namespace Drupal\plan_de_desarrollo\Controller;
use Drupal\Core\Controller\ControllerBase;
use Drupal\file\Entity\File;
use Drupal\Core\Url;
use Drupal\Core\Link;
use Drupal\Core\Datetime\DrupalDateTime;

/**
 * @file
 * Implement helpController 
 */

class helpController extends ControllerBase {
    
   
 
/***
 * devuelve el texto del boton para el crub 
 * 
 */
public static function getBtnText($edi, $delete){
  $edi= $edi != null? 'Edit' : 'Agregar';
  $delete= $delete != null ? 'Eliminar' : 'Agregar';
  $text_btn =  $edi == 'Edit' ? $edi : $delete;

  return $text_btn;
}


/**
 * obtener las url de los documentos de tipo img o pdf.
 * 
 */

public static function urlView($uel_document_global) {
  
$request = \Drupal::service('request_stack')->getCurrentRequest();
$files =  explode(",",   $uel_document_global);
$img_files =  explode("public://",  $files[0]);
$pdf_files =  explode("public://",  $files[1]);
$port= $request->getPort();
if($request->getHost() == 'localhost'){
  $url_img = 'https://'.$request->getHost().':'.$request->getPort().'/sites/default/files/'.$img_files[1];
  $url_pdf = 'https://'.$request->getHost().':'.$request->getPort().'/sites/default/files/'.$pdf_files[1];
}else{
  $url_img = 'http://'.$request->getHost().'/sites/default/files/'.$img_files[1];
  $url_pdf = 'http://'.$request->getHost().'/sites/default/files/'.$pdf_files[1];
}


return [$url_img, $url_pdf];

}

/**
*Obtiene los datos de nombre de una taxonomy por su nombre.
*/
  public static function getTaxonomyData($taxonomy_name) {
    $term_data = [];
    $terms = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadTree($taxonomy_name);
    foreach ($terms as $term) {
      $term_data[$term->name] = $term->name;
    }
    return $term_data;
  }


/**
* obtiene el id de una taxonomies.
*/
  public static function getIdTaxonomy($name_taxonomy, $name) {
    $vid = $name_taxonomy;
    $id_brand = '';
    $terms = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadTree($vid);
    foreach ($terms as $term) {
      if ($term->name == $name) {
        $id_brand = $term->tid;
        return $id_brand;
      }
    }
    return '';
  }

}