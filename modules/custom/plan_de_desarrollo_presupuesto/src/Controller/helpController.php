<?php

namespace Drupal\plan_de_desarrollo_presupuesto\Controller;
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
    
   

    /**
   * consultar plan de desarrollo por id 
   * 
   */
  public static function getPlanes() {
    $request = array_merge(\Drupal::request()->request->all(), \Drupal::request()->query->all());
    $query = \Drupal::database()->select('Tblplandesarrollo', 'p');
    $query->condition('estado', 1, '<>');
    $query->fields('p', [
      'id',
      'NombrePlanDesarrollo'
    ]);
    $result = $query->execute()->fetchAll();
    $list_option=[];
    for($i=0; $i < sizeof($result); $i++){
    $list_option[$result[$i]->id]= $result[$i]->NombrePlanDesarrollo;
    }
    
    return $list_option;
  }



     /**
   * consultar lineas de desarrollo por id 
   * 
   */
  public static function getPlanes_lineas($id_linea) {
    $query = \Drupal::database()->select('TblLineas', 'pt');
    $query->fields('pt', [
      'id_linea',
      'nombre_linea',
    ]);
    $query->condition('id_PlanDesarrollo', $id_linea, '=');
    $query->condition('estado',   1, '<>');
    $result = $query->execute()->fetchAll();
    $list_option=[];
    for($i=0; $i < sizeof($result); $i++){
    $list_option[$result[$i]->id_linea]= $result[$i]->nombre_linea;
    }
    
    return $list_option;
  }

  /**
  * consultar sector de desarrollo por id 
  * 
  */
 public static function get_sectores_lineas($id_linea) {
   $query = \Drupal::database()->select('TblSectores', 'set');
   $query->fields('set', [
     'id_sector',
     'Sector',
   ]);
   $query->condition('id_linea', $id_linea, '=');
   $query->condition('estado',   1, '<>');
   $result = $query->execute()->fetchAll();
   $sectores_option=[];
   for($i=0; $i < sizeof($result); $i++){
   $sectores_option[$result[$i]->id_sector]= $result[$i]->Sector;
   }
   
   return $sectores_option;
 }



  /**
  * consultar programas de desarrollo por id 
  * 
  */
  public static function get_programas($id_sectores) {
    $query = \Drupal::database()->select('TblProgramas', 'pro');
    $query->fields('pro', [
      'id_programas',
      'Programa',
    ]);
    $query->condition('id_sector', $id_sectores, '=');
    $query->condition('estado',   1, '<>');
    $result = $query->execute()->fetchAll();
    $programas_option=[];
    for($i=0; $i < sizeof($result); $i++){
    $programas_option[$result[$i]->id_programas]= $result[$i]->Programa;
    }
    
    return $programas_option;
  }


   /**
  * consultar programas de desarrollo por id 
  * 
  */
  public static function get_subprogramas($id_programas) {
    \Drupal::logger('id para selecionar los sub programas ')->info("id programa ".$id_programas);
    $query = \Drupal::database()->select('TblsubProgramas', 'sud');
    $query->fields('sud', [
      'id_subprogrma',
      'NombreSubPrograma',
    ]);
    $query->condition('id_programas', $id_programas, '=');
    $query->condition('estado',   1, '<>');
    $result = $query->execute()->fetchAll();
    $subprogramas_option=[];
    for($i=0; $i < sizeof($result); $i++)
    {
    $subprogramas_option[$result[$i]->id_subprogrma]= $result[$i]->NombreSubPrograma;
    }
    \Drupal::logger('id para selecionar los sub programas ')->info("sud programas".print_r($subprogramas_option, true));
    return $subprogramas_option;
  }

 
/***
 * devuelve el texto del boton para el crub 
 * 
 */
public static function getBtnText($edi, $delete){
  $edi= $edi != null? 'Grabar' : 'Agregar';
  $delete= $delete != null ? 'Eliminar' : 'Agregar';
  $text_btn =  $edi == 'Grabar' ? $edi : $delete;

  return $text_btn;
}

