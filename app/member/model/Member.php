<?php


namespace app\member\model;


use app\agency\model\Agency;
use app\agency\model\AgencyTeam;
use app\base\model\BaseModel;
use app\conference\model\ConferenceMember;
use app\conference\model\ConferenceType;

class Member extends BaseModel
{
    protected $pk = 'member_id';

    public function memberToken()
    {
        return $this->hasOne(MemberToken::class, 'member_id', 'member_id');
    }

    public function authMember()
    {
        return $this->hasOne(AuthMember::class, 'member_id', 'member_id');
    }

    public function agency()
    {
        return $this->hasOne(Agency::class, 'member_id', 'member_id');
    }

    public function agencyTeam()
    {
        return $this->hasOne(AgencyTeam::class, 'member', 'member_id')->where('level', 1);
    }

    public function agencyTeams()
    {
        return $this->hasMany(AgencyTeam::class, 'member', 'member_id');
    }

    public function mod()
    {
        return $this->hasOne('MemberMod', 'member_id', 'member_id');
    }

    public function memberAddress()
    {
        return $this->hasMany(MemberAddress::class, 'member_id', 'member_id');
    }

    public function bank()
    {
        return $this->hasMany(MemberBank::class, 'member_id', 'member_id');
    }

    public function memberReal()
    {
        return $this->hasOne(MemberReal::class, 'member_id', 'member_id');
    }


    public static function onBeforeInsert(Member $model)
    {
        $model->member_sn = $model->genOpenId();
    }

    /**
     * @param $token
     * @return array|\think\Model|Member
     */
    public static function checkLogin($token)
    {
        return self::hasWhere('memberToken', function ($query) use ($token) {
            $query->where('token', $token);
        })->find();
    }

    public static function register(array $member_data, array $auth_data, string $invitation_code = '')
    {
        $member = self::create($member_data);
        $member->authMember()->save($auth_data);

        if ($invitation_code && saas_config('distribution.is_on') == 1) {
            $agency = Agency::hasWhere('member', ['member_sn' => $invitation_code])->find();
            if (!is_null($agency)) {
                $data = [
                    'leader_agency' => $agency->agency_id,
                    'leader_member' => $agency->member_id,
                    'parent_member' => $agency->member_id,
                    'level' => 1,
                ];
                $member->agencyTeam()->save($data);
            }
        }
        return $member;
    }

    public function updateToken()
    {
        $token_type = saas_config('member.token_type');
        if ($token_type == 2) {
            //多人登录
            if ($this->memberToken) {
                return $this->memberToken->token;
            }
        }

        $data['token'] = uniqid();
        $data['expired_time'] = time() + saas_config('member.token_expired');

        if ($this->memberToken) {
            $this->memberToken->save($data);
        } else {
            $this->memberToken()->save($data);
        }
        return $data['token'];
    }

    /**
     * 更新用户登录状态
     */
    public function updateLoginStatus()
    {
        $this->last_login_time = time();
        $this->save();
    }

    /**
     * 生成系统openid
     * @return string
     */
    private function genOpenId()
    {
        // 默认使用UUID
        if (saas_config('member.is_sn_on') != 1) {
            return uniqid();
        }

        // 使用配置规则
        $type = intval(saas_config('member.sn_type'));
        switch ($type) {
            case 1:
                $start = intval(saas_config('member.sn_number_start'));
                $start = empty($start) ? 0 : $start;
                $last_id = self::order('member_id DESC')->value('member_id');
                $sn = $start + $last_id + 1;
                break;
            default:
                $sn = uniqid();
                break;
        }
        return saas_config('member.sn_pre') . $sn . saas_config('member.sn_suffix');
    }

    /**
     * @return array
     */
    public function statistics()
    {
        $start_time = request()->param('start_time', false);
        $start_time = ($start_time != false) ? $start_time : strtotime(date('Y-01-01 00:00:00', time()));
        $end_time = request()->param('end_time', false);
        $end_time = ($end_time != false) ? $end_time : strtotime(date('Y-12-31 23:59:59', time()));

        $mass_count = ConferenceMember::where(['member_id' => $this->member_id, 'is_mass' => 1])->count();
        $types = ConferenceType::where(['parent' => 0])->select();
        foreach ($types as $t => $type) {
            $map_types = [];
            $map_types[] = $type['conference_type_id'];
            $conferenceTypeModel = new ConferenceType();
            $conferenceTypeModel->childrenTypes($type['conference_type_id']);
            foreach ($conferenceTypeModel->allTypes as $type_v) {
                $map_types[] = $type_v;
            }
            $has_map = [];
            $has_map[] = ['conference_type_id', 'in', $map_types];
            $has_map[] = ['attend_time', 'between', [$start_time, $end_time]];
            $map = [];
            $map[] = ['member_id', '=', $this->member_id];
            $map[] = ['time_status', '=', 1];
            $map[] = ['is_verify', '=', 1];
            $types[$t]['count'] = ConferenceMember::hasWhere('conference', $has_map)->where($map)->sum('ConferenceMember.duty_num');
            $types[$t]['integral'] = ConferenceMember::hasWhere('conference', $has_map)->where($map)->sum('ConferenceMember.integral');
            $map_unread = $map;
            $map_unread[] = ['is_read', '=', 0];
            $types[$t]['unread'] = ConferenceMember::hasWhere('conference', $has_map)->where($map_unread)->count();
        }
        return [
            'mass_count' => $mass_count,
            'types' => $types
        ];
    }

    /**
     * @return int|mixed|string
     */
    public function rank()
    {
        $model = new Member();
        $res = $model->order('integral_count DESC')->select();
        foreach ($res as $k => $v) {
            if ($v['member_id'] == $this->member_id) {
                return $k + 1;
            }
        }
        return -1;
    }

    /**
     * @return float|int
     */
    public function integral_weekly()
    {

        //当前日期
        $sdefaultDate = date("Y-m-d");
        //$first =1 表示每周星期一为开始日期 0表示每周日为开始日期
        $first = 1;
        //获取当前周的第几天 周日是 0 周一到周六是 1 - 6
        $w = date('w', strtotime($sdefaultDate));
        //获取本周开始日期，如果$w是0，则表示周日，减去 6 天
        $week_start_str = date('Y-m-d', strtotime("$sdefaultDate -" . ($w ? $w - $first : 6) . ' days'));
        $week_start = strtotime("$sdefaultDate -" . ($w ? $w - $first : 6) . ' days');
        //本周结束日期
        // $week_end=date('Y-m-d',strtotime("$week_start +6 days"));
        $week_end = strtotime("$week_start_str +6 days");
        $map['member_id'] = $this->member_id;
        $map['time_status'] = 1;
        // $map['create_time'] = ['between', [$week_start, $week_end]];
        $sum = ConferenceMember::hasWhere('conference', [['attend_time', 'between', [$week_start, $week_end]]])->where($map)->sum('ConferenceMember.integral');
        return $sum;
    }
}