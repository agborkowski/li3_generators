<?php
/**
 * li3_generators Partial Helper
 *
 * @package li3_generators
 * @subpackage Helper
 */

namespace li3_generators\extensions\helper;

use lithium\template\TemplateException;

class Partial extends \lithium\template\Helper
{
    public function __call($method, $args)
    {
        $context = $this->_context;
        $vars = isset($args[0]) ? $args[0] : array();

        $render = function ($type, $context, $method, $vars) {
            return $context->view()->render(array($type => "_{$method}"), $vars, $context->request()->params);
        };

        try {
            return $render('template', $context, $method, $vars);
        } catch(TemplateException $e) {
            return $render('element', $context, $method, $vars);
        }
    }

}

?>
