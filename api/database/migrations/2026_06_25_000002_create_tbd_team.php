<?php

use App\Models\Team;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('teams')->updateOrInsert(
            ['code' => Team::TBD_CODE],
            [
                'name'       => 'A definir',
                'tla'        => null,
                'flag_code'  => null,
                'group_id'   => null,
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('teams')->where('code', Team::TBD_CODE)->delete();
    }
};
