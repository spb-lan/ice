<?php
/**
 * Ice action form model class
 *
 * @link http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Action;

use Ice\Core\Action;
use Ice\Core\Action_Context;
use Ice\Core\Model;

/**
 * Class Form_Model
 *
 * Action for model form
 *
 * @see Ice\Core\Action
 * @see Ice\Core\Action_Context
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package Ice
 * @subpackage Action
 *
 * @version 0.0
 * @since 0.0
 */
class Form_Model extends Action
{
    /**  public static $config = [
     *      'afterActions' => [],          // actions
     *      'layout' => null,               // Emmet style layout
     *      'template' => null,             // Template of view
     *      'output' => null,               // Output type: standard|file
     *      'viewRenderClassName' => null,  // Render class for view (example: Ice:Php)
     *      'inputDefaults' => [],          // Default input data
     *      'inputValidators' => [],        // Input data validators
     *      'inputDataProviderKeys' => [],  // InputDataProviders keys
     *      'outputDataProviderKeys' => [], // OutputDataProviders keys
     *      'cacheDataProviderKey' => ''    // Cache data provider key
     *  ];
     */
    public static $config = [
        'viewRenderClassName' => 'Ice:Smarty',
        'inputValidators' => [
            'submitTitle' => 'Ice:Not_Empty',
            'submitActionName' => 'Ice:Not_Empty',
            'modelClassName' => 'Ice:Not_Empty',
            'pk' => 'Ice:Not_Null'
        ],
        'inputDefaults' => [
            'groupping' => 1,
            'submitActionName' => 'Ice:Form_Submit',
            'reRenderClosest' => 'Ice:Form_Model',
            'reRenderActionNames' => [],
            'filterFields' => [],
            'redirect' => '',
            'params' => []
        ],
//        'layout' => Emmet::PANEL_BODY
    ];

    /**
     * Run action
     *
     * @param array $input
     * @param Action_Context $actionContext
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.2
     * @since 0.0
     */
    protected function run(array $input, Action_Context $actionContext)
    {
        /** @var Model $modelClass */
        $modelClass = Model::getClass($input['modelClassName']);

        $form = $modelClass::getForm($input['filterFields']);

        if ($input['pk']) {
            $form->bind($modelClass::getRow($input['pk'], '*'));
        }

        $data = [
            'form' => $form,
            'submitActionName' => $input['submitActionName'],
            'reRenderClosest' => $input['reRenderClosest'],
            'reRenderActionNames' => $input['reRenderActionNames'],
            'groupping' => $input['groupping'],
            'submitTitle' => $input['submitTitle'],
            'redirect' => $input['redirect'],
            'params' => $input['params']
        ];

        $actionContext->addAction('Ice:Form', $data);

        $reRenderActionNames = '';

        foreach ($input['reRenderActionNames'] as $reRenderAction) {
            $reRenderActionNames .= ',\'' . $reRenderAction . '\'';
        }

        $filterFields = '';

        foreach ($input['filterFields'] as $filterField) {
            $filterFields .= ',\'' . $filterField . '\'';
        }

        $input['reRenderActionNames'] = ltrim($reRenderActionNames, ',');
        $input['filterFields'] = ltrim($filterFields, ',');

        array_walk(
            $input['params'], function (&$item, $key) {

            if (is_array($item)) {
                array_walk(
                    $item, function (&$item) {
                    $item = '\'' . $item . '\'';
                }
                );
                $item = $key . ': [' . implode(', ', $item) . ']';
            } else {
                $item = $key . ': \'' . $item . '\'';
            }


        }
        );

        $input['params'] = '{' . implode(', ', $input['params']) . '}';

        return $input;
    }
}