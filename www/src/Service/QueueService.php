<?php


namespace App\Service;


use Pheanstalk\Job;
use Pheanstalk\Pheanstalk;

class QueueService
{
    private $queue;
    /**
     * @var Pheanstalk
     */
    private $client;

    public function __construct(array $args)
    {
        $this->queue = 'autoparts';
        $this->client = new Pheanstalk('beanstalkd');
    }

    public function send($request)
    {
        return $this->client
            ->useTube($this->queue)
            ->put(json_encode($request)); // Отправьте что угодно с кодировкой в формате json – и готово!
    }

    public function listen()
    {
        $this->client->watch($this->queue); // Снова передайте имя очереди.

        while ($job = $this->client->reserve()) { // Продолжайте это делать... чтобы он всегда слушал.

            $result = $this->process($job);
            if ($result['status'] == 'delete')
                $this->client->delete($job);
            elseif($result['status'] == 'release')
                $this->client->release($result['job']);
        }
    }

    public function process($job)
    {
        $message = json_decode($job->getData(), true);

        $newJob = new Job($job->getId(), json_encode($message));
        return ['job' => $newJob, 'status' => 'release'];
    }
}