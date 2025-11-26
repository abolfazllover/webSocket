<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Junges\Kafka\Facades\Kafka;

class KafkaConsume extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kafka:consume';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Consume Kafka messages';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Kafka consumer started...');
        $storage=new Storage();

       $k= Kafka::consumer()
            ->subscribe('test-topic')
            ->withHandler(function(\Junges\Kafka\Contracts\ConsumerMessage $message, \Junges\Kafka\Contracts\MessageConsumer $consumer) {
               try{
                   $data=  $message->getBody();

                   Log::info('Kafka message: ', $data);
                   Storage::put('kafka.txt', $data['message']['hello']);
                   $consumer->commit($message);
               }catch (\Exception $exception){
                   Log::error('error : '.$exception->getMessage());
               }


            })->build();

       $k->consume();
    }
}
