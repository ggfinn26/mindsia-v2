<?php

namespace App\Http\Controllers\Admin\Bonus;

use App\Http\Controllers\Controller;
use App\Repositories\Bonus\BonusRuleChangeHistoryRepository;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BonusRuleChangeHistoryController extends Controller
{
    public function __construct(
        private readonly BonusRuleChangeHistoryRepository $repo,
    ) {}

    public function index(Request $request): View
    {
        abort_unless($request->user()->can('bonus.rule.history.view'), 403);

        $filters = $request->only(['bonus_type', 'rule_id']);

        return view('admin.bonus.history.index', [
            'histories' => $this->repo->paginate($filters),
        ]);
    }
}
