<?php

namespace Drupal\plan_de_desarrollo_presupuesto\Controller;
use Drupal\Core\Controller\ControllerBase;
use Drupal\file\Entity\File;
use Drupal\Core\Url;
use Drupal\Core\Link;
use Drupal\Core\Datetime\DrupalDateTime;

/**
 * @file
 * Implement historicoController 
 */

class historicoController extends ControllerBase {
    
   
   
 /**
   * Function.
   */
  public static function getReportData($excel = FALSE) {
    $request = array_merge(\Drupal::request()->request->all(), \Drupal::request()->query->all());
    $ConsecutivoPresupuestoxVigenciaxLineaPresupues = !empty($request['ConsecutivoPresupuestoxVigenciaxLineaPresupuestal']) ? $request['ConsecutivoPresupuestoxVigenciaxLineaPresupuestal'] : NULL;
  
  
    $session = \Drupal::request()->getSession();
   // $search_filter = trim($session->get(planForm::$filterSessionKey));
     $request = array_merge(\Drupal::request()->request->all(), \Drupal::request()->query->all());
     $CodAreaFuncional = !empty($request['CodAreaFuncional']) ? $request['CodAreaFuncional'] : NULL;
     $id_hisotry = !empty($request['linea']) ? $request['linea'] : NULL;
     $id_plan = !empty($request['id']) ? $request['id'] : NULL;
     $delete = !empty($request['delete_id']) ? $request['delete_id'] : NULL;
     $tras = !empty($request['id_plan']) ? $request['id_plan'] : NULL;

    $rows = [];
    $header = [
      t('#'),
      t('Tipo Movimiento'),
      t('EstValor Movimientoado'),
      t('Consecutivo Decreto'),
      t('Consecutivo Presupuestos Vigencias LineaPresupues'),
      t('created'),
      t('Edit'),
      t('delete'),
    ];

    $query = \Drupal::database()->select('Tbl_Historial_Presupuestal', 'histo');
    $query->fields('histo', [
      'ConsecutivoHistorialPresupuestal',
      'Tipo_Movimiento',
      'Valor_Movimiento',
      'ConsecutivoDecreto',
      'ConsecutivoPresupuestoxVigenciaxLineaPresupues',
      'created',
    ]);
    if (isset($excel) && isset($id_plan) && !$tras == true) {
      $query = $query->extend('Drupal\Core\Database\Query\PagerSelectExtender');
      $query
        ->limit(20)
        ->element(0);
    }
    if(isset($CodAreaFuncional) || isset($delete)){
      $query->condition('ConsecutivoHistorialPresupuestal',   $ConsecutivoHistorialPresupuestal  != NULL ?   $ConsecutivoHistorialPresupuestal  : $delete, '=');

    }else if(isset($ConsecutivoPresupuestoxVigenciaxLineaPresupues)) {
      $query->condition('ConsecutivoPresupuestoxVigenciaxLineaPresupues',   $ConsecutivoPresupuestoxVigenciaxLineaPresupues, '='); 

    }
    if ($search_filter) {
      $or = $query->orConditionGroup();
      $or
        
        ->condition('Tipo_Movimiento', '%' . $search_filter . '%', 'LIKE');
        $query->condition($or);
    }

   
    $query->condition('estado',   1, '<>');
    $result = $query->orderBy('Tipo_Movimiento', 'ASC')
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
     $item->ConsecutivoHistorialPresupuestal,
     $item->Tipo_Movimiento,
     '$ '.number_format($item->Valor_Movimiento),
     $item->ConsecutivoDecreto,
     $item->ConsecutivoPresupuestoxVigenciaxLineaPresupues,
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
public static function addHistory($form_state, $plan){
  $request = array_merge(\Drupal::request()->request->all(), \Drupal::request()->query->all());
  $ConsecutivoPresupuestoxVigenciaxLineaPresupues = !empty($request['ConsecutivoPresupuestoxVigenciaxLineaPresupuestal']) ? $request['ConsecutivoPresupuestoxVigenciaxLineaPresupuestal'] : NULL;


  
  $id = \Drupal::database()->insert('Tbl_Historial_Presupuestal')
      ->fields([
        'ConsecutivoHistorialPresupuestal'=> $form_state->getValue('ConsecutivoHistorialPresupuestal'),
        'Tipo_Movimiento'=> $form_state->getValue('Tipo_Movimiento'),
        'Valor_Movimiento'=> $form_state->getValue('Valor_Movimiento'),
        'ConsecutivoDecreto'=> $form_state->getValue('ConsecutivoDecreto'),
        'ConsecutivoPresupuestoxVigenciaxLineaPresupues'=> $ConsecutivoPresupuestoxVigenciaxLineaPresupues,
        'created' => \Drupal::time()->getRequestTime(),
        'update' => \Drupal::time()->getRequestTime(),
  ])
    ->execute();
    historicoController::updatePresupuesto($ConsecutivoPresupuestoxVigenciaxLineaPresupues, $form_state->getValue('Valor_Movimiento'), $form_state->getValue('Tipo_Movimiento'));
    return $id;
}


/***
   * funcincion para editar id_indicadors id_subpro$id_indicador de desarrollo
   * 
   */
  public static function updatePresupuesto($id, $final_restar, $movimiento){
    $sql=\Drupal::database()->select('TblPresupuestoxVigenciaxLineaPresupuestal', 'pre');
  $value= $sql->fields('pre', [
    'Valor_DecretoLiquidacion_Final',
  ])->condition('ConsecutivoPresupuestoxVigenciaxLineaPresupuestal',  $id, '=')->execute()->fetchAll();

$operation= 0;

if($movimiento == 'Adiciones Presupuestales' || $movimiento == 'Créditos'){
  $operation= $final_restar + $value[0]->Valor_DecretoLiquidacion_Final;
}else{
  $operation= $value[0]->Valor_DecretoLiquidacion_Final - $final_restar;

}

    $id = \Drupal::database()->update('TblPresupuestoxVigenciaxLineaPresupuestal')
    ->fields([
      'Valor_DecretoLiquidacion_Final' => $operation,
      'update' => \Drupal::time()->getRequestTime(),
    ])
    ->condition('ConsecutivoPresupuestoxVigenciaxLineaPresupuestal', $id, '=')->execute();
  
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

 /**
  * decretos selector
  * 
  */
  public static function get_sectores_decretos() {
    $query = \Drupal::database()->select('TblDecretosPresupuestales', 'set');
    $query->fields('set', [
      'Numero_Decreto',
      'Fecha_Decreto',
    ]);
    $query->condition('estado',   1, '<>');
    $result = $query->execute()->fetchAll();
    $sectores_option=[];
    for($i=0; $i < sizeof($result); $i++){
    $sectores_option[$result[$i]->Numero_Decreto]= $result[$i]->Numero_Decreto.' | '.$result[$i]->Fecha_Decreto;
    }
    
    return $sectores_option;
  }

}