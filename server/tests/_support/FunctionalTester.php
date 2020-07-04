<?php

namespace Test;

/**
 * Inherited Methods
 * @method void wantToTest($text)
 * @method void wantTo($text)
 * @method void execute($callable)
 * @method void expectTo($prediction)
 * @method void expect($prediction)
 * @method void amGoingTo($argumentation)
 * @method void am($role)
 * @method void lookForwardTo($achieveValue)
 * @method void comment($description)
 * @method void pause()
 *
 * @SuppressWarnings(PHPMD)
 */
class FunctionalTester extends \Codeception\Actor
{
    use _generated\FunctionalTesterActions;

    /**
     * Allowed HTTP methods for a specific route.
     *
     * @param string $route
     *
     * @return array
     */
    public function _routeGetAllowedMethods(string $route): array
    {
        $routes = include __DIR__ . '/../../src/routing.php';

        return
            $routes[$route]['allowed_methods'] ??
            ['GET', 'HEAD', 'POST', 'PUT', 'DELETE', 'CONNECT', 'OPTIONS', 'PATCH', 'PURGE', 'TRACE'];
    }
}
