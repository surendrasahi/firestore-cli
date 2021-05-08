<?php

namespace App\Commands;

use App\Repositories\FirestoreRepository;
use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

class ExcelToFirestore extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'upload:xlsx {file : Excel file path (required)} {collection : The name of the collection (required)}';
    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Upload Excel .xlsx file to FireStore Collection';

    /**
     * Execute the console command.
     *
     * @param FirestoreRepository $repository
     * @return mixed
     */
    public function handle(FirestoreRepository $repository)
    {
        $this->info("Upload Started.");
        try {
            $repository->uploadExcelToCollection($this->argument('file'), $this->argument('collection'));
            $this->info("Upload Successful.");
        } catch (\Exception $exception) {
            $this->error($exception->getMessage());
        }
    }

    /**
     * Define the command's schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    public function schedule(Schedule $schedule): void
    {
        // $schedule->command(static::class)->everyMinute();
    }
}
