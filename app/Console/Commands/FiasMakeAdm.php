<?php

namespace App\Console\Commands;

use App\Jobs\CreateAdmAddresses;
use Illuminate\Console\Command;
use Liquetsoft\Fias\Laravel\LiquetsoftFiasBundle\Entity\AdmHierarchy;

class FiasMakeAdm extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fias:make:adm';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        AdmHierarchy::query()
            ->where('enddate', '>', now())
            ->chunk(500, function ($adms) {
                CreateAdmAddresses::dispatch($adms);
            });
    }
}
