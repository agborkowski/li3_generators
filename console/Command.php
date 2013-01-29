<?php
/**
 * li3_generators bootstrap config
 *
 * @author Mateusz Prażmowski http://li3.it
 * @package li3_generators
 * @subpackage Console
 */

namespace li3_generators\console;

class Command extends \lithium\console\Command
{

    /**
     * Handles input. Will continue to loop until `$options['quit']` or
     * result is part of `$options['choices']`.
     *
     * @param string $prompt
     * @param array $options
     * @return string Returns the result of the input data. If the input is equal to the `quit`
     *          option boolean `false` is returned
     */
    public function in($prompt = null, array $options = array())
    {
        $defaults = array('choices' => null, 'default' => null, 'quit' => 'q');
        $options += $defaults;
        $choices = null;

        if(is_array($options['choices'])) {
            $choices = '(' . implode('/', $options['choices']) . ')';
        }

        $default = $options['default'] ? "[{$options['default']}] " : '';

        do {
            $this->out("{$prompt} {$choices}: ", false);
            $result = trim($this->request->input());
        } while(!empty($options['choices']) && !in_array($result, $options['choices'], true) && (empty($options['quit']) || $result !== $options['quit']) && ($options['default'] == null || $result !== ''));

        if($result == $options['quit']) {
            return false;
        }

        if($options['default'] !== null && $result == '') {
            return $options['default'];
        }

        return $result;
    }

}

?>