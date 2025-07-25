<?php

namespace App\Controller;

use Prometheus\CollectorRegistry;
use Prometheus\RenderTextFormat;
use Prometheus\Storage\InMemory;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MetricsController {
    private $registry;

    public function __construct() {
        // Use InMemory or another adapter (Redis, APC)
        $this->registry = new CollectorRegistry(new InMemory());
    }

    /**
     * @Route("/metrics", name="metrics")
     */
    public function metrics(): Response {
        $counter = $this->registry->getOrRegisterCounter('app', 'requests_total', 'Total number of requests');
        $counter->inc();

        $renderer = new RenderTextFormat();
        $result = $renderer->render($this->registry->getMetricFamilySamples());

        return new Response($result, 200, ['Content-Type' => RenderTextFormat::MIME_TYPE]);
    }
}
