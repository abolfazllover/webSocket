<?php

namespace App\Console\Commands;

use App\Models\User;
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
                   Storage::append('kafka.txt', json_encode($data));
                   switch ($data['action']){
                       default : return;
                       case 'chaneStateUser' : $this->chaneStateUser($data);break;
                   }
                   $consumer->commit($message);


               }catch (\Exception $exception){
                   Log::error('error : '.$exception->getMessage());
               }


            })->withAutoCommit(false)->build();

       $k->consume();
    }

    function chaneStateUser($data){
        $is_online=$data['is_online'];
        $fromID=$data['fromID'];
        User::find($fromID)->update(['is_online'=>$is_online]);
        Kafka::publish()->onTopic('node-message')->withBodyKey('chaneStateUser',['is_online'=>$is_online,'fromID'=>$fromID])->send();
    }
}
