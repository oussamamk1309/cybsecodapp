<?php

namespace App\Controller;

use Prometheus\CollectorRegistry;
use Prometheus\RenderTextFormat;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Prometheus\Storage\InMemory;

class MetricsController {
    private CollectorRegistry $registry;

    public function __construct() {
        $adapter = new InMemory();
        $this->registry = new CollectorRegistry($adapter);
    }

    #[Route('/metrics', name: 'metrics')]
    public function metrics(): Response {
        $counter = $this->registry->getOrRegisterCounter('app', 'hits_total', 'Total number of hits');

        $counter->inc();

        $renderer = new RenderTextFormat();
        $metrics = $renderer->render($this->registry->getMetricFamilySamples());

        return new Response($metrics, 200, [
            'Content-Type' => RenderTextFormat::MIME_TYPE,
        ]);
    }
}
