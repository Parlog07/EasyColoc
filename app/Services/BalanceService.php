<?php

namespace App\Services;

use App\Models\Colocation;
use Illuminate\Support\Collection;

class BalanceService
{
    public function buildSnapshot(Colocation $colocation): array
    {
        $members = $colocation->users()->wherePivotNull('left_at')->get();
        $expenses = $colocation->expenses()->with(['payer', 'category'])->get();
        $payments = $colocation->payments()->get();

        return $this->calculate($members, $expenses, $payments);
    }

    public function userBalance(Colocation $colocation, int $userId): float
    {
        $members = $colocation->users()->wherePivotNull('left_at')->get();
        $expenses = $colocation->expenses()->get();
        $payments = $colocation->payments()->get();

        return $this->userBalanceFromData($members, $expenses, $payments, $userId);
    }

    public function userBalanceFromData(
        Collection $members,
        Collection $expenses,
        Collection $payments,
        int $userId
    ): float {
        $share = $members->count() > 0
            ? (float) $expenses->sum('amount') / $members->count()
            : 0.0;

        $paid = (float) $expenses->where('payer_id', $userId)->sum('amount');
        $sent = (float) $payments->where('from_user_id', $userId)->sum('amount');
        $received = (float) $payments->where('to_user_id', $userId)->sum('amount');

        return round(($paid - $share) + $sent - $received, 2);
    }

    public function calculate(Collection $members, Collection $expenses, Collection $payments): array
    {
        $totalExpenses = (float) $expenses->sum('amount');
        $memberCount = $members->count();
        $share = $memberCount > 0 ? round($totalExpenses / $memberCount, 2) : 0.0;

        $balances = [];
        foreach ($members as $member) {
            $balance = $this->userBalanceFromData($members, $expenses, $payments, $member->id);

            $balances[] = [
                'user' => $member,
                'paid' => round((float) $expenses->where('payer_id', $member->id)->sum('amount'), 2),
                'balance' => $balance,
            ];
        }

        $creditors = [];
        $debtors = [];

        foreach ($balances as $item) {
            if ($item['balance'] > 0.01) {
                $creditors[] = ['user' => $item['user'], 'amount' => $item['balance']];
            }

            if ($item['balance'] < -0.01) {
                $debtors[] = ['user' => $item['user'], 'amount' => abs($item['balance'])];
            }
        }

        $settlements = [];
        foreach ($debtors as &$debtor) {
            foreach ($creditors as &$creditor) {
                if ($debtor['amount'] <= 0.01 || $creditor['amount'] <= 0.01) {
                    continue;
                }

                $amount = round(min($debtor['amount'], $creditor['amount']), 2);
                if ($amount <= 0) {
                    continue;
                }

                $settlements[] = [
                    'from' => $debtor['user'],
                    'to' => $creditor['user'],
                    'amount' => $amount,
                ];

                $debtor['amount'] = round($debtor['amount'] - $amount, 2);
                $creditor['amount'] = round($creditor['amount'] - $amount, 2);
            }
        }

        return [
            'members' => $members,
            'expenses' => $expenses,
            'payments' => $payments,
            'balances' => $balances,
            'totalExpenses' => round($totalExpenses, 2),
            'share' => $share,
            'settlements' => $settlements,
        ];
    }
}
