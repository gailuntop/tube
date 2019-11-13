<?php

namespace Ryp\Tube\Activity\Coupon;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Ryp\Tube\Activity\Models\Marketing\CouponCollection;
use Ryp\Tube\Activity\Models\Marketing\PromotionsActivity;

class CouponComposer
{

    /**
     * 获取推荐人的identity_id
     * @param $identity_id
     * @param $activityId
     * @param $activationMethod
     * @return int
     */
    public static function getCouponReferrerInfo($identity_id, $activityTypeSn, $activationMethod)
    {
        $promotionsActivity = PromotionsActivity::whereHas('activityType', function ($query) use($activityTypeSn) {
            $query->where('activity_type_sn', $activityTypeSn);
        })
            ->get();
        if (!$promotionsActivity) {
            return 0;
        }

        $activityArray = $promotionsActivity->keyBy('id')->keys();
        Log::info('活动id组'.\GuzzleHttp\json_encode($activityArray));

        $couponCollection = CouponCollection::where('identity_id', $identity_id)
            ->whereIn('activity_id', $activityArray)
//            ->whereNull('start_time')
            ->get();
        if (!$couponCollection) {
            return 0;
        }
        $referrerArray = $couponCollection->keyBy('referrer_identity_id')->keys();
        Log::info('推荐人组'.\GuzzleHttp\json_encode($referrerArray));

        return $referrerArray[0] ?? 0;
    }


    /**
     * 激活自己的券（不含推荐人）
     */
    public static function activationMyCoupon($identity_id, $activityTypeSn, $activationMethod)
    {
        $promotionsActivity = PromotionsActivity::whereHas('activityType', function ($query) use($activityTypeSn) {
            $query->where('activity_type_sn', $activityTypeSn);
        })
            ->get();
        if (!$promotionsActivity) {
            return false;
        }
        $activityArray = $promotionsActivity->keyBy('id')->keys();
        Log::info('激活自己'.\GuzzleHttp\json_encode($activityArray));
        
        $couponList = CouponCollection::whereHas('Coupon', function ($query) use($activationMethod) {
            $query->where('activation_method', $activationMethod);
        })
            ->with(['Coupon' => function ($query) {
                $query->select('id', 'activation_days');
            }])
            ->where('identity_id', $identity_id)
            ->whereIn('activity_id', $activityArray)
            ->get(['id', 'coupon_id']);
        return self::activationCouponAction($couponList);
    }

    /**
     * @param $identity_id 而车主身份id
     * @param $activityId 活动id
     * @param $activationMethod 激活方法
     * @return bool
     * @author xw
     */
    public static function activationCoupon($identity_id, $activityTypeSn, $activationMethod, $referrerId = 0)
    {
        $promotionsActivity = PromotionsActivity::whereHas('activityType', function ($query) use($activityTypeSn) {
            $query->where('activity_type_sn', $activityTypeSn);
        })
            ->get();
        if (!$promotionsActivity) {
            return false;
        }
        $activityArray = $promotionsActivity->keyBy('id')->keys();
        Log::info(\GuzzleHttp\json_encode($activityArray));
        $couponList = CouponCollection::whereHas('Coupon', function ($query) use($activationMethod) {
            $query->where('activation_method', $activationMethod);
        })
            ->whereHas('couponReferrer', function ($query) use($referrerId) {
                if ($referrerId) {
                    $query->where('identity_id', $referrerId);
                }
            })
            ->with(['Coupon' => function ($query) {
                $query->select('id', 'activation_days');
            }])
            ->where('identity_id', $identity_id)
            ->whereIn('activity_id', $activityArray)
            ->get(['id', 'coupon_id']);
        return self::activationCouponAction($couponList);
    }

    /**
     * 激活券
     */
    public static function activationCouponAction($couponList)
    {
        if ($couponList->isEmpty()) {
            return false;
        }
        $startTimeSql = $endTimeSql = '';
        $idArr = [];
        foreach ($couponList as $k => $v) {
            $startTime = date('Y-m-d H:i:s');
            $endTime = date('Y-m-d H:i:s', time() + (3600 * 24 * $v->coupon->activation_days));
            $startTimeSql .= "when " . $v->id . " then '" . $startTime . "'";
            $endTimeSql .= "when " . $v->id . " then '" . $endTime . "'";
            $idArr[] = $v->id;
        }
        $sql = 'update marketing.coupon_collection set start_time = case id ' . $startTimeSql . ' end, end_time = case id ' . $endTimeSql . ' end where id in (' . implode(',', $idArr) . ')';
        DB::select($sql);
        return true;
    }

}