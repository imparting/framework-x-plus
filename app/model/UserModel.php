<?php

namespace app\model;

use support\Model;

class UserModel extends Model
{
    public function getUser()
    {
//        $this->db->begin();
//        $this->db->rollback();
//        $this->db->commit();
        $query = $this->db->selectQuery()
            ->table('user_rule')
            ->map(function ($item) {
                $item['v1'] = time();
                return $item;
            })->all();
//        $query = $this->selectQuery()->table('user_rule')->first();
//        $query = $this->insertQuery()->table('user_rule')
//            ->insert(['v0' => 0, 'v1' => 1, 'v2' => 2]);

//        $query = $this->db->updateQuery()->table('user_rule')
//            ->in('id', [481,483])
//            ->where('id=?', [483])
//            ->update(['v3' => time()]);
//
//        $query = $this->db->deleteQuery()->table('user_rule')
//            ->in('id', [435, 437, 439, 441])
//            ->orderBy('id')
//            ->limit(1)
//            ->delete();
        return $query;
    }
}