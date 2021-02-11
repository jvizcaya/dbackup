<?php

namespace Jvizcaya\Dbackup\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DbackupGenerate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
     protected $signature = 'dbackup:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a mysql database backup';

    /**
     * The generate backup file name.
     *
     * @var string
     */
     public $file_name;

     /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->file_name = now()->format('Y-m-d-Hi').'.sql.gz';

    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
          config(['filesystem.disks.backup' => config('dbackup.disk')]);

          $this->line('Removing backups with max storage days exceeded');

          $this->delete_backups();

          $this->line('Creating backup...');

          passthru($this->mysqldump());

          $this->info('Database backup '. $this->file_name .' has been created!');


    }


    /**
     * Get the ignore tables of the backup process.
     *
     * @return array
     */
    private function ignore_tables()
    {
            $ignore_tables_array = [];

            $tables = config('dbackup.ignore_tables');

            foreach($tables as $table)
            {
                $sql_sentence = "--ignore-table=". env('DB_DATABASE') .".".$table;

                array_push($ignore_tables_array, $sql_sentence);
            }

            return implode(" ",$ignore_tables_array);
    }

    /**
     * Delete old backups with max storage days exceeded.
     *
     * @return null
     */
    private function delete_backups()
    {
        $max_storage_time = now()->subDay(config('dbackup.storage_days'))->timestamp;

        $backup_files = Storage::disk('backup')->files();

        if($backup_files)
        {
            foreach ($backup_files as $backup_file)
            {
                $last_modified = Storage::disk('backup')->lastModified($backup_file);

                if($last_modified < $max_storage_time)
                {
                    Storage::disk('backup')->delete($backup_file);
                }

            }
        }
    }

    /**
     * Get the mysqldump command.
     *
     * @return string
     */
    private function mysqldump()
    {
          $string = 'mysqldump --single-transaction -h ? -P ? -u ? -p? ? ? | gzip > ?';

          return  Str::replaceArray('?', [
                        config('database.connections.mysql.host'),
                        config('database.connections.mysql.port'),
                        config('database.connections.mysql.username'),
                        quotemeta(config('database.connections.mysql.password')),
                        config('database.connections.mysql.database'),
                        $this->ignore_tables(),
                        Storage::disk('backup')->path($this->file_name)
                  ] ,$string);

    }




}
