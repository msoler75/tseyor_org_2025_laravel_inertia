<?php

namespace App\Http\Controllers\Backpack\Fields;

use Backpack\CRUD\app\Library\CrudPanel\CrudField;

class OptionalBelongsToField extends CrudField
{
    public function __construct($name, $label = '')
    {
        parent::__construct($name, $label);
        $this->type('optional_belongs_to');
    }
}
