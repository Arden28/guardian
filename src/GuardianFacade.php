<?php

namespace Arden28\Guardian;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Arden28\Guardian\Skeleton\SkeletonClass
 */
class GuardianFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'guardian';
    }
}
