<?php

namespace Drupal\plan_de_desarrollo\Controller;
use Drupal\Core\Controller\ControllerBase;
use Drupal\file\Entity\File;
use Drupal\Core\Url;
use Drupal\Core\Link;
use Drupal\Core\Datetime\DrupalDateTime;

/**
 * @file
 * Implement ProgramacionIndicadoresController 
 */

class ProgramacionIndicadoresController extends ControllerBase {
    
  /*
  *
  *obtener el reporte general del indicador id
  */ 

 public static function getReportData($excel = FALSE) {
   $session = \Drupal::request()->getSession();
  // $search_filter = trim($session->get(planForm::$filterSessionKey));
  $request = array_merge(\Drupal::request()->request->all(), \Drupal::request()->query->all());

 //   $indicadores_id = $this->codigoIndicador();
 $indicadores_id= !empty($request['id_indicador']) ? $request['id_indicador'] : NULL;
    $delete = !empty($request['delete_id']) ? $request['delete_id'] : NULL;
    $edi = !empty($request['id_programacion']) ? $request['id_programacion'] : NULL;
    $id_indicador = !empty($request['id_indicador']) ? $request['id_indicador'] : NULL;
    $id_subprogrma = !empty($request['id_subprogramas']) ? $request['id_subprogramas'] : NULL;
    $id_programas = !empty($request['id_programas']) ? $request['id_programas'] : NULL;
    $id_plan = !empty($request['id']) ? $request['id'] : NULL;
    $id_linea = !empty($request['id_linea']) ? $request['id_linea'] : NULL;
    $id_sector = !empty($request['id_sector']) ? $request['id_sector'] : NULL;
    $id_meta = !empty($request['id_meta']) ? $request['id_meta'] : NULL;

   $rows = [];
   $header = [
     t('#'),
     t('Vigencia'),
     t('Programado'),
     t('Medida'),
     t('Ejecutado'),
     t('% de Ejecucion'),
     t('Indicador'),
     t('created'),
     t('Edit'),
     t('delete'),
     t(''),    
   ];

   $query = \Drupal::database()->select('TblIProgramacionIndicadoresProducto', 'pt');
   $query->fields('pt', [
     'id_programacion',
     'Vigencia',
     'ProgramacionIndicador',
     'UnidadMedida',
     'EjecucionIndicador',
     'Avance',
     'id_indicador',
     'created'
   ]);
   if (isset($excel) && isset($id_linea) && !$tras == true) {
     $query = $query->extend('Drupal\Core\Database\Query\PagerSelectExtender');
     $query
       ->limit(20)
       ->element(0);
   }
   if(isset($indicadores_id)){
     $query->condition('id_indicador',   $indicadores_id, '=');
   } 

   if(isset($delete) || isset($edi)){
    $query->condition('id_programacion',  isset($delete) ? $delete : $edi , '=');
   }

   $query->condition('estado',   1, '<>');
   $result = $query->orderBy('id_programacion', 'ASC')
   ->execute();

   $request = array_merge(\Drupal::request()->request->all(), \Drupal::request()->query->all());
   $page = !empty($request['page']) ? $request['page'] : 0;
   $date_formatter = \Drupal::service('date.formatter');
   $suma_ejecutado= 0;
   $suma_programado= 0;
   $unidad_medida= '';
   foreach ($result as $item) {
    // $created = new DrupalDateTime($item->created);
     $delete = Url::fromUserInput('/programas_indicadores?delete_id=' . $item->id_programacion.'&page='.$page.'&id_indicador='.$id_indicador.'&id_meta='.$id_meta.'&id_subprogramas='.$id_subprogrma.'&id_programas='.$id_programas.'&id_sector='.$id_sector.'&id='.$id_plan.'&id_linea='.$id_linea);
     $edit = Url::fromUserInput('/programas_indicadores?id_programacion='.$item->id_programacion.'&page='.$page.'&id_indicador='.$id_indicador.'&id_meta='.$id_meta.'&id_subprogramas='.$id_subprogrma.'&id_programas='.$id_programas.'&id_sector='.$id_sector.'&id='.$id_plan.'&id_linea='.$id_linea);
     $indicadores = Url::fromUserInput('/indicadores?id_indicador=' . $item->id_indicador . '&page=' . $page.'&page='.$page.'&id_indicador='.$id_indicador.'&id_meta='.$id_meta.'&id_subprogramas='.$id_subprogrma.'&id_programas='.$id_programas.'&id_sector='.$id_sector.'&id='.$id_plan.'&id_linea='.$id_linea);
     $indicadores = Url::fromUserInput('/indicadores?id_indicador=' . $item->id_indicador. '&page=' . $page.'&page='.$page.'&id_indicador='.$id_indicador.'&id_meta='.$id_meta.'&id_subprogramas='.$id_subprogrma.'&id_programas='.$id_programas.'&id_sector='.$id_sector.'&id='.$id_plan.'&id_linea='.$id_linea);
    $suma_ejecutado=  $item->EjecucionIndicador + $suma_ejecutado;
    $suma_programado= $item->ProgramacionIndicador + $suma_programado;
    $unidad_medida=   $item->UnidadMedida;
     $row = [
    $item->id_programacion,
    $item->Vigencia,
    $item->ProgramacionIndicador,
    $item->UnidadMedida,
    $item->EjecucionIndicador,
    round($item->Avance, 2).'%',
    Link::fromTextAndUrl(t('<i class="fas fa-arrow-left"></i> Indicador'), $indicadores),
    $date_formatter->format($item->created, 'custom', 'd/m/Y H:i:s'),
    Link::fromTextAndUrl(t('<i class="fas fa-edit"></i>'), $edit),
    Link::fromTextAndUrl(t('<i class="fas fa-trash"></i>'), $delete),
    Link::fromTextAndUrl(t('<i class="fas fa-clipboard-list"></i>'), $indicadores),
     ];
     $rows[] = $row;
   }
     $totales = [
    'Totales:',
    '',
    $suma_programado.' '.$unidad_medida,
    '',
    $suma_ejecutado.' '.$unidad_medida,
    round((($suma_ejecutado/$suma_programado)*100), 2).'%',
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
   * consultar plan de desarrollo por id 
   * 
   */
  public static function getReportDataId() {
    $request = array_merge(\Drupal::request()->request->all(), \Drupal::request()->query->all());
    $edi = !empty($request['id_programacion']) ? $request['id_programacion'] : NULL;
    $delete = !empty($request['delete_id']) ? $request['delete_id'] : NULL;

  
    $query = \Drupal::database()->select('TblIProgramacionIndicadoresProducto', 'p');
    $query->condition('id_programacion',  isset($edi) ?  $edi : $delete, '=');
    $query->fields('p');
    $result = $query->execute()->fetchAll();
  
    return $result;

  }





public static function addPrograma($form_state, $id_indicador, $url_pdf){


    $id = \Drupal::database()->insert('TblIProgramacionIndicadoresProducto')
    ->fields([
          'Vigencia' => $form_state->getValue('Vigencia'), 
          'ProgramacionIndicador' => $form_state->getValue('ProgramacionIndicador'),
          'UnidadMedida' =>  $form_state->getValue('UnidadMedida'),
          'EjecucionIndicador' =>  $form_state->getValue('EjecucionIndicador'),
          'Avance' => ($form_state->getValue('EjecucionIndicador')/ $form_state->getValue('ProgramacionIndicador'))*100,
          'urls_evidencias' => $url_pdf,
          'id_indicador' => $id_indicador,
          'created' => \Drupal::time()->getRequestTime(),
          'update' => \Drupal::time()->getRequestTime(),
    ])
      ->execute();


    return $id;
}

/**
 * cambio modificar programa de desarrollo
 * 
 */

 public static function updateProgrma($form_state, $edi){
  $id = \Drupal::database()->update('TblIProgramacionIndicadoresProducto')
   ->fields([
    'Vigencia' => $form_state->getValue('Vigencia'), 
    'ProgramacionIndicador' => $form_state->getValue('ProgramacionIndicador'),
    'UnidadMedida' =>  $form_state->getValue('UnidadMedida'),
    'EjecucionIndicador' =>  $form_state->getValue('EjecucionIndicador'),
    'Avance' => ($form_state->getValue('EjecucionIndicador')/ $form_state->getValue('ProgramacionIndicador'))*100,
    'urls_evidencias' => $this->getDocumenUrl($form_state),
    'update' => \Drupal::time()->getRequestTime(),
])->condition('id_programacion',  $edi, '=')->execute();
  return $id;
 }

 /**
 * cambio modificar programa de desarrollo
 * 
 */

public static function updateDeleteProgrma($form_state, $edi){
  $id = \Drupal::database()->update('TblIProgramacionIndicadoresProducto')
   ->fields([
    'estado' => 1, 
    'update' => \Drupal::time()->getRequestTime(),
])->condition('id_programacion',  $edi, '=')->execute();
  return $id;
 }


 /**
 * Obtener Url Documentos, para mostrar en contenedor de docuemtos
 * 
 */

public function getDocumenUrl($form_state){

  $fid = $form_state->getValue(['url_pdf_programacion', 0]);

  $fileDesk = File::load($fid);
  $urlDes = $fileDesk->getFileUri();
  $fileDesk->setPermanent();
  $fileDesk->save();
var_dump($urlDes);

return $urlDes;

}


}