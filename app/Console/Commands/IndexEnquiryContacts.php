<?php

namespace App\Console\Commands;

use App\Support\LeadDuplicateReview;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class IndexEnquiryContacts extends Command
{
    protected $signature = 'crm:index-enquiry-contacts';

    protected $description = 'Index existing website contacts for future duplicate checks without changing enquiries or deliveries';

    public function handle(LeadDuplicateReview $reviews): int
    {
        $count = 0;
        DB::table('counselling_enquiries as e')
            ->leftJoin('lead_contact_keys as c', 'c.enquiry_id', '=', 'e.id')
            ->whereIn('e.source', ['website', 'wordpress'])->whereNull('c.id')
            ->select('e.id', 'e.email', 'e.phone')->orderBy('e.id')
            ->chunkById(200, function ($enquiries) use ($reviews, &$count): void {
                foreach ($enquiries as $enquiry) {
                    $reviews->indexContact($enquiry);
                    $count++;
                }
            }, 'e.id', 'id');
        $this->info("Indexed {$count} existing contacts. Original enquiries and delivery statuses were not changed.");

        return self::SUCCESS;
    }
}
