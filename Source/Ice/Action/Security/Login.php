<?php
namespace Ice\Action;

use Ice\Core\Action;
use Ice\Core\Action_Context;
use Ice\Core\Form_Security_Login;

/**
 * Class Security_Login
 *
 * @see Ice\Core\Action
 * @see Ice\Core\Action_Context;
 * @package Ice\Action;
 * @author dp <email>
 * @version 0
 * @since 0
 */
class Security_Login extends Action
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
        'viewRenderClassName' => 'Ice:Php',
        'inputDefaults' => [
            'security' => 'Login_Password',
            'redirect' => '/'
        ],
    ];

    /**
     * Run action
     *
     * @param array $input
     * @param Action_Context $actionContext
     * @return array
     */
    protected function run(array $input, Action_Context $actionContext)
    {
        $resource = Security_Login::getResource();

        $actionContext->addAction(
            'Ice:Form', [
                'form' => Form_Security_Login::getInstance($input['security']),
                'submitTitle' => $resource->get('Login'),
                'redirect' => $input['redirect']
            ]
        );
        return ['resource' => $resource];
    }
}