<?php

namespace Drupal\plan_de_desarrollo_presupuesto\Controller;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\file\Entity\File;
use Drupal\Core\Url;
use Drupal\Core\Link;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;

/**
 * @file
 * Implement presupuestoController 
 */

class presupuestoController extends ControllerBase {
    
   
 /**
   * Function.
   */
  public static function getReportData($excel = FALSE) {
    $session = \Drupal::request()->getSession();
   // $search_filter = trim($session->get(planForm::$filterSessionKey));
     $request = array_merge(\Drupal::request()->request->all(), \Drupal::request()->query->all());
     $id_indicador = !empty($request['id_indicador']) ? $request['id_indicador'] : NULL;
     $edit = !empty($request['id_indicador']) ? $request['id_indicador'] : NULL;
     $id_meta = !empty($request['id_meta']) ? $request['id_meta'] : NULL;
     $delete = !empty($request['delete_id']) ? $request['delete_id'] : NULL;

    $rows = [];
    $header = [
      t('#'),
      t('Código Indicador productos'),
      t('Indicador Producto'),
      t('Tipo'),
      t('Codigo Secretaria'),
      t('created'),
      t('Volver sub programa'),
      t('Edit'),
      t('delete'),
      t(''),    
    ];

    $query = \Drupal::database()->select('TblIndicadoresProducto', 'pt');
    $query->fields('pt', [
      'id_indicador',
      'CodIndicadorProducto',
      'IndicadorProducto',
      'Tipo',
      'CodSecretaria',
      'created',
      'id_meta'
    ]);
    if (isset($excel) && isset($id_meta)) {
      $query = $query->extend('Drupal\Core\Database\Query\PagerSelectExtender');
      $query
        ->condition('id_meta', $id_meta, '=')
        ->limit(20)
        ->element(0);
    }
    if(isset($edit) || isset($delete)){
      $query->condition('id_indicador', isset($edit)  ? $edit : $delete, '=');

    }
    if ($search_filter) {
      $or = $query->orConditionGroup();
      $or
        
        ->condition('IndicadorProducto', '%' . $search_filter . '%', 'LIKE');
        $query->condition($or);
    }

  
    $query->condition('estado',   1, '<>');
    $result = $query->orderBy('IndicadorProducto', 'ASC')
    ->execute();

    $request = array_merge(\Drupal::request()->request->all(), \Drupal::request()->query->all());
    $page = !empty($request['page']) ? $request['page'] : 0;
    $date_formatter = \Drupal::service('date.formatter');

    foreach ($result as $item) {
     // $created = new DrupalDateTime($item->created);
      $delete = Url::fromUserInput('/indicadores?delete_id='.$item->id_indicador.'&id_meta='.$item->id_meta.'&page='.$page);
      $edit = Url::fromUserInput('/indicadores?id_indicador='.$item->id_indicador.'&id_meta='.$item->id_meta.'&page='.$page);
      $url_programa = Url::fromUserInput('/programas_indicadores?id_indicador='.$item->id_indicador.'&page='.$page);
      $atras_metas = Url::fromUserInput('/metasproducto?id_meta='.$item->id_meta.'&page='.$page);
      
      $row = [
     $item->id_indicador,
     $item->CodIndicadorProducto,
     $item->IndicadorProducto,
     $item->Tipo,
     $item->CodSecretaria,
     $date_formatter->format($item->created, 'custom', 'd/m/Y H:i:s'),
     Link::fromTextAndUrl(t('<i class="fas fa-arrow-left"></i> Metas'), $atras_metas),
     Link::fromTextAndUrl(t('<i class="fas fa-edit"></i>'), $edit),
     Link::fromTextAndUrl(t('<i class="fas fa-trash"></i>'), $delete),
     Link::fromTextAndUrl(t('Programación <i class="fas fa-arrow-right"></i>'), $url_programa),
      ];
      $rows[] = $row;
    }
 
    return [$header, $rows];
  }


  
 /**
   * consultar id_indicadors  de desarrollo por id 
   * 
   */
  public static function getReportDataId() {
    $request = array_merge(\Drupal::request()->request->all(), \Drupal::request()->query->all());
    $edi = !empty($request['id_indicador']) ? $request['id_indicador'] : NULL;
    $delete = !empty($request['delete_id']) ? $request['delete_id'] : NULL;
    $query = \Drupal::database()->select('TblIndicadoresProducto', 'p');
    $query->condition('id_indicador', isset($edi) ? $edi : $delete, '=');
    $query->fields('p');
    $result = $query->execute()->fetchAll();
    return $result;

  }

