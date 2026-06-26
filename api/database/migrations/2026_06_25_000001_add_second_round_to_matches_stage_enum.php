<?php

use App\Http\Enums\MatchStage;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $this->modifyStageEnum(array_column(MatchStage::cases(), 'value'));
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $values = array_values(array_filter(
            array_column(MatchStage::cases(), 'value'),
            fn(string $value) => $value !== MatchStage::SECOND_ROUND->value,
        ));

        $this->modifyStageEnum($values);
    }

    /**
     * @param  list<string>  $values
     */
    private function modifyStageEnum(array $values): void
    {
        $list = implode(', ', array_map(fn(string $value) => "'{$value}'", $values));

        DB::statement(
            "ALTER TABLE matches MODIFY COLUMN stage ENUM({$list}) NOT NULL DEFAULT '" . MatchStage::GROUP_STAGE->value . "'"
        );
    }
};
