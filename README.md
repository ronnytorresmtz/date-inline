# Date-Inline Field

## Laravel Nova Date Inline Field

### Installation

        composer require ronnytorres/date-inline

### Usage

#### Add the field to your resource

        public function fields(Request $request)
        {
                return [

                DateInline::make('Start Date'),
                
        }

#### Show the message 'Date is overdue' at the bottom of the date field

        public function fields(Request $request)
        {
                return [

                DateInline::make('Start Date'),
                
                DateInline::make('End Date')
                        ->showOverdue(),
                ];
        }


#### The database field must be nullable

        $table->date('data_field_name')->nullable();

### Resoure Example
![Image](dateInline_image.PNG)