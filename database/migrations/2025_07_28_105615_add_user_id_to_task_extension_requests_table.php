public function up()
{
    Schema::table('task_extension_requests', function (Blueprint $table) {
        $table->foreignId('user_id')->nullable()->after('employee_id')->constrained('users')->nullOnDelete();
    });
}
