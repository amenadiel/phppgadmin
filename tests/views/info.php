<?php

/**
 * PHPPgAdmin6
 */

// Include application functions

function infoFactory($container)
{
    $do_render = false;

    $controller = new \PHPPgAdmin\Controller\InfoController($container);

    if ($do_render) {
        $controller->render();
    }

    return $controller;
}
