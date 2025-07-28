public function up(): void
{
    Schema::create('job_users', function (Blueprint $table) {
        $table->id();
        $table->foreignId('job_id')->constrained('jobs')->cascadeOnDelete();
        $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
        $table->foreignId('task_id')->nullable()->constrained('tasks')->nullOnDelete();
        $table->string('custom_task')->nullable();
        $table->date('start_date')->nullable();
        $table->date('end_date')->nullable();
        $table->integer('duration_in_days')->nullable();
        $table->enum('status', ['pending', 'in_progress', 'completed', 'cancelled'])->default('pending');
        $table->text('notes')->nullable();
        $table->foreignId('created_by')->constrained('users');
        $table->foreignId('updated_by')->constrained('users');
        $table->timestamps();

        $table->unique(['job_id', 'user_id', 'task_id']);
    });
}
