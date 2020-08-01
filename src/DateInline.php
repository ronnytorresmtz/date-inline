<?php

namespace Ronnytorresmtz\DateInline;

use Laravel\Nova\Fields\Field;

class DateInline extends Field
{
    /**
     * The field's component.
     *
     * @var string
     */
    public $component = 'date-inline';

    
    public function showOverdue()
    {
        return $this->withMeta(['showOverdue' => true]);
    }

}
