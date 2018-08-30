<?php

namespace Imanghafoori\HeyMan;

use Imanghafoori\HeyMan\Situations\{EloquentSituations, EventSituations, RouteSituations, ViewSituations};
use Imanghafoori\HeyMan\Switching\Turn;

class HeyMan
{
    use Turn;

    public function forget(): Forget
    {
        return new Forget();
    }

    public function __call($method, $args)
    {
        $this->writeDebugInfo(debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 2));

        return $this->startChain($method, $args);
    }

    /**
     * @return array
     */
    private function situations(): array
    {
        return [
            RouteSituations::class,
            ViewSituations::class,
            EloquentSituations::class,
            EventSituations::class,
        ];
    }

    /**
     * @param $d
     */
    private function writeDebugInfo($d)
    {
        app(Chain::class)->debugInfo = array_only($d[1], ['file', 'line', 'args']);
    }

    /**
     * @param $method
     * @param $args
     * @return mixed
     */
    private function startChain($method, $args)
    {
        foreach ($this->situations() as $className) {
            if (method_exists($className, $method)) {
                return app($className)->$method(...$args);
            }
        }
    }
}
