# date-inline


### The database field must be nullable

    Schema::create('customers', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->date('start_date')->nullable();
        $table->date('end_date')->nullable();
        $table->timestamps();
    });