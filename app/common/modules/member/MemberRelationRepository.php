<?php

namespace app\common\modules\member;

use app\common\models\MemberRelation;
use app\framework\Repository\Repository;

class MemberRelationRepository extends Repository
{
    protected $modelName = MemberRelation::class;

}