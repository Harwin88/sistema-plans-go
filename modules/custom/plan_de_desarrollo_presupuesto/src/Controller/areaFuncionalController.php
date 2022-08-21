<?php

namespace Drupal\plan_de_desarrollo_presupuesto\Controller;
use Drupal\Core\Controller\ControllerBase;
use Drupal\file\Entity\File;
use Drupal\Core\Url;
use Drupal\Core\Link;
use Drupal\Core\Datetime\DrupalDateTime;

/**
 * @file
 * Implement areaFuncionalController 
 */

class areaFuncionalController extends ControllerBase {
    
   
   
 /**
   * Function.
   */
  public static function getReportData($excel = FALSE) {
    $session = \Drupal::request()->getSession();
   // $search_filter = trim($session->get(planForm::$filterSessionKey));
     $request = array_merge(\Drupal::request()->request->all(), \Drupal::request()->query->all());
     $CodAreaFuncional = !empty($request['CodAreaFuncional']) ? $request['CodAreaFuncional'] : NULL;
     $linea = !empty($request['linea']) ? $request['linea'] : NULL;
     $id_plan = !empty($request['id']) ? $request['id'] : NULL;
     $delete = !empty($request['delete_id']) ? $request['delete_id'] : NULL;
     $tras = !empty($request['id_plan']) ? $request['id_plan'] : NULL;

    $rows = [];
    $header = [
      t('#'),
      t('Descripcion Area Funcional'),
      t('Estado'),
      t('Cod Sub programa'),
      t('created'),
      t('Edit'),
      t('delete'),
    ];

    $query = \Drupal::database()->select('TblAreasFuncionales', 'are');
    $query->fields('are', [
      'CodAreaFuncional',
      'DescripcionAreaFuncional',
      'Estado',
      'CodSubprograma',
      'created',
    ]);
    if (isset($excel) && isset($id_plan) && !$tras == true) {
      $query = $query->extend('Drupal\Core\Database\Query\PagerSelectExtender');
      $query
        ->limit(20)
        ->element(0);
    }
    if(isset($CodAreaFuncional) || isset($delete)){
      $query->condition('CodAreaFuncional',   $CodAreaFuncional  != NULL ?   $CodAreaFuncional  : $delete, '=');

    }else if(isset($linea)) {
      $query->condition('CodAreaFuncional',   $linea, '='); 

    }
    if ($search_filter) {
      $or = $query->orConditionGroup();
      $or
        
        ->condition('DescripcionAreaFuncional', '%' . $search_filter . '%', 'LIKE');
        $query->condition($or);
    }

   
    $query->condition('estado',   1, '<>');
    $result = $query->orderBy('DescripcionAreaFuncional', 'ASC')
    ->execute();

    $request = array_merge(\Drupal::request()->request->all(), \Drupal::request()->query->all());
    $page = !empty($request['page']) ? $request['page'] : 0;
    $date_formatter = \Drupal::service('date.formatter');

    foreach ($result as $item) {
     // $created = new DrupalDateTime($item->created);
      $delete = Url::fromUserInput('/DescripcionAreaFuncional?delete_id='.$item->CodAreaFuncional.'&id='.$item->id_PlanDesarrollo.'&page='.$page);
      $edit = Url::fromUserInput('/DescripcionAreaFuncional?CodAreaFuncional='.$item->CodAreaFuncional."&id=".$item->id_PlanDesarrollo.'&page='.$page);
      $url_sectores = Url::fromUserInput('/sectores?CodAreaFuncional='.$item->CodAreaFuncional.'&page='.$page);
      $atras_plan = Url::fromUserInput('/plandedesarrollo?id='.$item->id_PlanDesarrollo.'&page='.$page);
      
      $row = [
     $item->CodAreaFuncional,
     $item->DescripcionAreaFuncional,
     $item->Estado,
     $item->CodSubprograma,
     $date_formatter->format($item->created, 'custom', 'd/m/Y H:i:s'),
     Link::fromTextAndUrl(t('<i class="fas fa-edit"></i>'), $edit),
     Link::fromTextAndUrl(t('<i class="fas fa-trash"></i>'), $delete),
      ];
      $rows[] = $row;
    }
 
    return [$header, $rows];
  }


