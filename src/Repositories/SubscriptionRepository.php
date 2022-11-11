<?php

namespace Volistx\FrameworkKernel\Repositories;

use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Volistx\FrameworkKernel\Enums\SubscriptionStatus;
use Volistx\FrameworkKernel\Models\Subscription;

class SubscriptionRepository
{
    public function Create(array $inputs): Model|Builder
    {
        return Subscription::query()->create([
            'user_id'           => $inputs['user_id'],
            'plan_id'           => $inputs['plan_id'],
            'hmac_token'        => $inputs['hmac_token'],
            'status'            => SubscriptionStatus::ACTIVE,
            'plan_activated_at' => Carbon::now(),
            'plan_expires_at'   => $inputs['plan_expires_at'],
            'plan_cancels_at'   => null,
            'plan_cancelled_at' => null,
        ]);
    }

    public function Clone($subscriptionID, $inputs): Builder|Model|null
    {
        $subscription = $this->Find($subscriptionID);

        if (!$subscription) {
            return null;
        }

        return Subscription::query()->create([
            'user_id'           => $subscription->user_id,
            'plan_id'           => $inputs['plan_id'] ?? $subscription->plan_id,
            'hmac_token'        => $inputs['hmac_token'] ?? $subscription->hmac_token,
            'status'            => SubscriptionStatus::ACTIVE,
            'plan_activated_at' => Carbon::now(),
            'plan_expires_at'   => $inputs['plan_expires_at'],
            'plan_cancels_at'   => null,
            'plan_cancelled_at' => null,
        ]);
    }

    public function Update($subscriptionID, array $inputs): ?object
    {
        $subscription = $this->Find($subscriptionID);

        if (!$subscription) {
            return null;
        }

        if (isset($inputs['status'])) {
            $subscription->status = $inputs['status'];
        }

        if (isset($inputs['hmac_token'])) {
            $subscription->hmac_token = $inputs['hmac_token'];
        }

        if (isset($inputs['plan_cancels_at'])) {
            $subscription->plan_cancels_at = $inputs['plan_cancels_at'];
        }

        if (isset($inputs['plan_cancelled_at'])) {
            $subscription->plan_cancelled_at = $inputs['plan_cancelled_at'];
        }

        $subscription->save();

        return $subscription;
    }

    public function Find($subscriptionID): ?object
    {
        return Subscription::with('plan')->where('id', $subscriptionID)->first();
    }

    public function Delete($subscriptionID): ?bool
    {
        $toBeDeletedSub = $this->Find($subscriptionID);

        if (!$toBeDeletedSub) {
            return null;
        }

        $toBeDeletedSub->delete();

        return true;
    }

    public function FindAll($search, $page, $limit): LengthAwarePaginator|null
    {
        //handle empty search
        if ($search === '') {
            $search = 'id:';
        }

        if (!str_contains($search, ':')) {
            return null;
        }

        $columns = Schema::getColumnListing('subscriptions');

        $values = explode(':', $search, 2);
        $columnName = strtolower(trim($values[0]));

        if (!in_array($columnName, $columns)) {
            return null;
        }

        $searchValue = strtolower(trim($values[1]));

        return Subscription::query()
            ->where($values[0], 'LIKE', "%$searchValue%")
            ->paginate($limit, ['*'], 'page', $page);
    }
}
