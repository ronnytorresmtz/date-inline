<?php

namespace Ronnytorresmtz\DateInline;

use Laravel\Nova\Fields\Field;

class DateInline extends Field
{
    /**
     * @var bool
     */
    protected $inlineOnIndex = false;

    /**
     * The field's component.
     *
     * @var string
     */
    public $component = 'date-inline';

    /**
     * Allows field to be editable on index view.
     *
     * @param closure|null $callback
     * @return self
     */
    public function inlineOnIndex(callable $callback = null)
    {
        $inline = true;

        if (is_callable($callback)) {
            $inline = call_user_func($callback, resolve(NovaRequest::class));
        }

        $this->inlineOnIndex = $inline;

        return $this;
    }

    /**
     * Resolve the field's value.
     *
     * @param  mixed  $resource
     * @param  string|null  $attribute
     * @return void
     */
    public function resolve($resource, $attribute = null)
    {
        parent::resolve($resource, $attribute);

        $this->withMeta([
            'inlineOnIndex' => $this->inlineOnIndex,
        ]);
    }

    
    public function showOverdue()
    {
        return $this->withMeta(['showOverdue' => true]);
    }

    public function greaterThan($date)
    {
        return $this->withMeta(['greaterThan' => $date]);
    }

}
