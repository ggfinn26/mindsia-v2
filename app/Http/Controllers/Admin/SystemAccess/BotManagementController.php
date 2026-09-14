<?php

namespace App\Http\Controllers\Admin\SystemAccess;

use App\Http\Controllers\Controller;
use App\Http\Requests\SystemAccess\StoreBotRequest;
use App\Http\Requests\SystemAccess\UpdateBotRequest;
use App\Models\Bot;
use App\Repositories\SystemAccess\BotRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class BotManagementController extends Controller
{
    public function __construct(
        private readonly BotRepository $repository,
    ) {}

    public function index(): View
    {
        return view('system-access.bot.index', [
            'bots' => $this->repository->all(),
        ]);
    }

    public function create(): View
    {
        return view('system-access.bot.create');
    }

    public function store(StoreBotRequest $request): RedirectResponse
    {
        $this->repository->create($request->validated());

        return redirect()->route('system.bots.index')->with('success', 'Bot berhasil ditambahkan.');
    }

    public function edit(Bot $bot): View
    {
        return view('system-access.bot.edit', compact('bot'));
    }

    public function update(UpdateBotRequest $request, Bot $bot): RedirectResponse
    {
        $this->repository->update($bot, $request->validated());

        return redirect()->route('system.bots.index')->with('success', 'Bot berhasil diperbarui.');
    }

    public function toggleActive(Bot $bot): RedirectResponse
    {
        $this->repository->toggleActive($bot);

        return redirect()->route('system.bots.index')->with('success', 'Status bot diubah.');
    }
}