  /**
   * consultar plan de desarrollo por id 
   * 
   */
  public static function getReporDescripcionAreaFuncionalId() {
    $request = array_merge(\Drupal::request()->request->all(), \Drupal::request()->query->all());
    $CodAreaFuncional = !empty($request['CodAreaFuncional']) ? $request['CodAreaFuncional'] : NULL;
    $delete = !empty($request['delete_id']) ? $request['delete_id'] : NULL;
    $query = \Drupal::database()->select('TblAreasFuncionales', 'p');
    $query->condition('CodAreaFuncional',   $CodAreaFuncional  != NULL ?   $CodAreaFuncional  : $delete, '=');
    $query->fields('p');
    $result = $query->execute()->fetchAll();

    return $result;

  }

 /**
   * consultar DescripcionAreaFuncionals  de desarrollo por id 
   * 
   */
  public static function getReportDataId() {
    $request = array_merge(\Drupal::request()->request->all(), \Drupal::request()->query->all());
    $edi = !empty($request['CodAreaFuncional']) ? $request['CodAreaFuncional'] : NULL;
    $delete = !empty($request['delete_id']) ? $request['delete_id'] : NULL;
    $query = \Drupal::database()->select('Tblplandesarrollo', 'p');
    $query->condition('id',  $edi != NULL ?  $edi : $delete, '=')->condition('estado', 1, '<>');
    $query->fields('p');
    $result = $query->execute()->fetchAll();
    return $result;

  }

  /***
   * funcion que permite agregar DescripcionAreaFuncionals a planes de desarrollo
   * 
   */
public static function addAreas($form_state, $plan){

  $id = \Drupal::database()->insert('TblAreasFuncionales')
      ->fields([
        'CodAreaFuncional'=> $form_state->getValue('CodAreaFuncional'),
        'DescripcionAreaFuncional'=> $form_state->getValue('DescripcionAreaFuncional'),
        'Estado'=> $form_state->getValue('Estado'),
        'CodSubprograma'=> $form_state->getValue('CodSubprograma'),
        'created' => \Drupal::time()->getRequestTime(),
        'update' => \Drupal::time()->getRequestTime(),
  ])
    ->execute();

    return $id;
}

  /***
   * funcincion para editar DescripcionAreaFuncionals plan de desarrollo
   * 
   */
  public static function updateDescripcionAreaFuncionals($form_state, $CodAreaFuncionals){

    $id = \Drupal::database()->update('TblAreasFuncionales')
    ->fields([
      'DescripcionAreaFuncional' => $form_state->getValue('DescripcionAreaFuncional'),
      'CodVigenciaDescripcionAreaFuncional' => $form_state->getValue('CodVigenciaDescripcionAreaFuncional'),
      'CodTipoRecurso' => $form_state->getValue('CodTipoRecurso'),
      'CodSituacionDescripcionAreaFuncional' => $form_state->getValue('CodSituacionDescripcionAreaFuncional'),
      'CodFuenteCUIPO' => $form_state->getValue('CodFuenteCUIPO'),
      'update' => \Drupal::time()->getRequestTime(),
    ])
    ->condition('CodAreaFuncional', $CodAreaFuncionals, '=')->execute();
  
      return $id;
  }

  /***
   * funcincion para editar DescripcionAreaFuncionals plan de desarrollo
   * 
   */
  public static function deleteDescripcionAreaFuncionals($form, $delete){

    $id= \Drupal::database()->update('TblAreasFuncionales')
    ->fields([
          'estado' => 1,
          'update' => \Drupal::time()->getRequestTime(),
    ])
    ->condition('CodAreaFuncional', $delete, '=')->execute();
  
      return $id;
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


}