/**
  * Function.
  */
  public static function getReportData($id_subprogrma) {
    $session = \Drupal::request()->getSession();
  
   // $search_filter = trim($session->get(planForm::$filterSessionKey));
     $request = array_merge(\Drupal::request()->request->all(), \Drupal::request()->query->all());
     $id_programas = !empty($request['id_programas']) ? $request['id_programas'] : NULL;
   // $id_subprogrma = !empty($request['id_subprogramas']) ? $request['id_subprogramas'] : NULL;
    $subPrograma = !empty($request['subPrograma']) ? $request['subPrograma'] : NULL;
     if($subPrograma && !($id_subprogrma)){
      $id_subprogrma= $subPrograma;
     }
    
    $rows = [];
    $header = [
      t('#'),
      t('FONDO'),
      t('C.G'),
      t('POSPRE'),
      t('AREA FUNCIONAL'),
      t('PROYECTOS'),
      t('VALOR DECRETO INICIAL'),
      t('VALOR DECRETO FINAL'),
      T('total'),
      t('created'),
      t(''),  
      t(''), 
      t('PAC'), 
    ];
 
    $query = \Drupal::database()->select('TblPresupuestoxVigenciaxLineaPresupuestal', 'pt');
    $query->fields('pt', [
      'ConsecutivoPresupuestoxVigenciaxLineaPresupuestal',
      'Fondo',
       'CentroGestor',
       'Pospre',
       'AreaFuncional',
       'Proyecto',
       'Valor_DecretoLiquidacion_Inicial',
       'Valor_DecretoLiquidacion_Final',
       'created'
    ]);
    if (isset($excel) && isset($id_programas)) {
      $query = $query->extend('Drupal\Core\Database\Query\PagerSelectExtender');
      $query
        ->condition('ConsecutivoPresupuestoxVigencia', $id_programas, '=')
        ->limit(20)
        ->element(0);
    }
    $respu='';

    if(isset($id_subprogrma)){
      $sql = \Drupal::database()->select('TblPresupuestoxVigencia', 'pre');
      $respu = $sql->fields('pre', [
        'ConsecutivoPresupuestoxVigencia',
      ])->condition('ConsecutivoSubPrograma', $id_subprogrma, '=')->execute()->fetchAll();
      
    }


   /* if(isset($id_subprogrma) || isset($delete)){
      $query->condition('ConsecutivoPresupuestoxVigenciaxLineaPresupuestal',   $id_subprogrma  != NULL ?   $id_subprogrma  : $delete, '=');
    }*/
    if ($search_filter) {
      $or = $query->orConditionGroup();
      $or
        ->condition('Fondo', '%' . $search_filter . '%', 'LIKE');
        $query->condition($or);
    }
   $or_con= $query->orConditionGroup();
   if($respu != ''){
    for($i=0; $i <= sizeof($respu); $i++){
      $or_con->condition('ConsecutivoPresupuestoxVigencia', $respu[$i]->ConsecutivoPresupuestoxVigencia, '=');
    }
     $query->condition($or_con);
   }
 
   
    $query->condition('estado',   1, '<>');
    $result = $query->orderBy('Fondo', 'ASC')
    ->execute();
 
    $request = array_merge(\Drupal::request()->request->all(), \Drupal::request()->query->all());
    $page = !empty($request['page']) ? $request['page'] : 0;
    $date_formatter = \Drupal::service('date.formatter');
    $suma_decreto_inicial=0;
    $suma_decreto_final=0;
    foreach ($result as $item) {
     // $created = new DrupalDateTime($item->created);
      $delete = Url::fromUserInput('/subprogramas?delete_id=' . $item->ConsecutivoPresupuestoxVigenciaxLineaPresupuestal.'&sector=' . $item->ConsecutivoPresupuestoxVigenciaxLineaPresupuestal.'&id_programas=' . $item->id_programas . '&page=' . $page);
      $edit = Url::fromUserInput('/subprogramas?id_subprogramas=' . $item->ConsecutivoPresupuestoxVigenciaxLineaPresupuestal.'&id_programas=' . $item->id_programas  . '&page=' . $page);
      $pac = Url::fromUserInput('/pac?ConsecutivoPresupuestoxVigenciaxLineaPresupuestal=' . $item->ConsecutivoPresupuestoxVigenciaxLineaPresupuestal . '&page=' . $page. '&subPrograma=' . $id_subprogrma);
      $PAG = Url::fromUserInput('/modificacion_presupusto?ConsecutivoPresupuestoxVigenciaxLineaPresupuestal=' . $item->ConsecutivoPresupuestoxVigenciaxLineaPresupuestal . '&page=' . $page);
      $suma_decreto_inicial= $item->Valor_DecretoLiquidacion_Inicial + $suma_decreto_inicial;
      $suma_decreto_final= $item->Valor_DecretoLiquidacion_Final + $suma_decreto_final;
      $row = [
       $item->ConsecutivoPresupuestoxVigenciaxLineaPresupuestal,
       $item->Fondo,
       $item->CentroGestor,
       $item->Pospre,
       $item->AreaFuncional,
       $item->Proyecto,
       '$ '.number_format($item->Valor_DecretoLiquidacion_Inicial),
       '$ '.number_format($item->Valor_DecretoLiquidacion_Final),
     $date_formatter->format($item->created, 'custom', 'd/m/Y H:i:s'),
     Link::fromTextAndUrl(t('<i class="fas fa-edit"></i>'), $edit),
     Link::fromTextAndUrl(t('<i class="fas fa-trash"></i>'), $delete),
     Link::fromTextAndUrl(t('<i class="fas fa-chart-bar"></i>'),  $PAG),
     Link::fromTextAndUrl(t('<i class="fas fa-cogs"></i>'), $pac),

      ];
      $rows[] = $row;
    }
    $totales = [
      'Totales:',
      '',
      '',
      '',
      '',
      '',
      '$ '.number_format($suma_decreto_inicial),
      '$ '.number_format($suma_decreto_final),
      '',
      '',
      '',
      '',
      '',
       ];
    $rows[] = $totales;
  
 
    return [$header, $rows, $totales];
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
   // $terms_pos = \Drupal::entityTypeManager()->getStorage('taxonomy_term_field_pospre');
  
    foreach ($terms as $term) {
      $term_data[$term->name] = $term->name;
    }
    return $term_data;
  }

  /**
   * Function.
   */
  public static function getFondos() {
    $query = \Drupal::database()->select('TblFondos', 'pt');
    $query->fields('pt', [
      'CodFondo',
      'Fondo',
      'CodVigenciaFondo',
      'CodTipoRecurso',
      'CodSituacionFondo',
      'CodFuenteCUIPO',
      'created',
    ]);
   
    $query->condition('estado',   1, '<>');
    $result = $query->orderBy('Fondo', 'ASC')
    ->execute();
    $request = array_merge(\Drupal::request()->request->all(), \Drupal::request()->query->all());
    $page = !empty($request['page']) ? $request['page'] : 0;
    $date_formatter = \Drupal::service('date.formatter');
    $rows=[];
    foreach ($result as $item) {
      $rows[$item->CodFondo] = $item->CodFondo.'|'.$item->Fondo.'|'.$item->CodVigenciaFondo.'|'.$item->CodTipoRecurso.'|'.$item->CodSituacionFondo.'|'.$item->CodSituacionFondo.'|'. $item->CodFuenteCUIPO;
    }
    return $rows;
  }
/**
 * meto para obtener el area funcional para el presupuesto.
*/
  public static function getAreas(){
    $query = \Drupal::database()->select('TblAreasFuncionales', 'area');
    $query->fields('area', [
      'CodAreaFuncional',
      'DescripcionAreaFuncional',
      'Estado',
    ]);
   
    $query->condition('estado_register',   1, '<>');
    $result = $query->orderBy('DescripcionAreaFuncional', 'ASC')
    ->execute();
    $request = array_merge(\Drupal::request()->request->all(), \Drupal::request()->query->all());
    $page = !empty($request['page']) ? $request['page'] : 0;
    $date_formatter = \Drupal::service('date.formatter');
    $rows=[];
    foreach ($result as $item) {
      $rows[$item->CodAreaFuncional] = $item->CodAreaFuncional.'|'.$item->DescripcionAreaFuncional.'|'.$item->Estado;
    }
    return $rows;
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