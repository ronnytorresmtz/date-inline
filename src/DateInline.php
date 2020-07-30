<?php

namespace Ronnytorresmtz\DateInline;

use Laravel\Nova\Fields\Field;
use Laravel\Nova\Http\Requests\NovaRequest;

class DateInline extends Field
{
    /**
     * The field's component.
     *
     * @var string
     */
    public $component = 'date-inline';

}
