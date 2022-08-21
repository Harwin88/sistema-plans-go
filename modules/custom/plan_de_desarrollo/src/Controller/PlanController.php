<?php

namespace Drupal\plan_de_desarrollo\Controller;
use Drupal\Core\Controller\ControllerBase;
use Drupal\file\Entity\File;
use Drupal\Core\Url;
use Drupal\Core\Link;
use Drupal\Core\Datetime\DrupalDateTime;

/**
 * @file
 * Implement PlanController 
 */

class PlanController extends ControllerBase {
    
   
  /**
   * reporte planes de desarrollo retorno [$header, $rows]. 
   */
  public static function getReportData($excel = FALSE) {
    $session = \Drupal::request()->getSession();
    //$search_filter = trim($session->get(planForm::$filterSessionKey));
    $request = array_merge(\Drupal::request()->request->all(), \Drupal::request()->query->all());
    $edi = !empty($request['id']) ? $request['id'] : NULL;

    $rows = [];
    $header = [
      t('#'),
      t('Nombre PlanDesarrollo'),
      t('Nombre Gobernador'),
      t('Cédula'),
      t('Vigencia Inicio'),
      t('Vigencia Terminacion'),
     // t('Adjunto PlanDesarrollo'),
      t('created'),
      t('Edit'),
      t('delete'),
      t('Agregar Lineas'),
    ];

    $query = \Drupal::database()->select('Tblplandesarrollo', 'pt');
    $query->fields('pt', [
      'id',
      'NombrePlanDesarrollo',
      'NombreGobernador',
      'Cedula',
      'Vigencia_Inicio',
      'Vigencia_Terminacion',
     // 'AdjuntoPlanDesarrollo',
      'created'
    ]);
    if (!$excel) {
      $query = $query->extend('Drupal\Core\Database\Query\PagerSelectExtender');
      if(!empty($edi)) {
      $query
      ->condition('id', $edi, '=')
      ->condition('estado', 1, '<>')
        ->limit(20)
        ->element(0);
      }else{
        $query
          ->limit(20)
          ->element(0);
      }
    }

    if ($search_filter) {
      $or = $query->orConditionGroup();
      $or
        ->condition('estado', 1, '<>')
        ->condition('NombrePlanDesarrollo', '%' . $search_filter . '%', 'LIKE');
      $query->condition($or);
    }

    $result = $query->condition('estado', 1, '<>')
      ->orderBy('NombrePlanDesarrollo', 'ASC')
      ->execute();

    $request = array_merge(\Drupal::request()->request->all(), \Drupal::request()->query->all());
    $page = !empty($request['page']) ? $request['page'] : 0;
    $date_formatter = \Drupal::service('date.formatter');

    foreach ($result as $item) {
     // $created = new DrupalDateTime($item->created);
      $delete = Url::fromUserInput('/plandedesarrollo?delete_id=' . $item->id . '&page=' . $page);
      $edit = Url::fromUserInput('/plandedesarrollo?id=' . $item->id . '&page=' . $page);
      $lineas = Url::fromUserInput('/lineas?id=' . $item->id . '&page=' . $page);
      $row = [
     $item->id,
     $item->NombrePlanDesarrollo,
     $item->NombreGobernador,
     $item->Cedula,
     $item->Vigencia_Inicio,
     $item->Vigencia_Terminacion,
    // substr($item->AdjuntoPlanDesarrollo, 35),
     $date_formatter->format($item->created, 'custom', 'd/m/Y H:i:s'),
     Link::fromTextAndUrl(t('<i class="fas fa-edit"></i>'), $edit),
     Link::fromTextAndUrl(t('<i class="fas fa-trash"></i>'), $delete),
     Link::fromTextAndUrl(t('lineas <i class="fas fa-arrow-right"></i>'), $lineas),
      ];
      $rows[] = $row;
    }

    return [$header, $rows];
  }

  /**
   * consultar plan de desarrollo por id 
   * 
   */
  public static function getReportDataId() {
    $request = array_merge(\Drupal::request()->request->all(), \Drupal::request()->query->all());
    $edi = !empty($request['id']) ? $request['id'] : NULL;

    $delete = !empty($request['delete_id']) ? $request['delete_id'] : NULL;
    $query = \Drupal::database()->select('Tblplandesarrollo', 'p');
    $query->condition('id',  $edi != NULL ?  $edi : $delete, '=')->condition('estado', 1, '<>');
    $query->fields('p');
    $result = $query->execute()->fetchAll();
    return $result;

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


/***
 * editar plan de desarrollo editar.
 * 
 */
public static function editPlan($edi, $form_state, $url_docuemnto){
  $id = \Drupal::database()->update('Tblplandesarrollo')
  ->fields([
    'NombrePlanDesarrollo' => $form_state->getValue('NombrePlanDesarrollo'),
    'NombreGobernador' => $form_state->getValue('NombreGobernador'),
    'Cedula' => $form_state->getValue('Cedula'),
    'Vigencia_Inicio' => $form_state->getValue('Vigencia_Inicio'),
    'Vigencia_Terminacion' => $form_state->getValue('Vigencia_Terminacion'),
    'AdjuntoPlanDesarrollo' => $url_docuemnto,
    'update' => \Drupal::time()->getRequestTime(),
  ])->condition('id', $edi, '=')
  ->execute();

  return $id;
}

/**
 * Obtener Url Documentos, para mostrar en contenedor de docuemtos
 * 
 */

public static function getDocumenUrl($form_state){

      $fid = $form_state->getValue(['url_documentos', 0]);
      $fid_plan = $form_state->getValue(['url_pdf_plan', 0]);
  
      $fileDesk = File::load($fid);
      $urlDes = $fileDesk->getFileUri();
      $fileDesk->setPermanent();
      $fileDesk->save();
      $pdfPlan = File::load($fid_plan);
      $pdfPlan_arr = $pdfPlan->getFileUri();
      $pdfPlan->setPermanent();
      $pdfPlan->save();

  return $urlDes.",".$pdfPlan_arr;

}

/**
 * function para Insertar Url 
 * 
*/

public static function addPlan($form_state, $document_url){
    
  $Cedula= $form_state->getValue('Cedula');
  $id = \Drupal::database()->insert('Tblplandesarrollo')
  ->fields([
        'NombrePlanDesarrollo' => $form_state->getValue('NombrePlanDesarrollo'),
        'NombreGobernador' => $form_state->getValue('NombreGobernador'),
        'Cedula' => $form_state->getValue('Cedula'),
        'Vigencia_Inicio' => $form_state->getValue('Vigencia_Inicio'),
        'Vigencia_Terminacion' => $form_state->getValue('Vigencia_Terminacion'),
        'AdjuntoPlanDesarrollo' => $document_url,
        'created' => \Drupal::time()->getRequestTime(),
        'update' => \Drupal::time()->getRequestTime(),
  ])
    ->execute();

    return $id;
}

/**
 * cambio de estado plan de desarrollo
 * 
 */

 public static function deletePlan($delete){
   \Drupal::database()->update('Tblplandesarrollo')
  ->fields([
    'estado' => 1,
    'update' => \Drupal::time()->getRequestTime(),
  ])->condition('id', $delete, '=')
  ->execute();
  return "delete";
 }




}