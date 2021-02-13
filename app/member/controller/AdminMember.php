<?php

namespace app\member\controller;

use app\agency\model\Agency;
use app\agency\model\AgencyTeam;
use app\base\controller\AdminBaseController;
use app\conference\model\ConferenceMember;
use app\coupon\model\MemberCoupon;
use app\member\model\AuthMember;
use app\member\model\Member;
use app\member\model\MemberAddress;
use app\member\model\MemberFeedback;
use app\member\model\MemberToken;
use app\message\model\MessageMember;

class AdminMember extends AdminBaseController
{
    /**
     * 显示资源列表
     *
     * @return \think\response\Json
     */
    public function index()
    {
        $res = Member::getList(['authMember', 'mod']);
        return $this->sendResponse(0, $res);
    }

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        //

    }

    /**
     * 保存新建的资源
     *
     * @param \think\Request $request
     * @return \think\Response
     */
    public function save()
    {
        $auth = new AuthMember();
        $auth->genMember($this->data);
        if (!is_null($auth)) { // 发送用户注册消息
            MessageMember::sendRegisterMessage($auth);
        }
        return $this->sendResponse(0);
    }

    /**
     * 显示指定的资源
     *
     * @param int $id
     * @return \think\response\Json
     */
    public function read($id)
    {
        $res = Member::with(['authMember', 'mod'])->find($id);
        return $this->sendResponse(0, $res);
    }

    /**
     * 显示编辑资源表单页.
     *
     * @param int $id
     * @return \think\Response
     */
    public function edit($id)
    {

    }

    /**
     * 保存更新的资源
     *
     * @param int $id
     * @return \think\response\Json
     */
    public function update($id)
    {
        $model = Member::find($id);
        $model->save($this->getParams());
        return $this->sendResponse(0);
    }

    /**
     * 删除指定资源
     *
     * @param int $id
     */
    public function delete($id)
    {
        Member::destroy($id);
        MemberToken::where('member_id', $id)->delete();
        AuthMember::where('member_id', $id)->delete();
        MemberCoupon::where('member_id', $id)->delete();
        MemberFeedback::where('member_id', $id)->delete();
        MemberAddress::where('member_id', $id)->delete();
        AgencyTeam::where('member', $id)->delete();
        $agency = Agency::where(['member_id' => $id])->find();
        if (!is_null($agency)) {
            $agency->delete();
        }
        $this->sendResponse(SUCCESS);
    }

    public function memberSort()
    {
        $name = $this->getParams('nick_name', false);
        $street = $this->getParams('street', false);
        $station = $this->getParams('station', false);
        $sort = $this->getParams('sort', false);
        $start_time = $this->getParams('start_time', false);
        $start_time = ($start_time != false) ? $start_time : strtotime(date('Y-01-01 00:00:00', time()));
        $end_time = $this->getParams('end_time', false);
        $end_time = ($end_time != false) ? $end_time : strtotime(date('Y-12-31 23:59:59', time()));

        $map = [];
        if (!empty($name)) {
            $map[] = ['nick_name', 'like', '%' . $name . '%'];
        }
        if (!empty($street)) {
            $map[] = ['street', '=', $street];
        }
        if (!empty($station)) {
            $map[] = ['station', '=', $station];
        }
        $members = Member::where($map)->select();
        $members = $members->toArray();
        foreach ($members as $k => $v) {
            $count_map = [
                ['create_time', 'between', [$start_time, $end_time]],
                ['time_status', '=', 1],
                ['member_id', '=', $v['member_id']],
            ];
            trace('count map:' . json_encode($count_map), 'error');
            $members[$k]['integral_interval_count'] = ConferenceMember::where($count_map)->sum('integral');
        }
        usort($members, function ($a, $b) {
            if ($a['integral_interval_count'] == $b['integral_interval_count']) return 0;
            return ($a['integral_interval_count'] < $b['integral_interval_count']) ? 1 : -1;
        });
        if (!empty($sort)) {
            $temp = $members[$sort - 1];
            $members = [];
            $members[] = $temp;
        }
        return $this->sendResponse(0, $members);
    }

    /**
     * 街道统计
     */
    public function streetStatistics()
    {
        $start_time = $this->getParams('start_time', true);
        $end_time = $this->getParams('end_time', true);
        $data = [];
        $model = new Member();
        for ($i = 1; $i < 11; $i++) {
            $cell = [];
            $cell['street'] = $i;
            $members = $model->where('street', $i)->select();
            $cell['duty_count'] = $model->where('street', $i)->sum('duty_count');
            $cell['member_count'] = count($members);
            $cell['member_attend_count'] = count($members);
            $cell['duty_count'] = 0;
            $cell['integral_count'] = 0;
            foreach ($members as $m) {
//                $sql = ConferenceMember::hasWhere('conference', ['attend_time' => ['between', [$start_time, $end_time]]])->where(['time_status' => 1, 'member_id' => $m->member_id])->fetchSql(true)->sum('duty_num');
//                trace($i . 'sql:' . $sql, 'error');
                $cell['duty_count'] += ConferenceMember::hasWhere('conference', [['attend_time', 'between', [$start_time, $end_time]]])->where(['time_status' => 1, 'member_id' => $m->member_id])->sum('duty_num');
                $cell['integral_count'] += ConferenceMember::hasWhere('conference', [['attend_time', 'between', [$start_time, $end_time]]])->where(['time_status' => 1, 'member_id' => $m->member_id])->sum('ConferenceMember.integral');
            }
            $data[] = $cell;
        }
        return $this->sendResponse(0, $data);
    }
}