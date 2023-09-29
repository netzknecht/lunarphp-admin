<?php

namespace Lunar\Hub\Http\Controllers;

use Illuminate\Routing\Controller;
use Lunar\Hub\LunarHub;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ScriptsController extends Controller
{
    /**
     * @return \Lunar\Hub\Assets\Script
     *
     * @throws HttpException
     * @throws NotFoundHttpException
     */
    public function __invoke(string $script)
    {
        $asset = collect(LunarHub::scripts())
            ->filter(function ($asset) use ($script) {
                return $asset->name() === $script;
            })->first();

        if (! $asset) {
            abort(404);
        }

        return $asset;
    }
}