  /***
   * funcion que permite agregar id_indicadors a planes de desarrollo
   * 
   */
public static function addPresupuesto($form_state){

  $id = \Drupal::database()->insert('TblPresupuestoxVigencia')
      ->fields([
       'ValorPresupuestoInicial' => $form_state->getValue('Valor_DecretoLiquidacion_Inicial'),
       'ValorPresupuestoFinal' => $form_state->getValue('Valor_DecretoLiquidacion_Inicial'),
       'ConsecutivoSubPrograma' => $form_state->getValue('ConsecutivoSubPrograma'),
       'created' => \Drupal::time()->getRequestTime(),
       'update' => \Drupal::time()->getRequestTime(),
  ])
    ->execute();

    \Drupal::database()->insert('TblPresupuestoxVigenciaxLineaPresupuestal')
      ->fields([
       'Fondo' => $form_state->getValue('Fondo'),
       'CentroGestor' => $form_state->getValue('CentroGestor'),
       'Pospre' => $form_state->getValue('Pospre'),
       'AreaFuncional' => $form_state->getValue('AreaFuncional'),
       'Proyecto' => $form_state->getValue('Proyecto'),
       'Valor_DecretoLiquidacion_Inicial' => $form_state->getValue('Valor_DecretoLiquidacion_Inicial'),
       'Valor_DecretoLiquidacion_Final' => $form_state->getValue('Valor_DecretoLiquidacion_Inicial'),
       'ConsecutivoPresupuestoxVigencia' => $id,
       'Cpi_Sector' => '',
       'created' => \Drupal::time()->getRequestTime(),
       'update' => \Drupal::time()->getRequestTime(),
  ])
    ->execute();



    return $id;
}

  /***
   * funcincion para editar id_indicadors id_subpro$id_indicador de desarrollo
   * 
   */
  public static function updateIndicador($form_state, $id_indicador){

    $id = \Drupal::database()->update('TblIndicadoresProducto')
    ->fields([
      'CodIndicadorProducto' => $form_state->getValue('CodIndicadorProducto'),
      'IndicadorProducto' => $form_state->getValue('IndicadorProducto'),
      'Tipo' => $form_state->getValue('Tipo'),
      'CodSecretaria' => $form_state->getValue('CodSecretaria'), 
      'update' => \Drupal::time()->getRequestTime(),
    ])
    ->condition('id_indicador', $id_indicador, '=')->execute();
  
      return $id;
  }

  /***
   * funcincion para editar id_indicadors id_subpro$id_indicador de desarrollo
   * 
   */
  public static function deleteIndicador($form, $delete){
    $id= \Drupal::database()->update('TblIndicadoresProducto')
    ->fields([
          'estado' => 1,
          'update' => \Drupal::time()->getRequestTime(),
    ])
    ->condition('id_indicador', $delete, '=')->execute();
  
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

    foreach ($result as $item) {
 
      $row = [
        $item->CodFondo, $item->Fondo.' | '.$item->CodVigenciaFondo.' | '.$item->CodTipoRecurso.' | '.$item->CodSituacionFondo.' | '.$item->CodSituacionFondo.' | '. $item->CodFuenteCUIPO,
      ];
      $rows[] = $row;
    }
 
    return $rows;
  }


}