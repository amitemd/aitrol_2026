<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;

class CustomCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'custom:command';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete all inactive users';
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }
    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        DB::table('log_usuario')->insert([
            ['id_usuario' => '0922729587', 'descripcion' => asset('api_doc_electronico')]
        ]);
        $this->info('Correcto!');
    }
}
