<?php

namespace App\Console;

use App\Actions\Orders\Magento\MagentoUpdate;
use App\Actions\Orders\WooCommerce\WooCommerceUpdate;
use App\Actions\Orders\Worten\WortenUpdate;
use App\Jobs\CheckOrdersCompleted;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $this->scheduleOrderUpdates($schedule);

        $schedule->call(function () {
            CheckOrdersCompleted::dispatch();
        })->everyFourHours();
    }

    /**
     * Schedule order updates for multiple platforms.
     */
    public function scheduleOrderUpdates(Schedule $schedule): void
    {
        $orderUpdateActions = [
            WooCommerceUpdate::class,
            MagentoUpdate::class,
            WortenUpdate::class,
        ];

        foreach ($orderUpdateActions as $action) {
            $schedule->call(function () use ($action) {
                (new $action())->execute();
            })->everyFiveMinutes();
        }
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');
        include base_path('routes/console.php');
    }

    protected function bootstrappers(): array
    {
        return array_merge(
            [\Bugsnag\BugsnagLaravel\OomBootstrapper::class],
            parent::bootstrappers(),
        );
    }
}
