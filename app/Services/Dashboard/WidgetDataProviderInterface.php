<?php

namespace App\Services\Dashboard;

interface WidgetDataProviderInterface
{
    public function getKey(): string;

    public function getData(array $filters = []): array;

    public function getExportData(array $filters = []): array;
}
