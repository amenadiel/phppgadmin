<?php

/**
 * PHPPgAdmin 6.1.3
 */

namespace PHPPgAdmin\Decorators;

use Closure;
use PHPPgAdmin\Traits\HelperTrait;

class Decorator
{
    public $val;
    use HelperTrait;

    public $container;

    public function __construct($value)
    {
        $this->val = $value;
    }

    public function value(array $fields)
    {
        return $this->val;
    }

    /**
     * @param null|string $esc
     * @param scalar      $var
     * @param array       $fields
     */
    public static function get_sanitized_value(&$var, array &$fields, ?string $esc = null)
    {
        if ($var instanceof self) {
            $val = $var->value($fields);
        } else {
            $val = &$var;
        }

        if (\is_string($val)) {
            switch ($esc) {
                case 'xml':
                    return \strtr($val, [
                        '&' => '&amp;',
                        "'" => '&apos;',
                        '"' => '&quot;',
                        '<' => '&lt;',
                        '>' => '&gt;',
                    ]);
                case 'html':
                    return \htmlentities($val, \ENT_COMPAT, 'UTF-8');
                case 'url':
                    return \urlencode($val);
            }
        }

        return $val;
    }

    /**
     * @param Closure                 $callback
     * @param ((mixed|string)[]|null) $params
     *
     * @return CallbackDecorator
     */
    public static function callback(Closure $callback, ?array $params = null)
    {
        return new  CallbackDecorator($callback, $params);
    }

    /**
     * @param scalar $var
     * @param array  $fields
     */
    public static function value_url(&$var, array &$fields)
    {
        return self::get_sanitized_value($var, $fields, 'url');
    }

    /**
     * @return ConcatDecorator
     */
    public static function concat(/* ... */)
    {
        return new ConcatDecorator(\func_get_args());
    }

    /**
     * @param array  $params
     * @param string $str
     *
     * @return ReplaceDecorator
     */
    public static function replace(string $str, array $params)
    {
        return new ReplaceDecorator($str, $params);
    }

    /**
     * @param null|array $default
     * @param string     $fieldName
     *
     * @return FieldDecorator
     */
    public static function field(string $fieldName, ?array $default = null)
    {
        return new FieldDecorator($fieldName, $default);
    }

    /**
     * @param null|array $vars
     * @param mixed      $base
     *
     * @return BranchUrlDecorator
     */
    public static function branchurl($base, ?array $vars = null/* ... */)
    {
        // If more than one array of vars is given,
        // use an ArrayMergeDecorator to have them merged
        // at value evaluation time.
        if (2 < \func_num_args()) {
            $urlvalue = \func_get_args();
            \array_shift($urlvalue );

            return new BranchUrlDecorator($base, new ArrayMergeDecorator($urlvalue));
        }

        return new BranchUrlDecorator($base, $vars);
    }

    /**
     * @param null|array $vars
     * @param mixed      $base
     *
     * @return ActionUrlDecorator
     */
    public static function actionurl($base, ?array $vars = null/* ... */)
    {
        // If more than one array of vars is given,
        // use an ArrayMergeDecorator to have them merged
        // at value evaluation time.
        if (2 < \func_num_args()) {
            $urlvalue = \func_get_args();
            \array_shift($urlvalue);

            return new ActionUrlDecorator($base, new ArrayMergeDecorator($urlvalue));
        }

        return new ActionUrlDecorator($base, $vars);
    }

    /**
     * @param null|array $vars
     * @param mixed      $base
     *
     * @return RedirectUrlDecorator
     */
    public static function redirecturl($base, ?array $vars = null/* ... */)
    {
        // If more than one array of vars is given,
        // use an ArrayMergeDecorator to have them merged
        // at value evaluation time.
        if (2 < \func_num_args()) {
            $urlvalue = \func_get_args();
            \array_shift($urlvalue);

            return new RedirectUrlDecorator($base, new ArrayMergeDecorator($urlvalue));
        }

        return new RedirectUrlDecorator($base, $vars);
    }

    /**
     * @param null|array $vars
     * @param mixed      $base
     *
     * @return UrlDecorator
     */
    public static function url($base, ?array $vars = null/* ... */)
    {
        // If more than one array of vars is given,
        // use an ArrayMergeDecorator to have them merged
        // at value evaluation time.

        if (2 < \func_num_args()) {
            $urlvalue = \func_get_args();
            $base = \array_shift($urlvalue );

            return new UrlDecorator($base, new ArrayMergeDecorator($urlvalue ));
        }

        return new UrlDecorator($base, $vars);
    }

    /**
     * @return IfEmptyDecorator
     */
    public static function ifempty($value, string $empty, $full = null)
    {
        return new IfEmptyDecorator($value, $empty, $full);
    }
}
