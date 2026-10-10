<?php

namespace App\Console\Commands;

use App\Models\Image;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ResetRankCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gap:reset-rank';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset image ranking order grouped by imageable entity';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Starting image rank reset...');

        $imgs = DB::select("select count(id), imageable_id, imageable_type from images group by imageable_id, imageable_type");

        $totalGroups = count($imgs);
        $this->info("Found {$totalGroups} image groups to re-rank.");

        foreach ($imgs as $img) {
            $item_imgs = Image::where('imageable_id', $img->imageable_id)
                ->where('imageable_type', $img->imageable_type)
                ->orderBy('id')
                ->get();

            $rank = 1;
            foreach ($item_imgs as $item_img) {
                $item_img->update(['rank' => $rank]);
                $rank++;
            }
        }

        Log::info('Image ranks successfully reset via gap:reset-rank Artisan command.');
        $this->info('Image ranks reset successfully.');

        return 0;
    }
}
