<?php

namespace App\Console\Commands;

use App\Support\ForisLeadSync;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ForisBackfill extends Command
{
    protected $signature = 'crm:foris-backfill {--limit=500 : Maximum enquiries per chunk} {--all : Schedule every remaining chunk} {--dry-run : Count eligible unscheduled leads without changes}';

    protected $description = 'Resumably schedule existing GEIC website and historical WordPress leads for Foris';

    public function handle(ForisLeadSync $sync): int
    {
        $limit = max(1, min(500, (int) $this->option('limit')));
        $query = DB::table('counselling_enquiries as e')
            ->leftJoin('foris_lead_deliveries as d', 'd.enquiry_id', '=', 'e.id')
            ->whereIn('e.source', ['website', 'wordpress'])->whereNull('d.id');
        if ($this->option('dry-run')) {
            $this->info($query->count().' existing website leads have not been scheduled.');

            return self::SUCCESS;
        }
        $total = 0;
        do {
            $ids = (clone $query)->orderBy('e.id')->limit($limit)->pluck('e.id');
            foreach ($ids as $id) {
                if ($sync->register((int) $id, true)) {
                    $total++;
                }
            }
        } while ($this->option('all') && $ids->count() === $limit);
        $this->info($total.' existing website leads scheduled. Completed deliveries are never scheduled again.');

        return self::SUCCESS;
    }
}
