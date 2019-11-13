<?php

namespace Ryp\Tube\Token;

use Illuminate\Support\Facades\DB;
use Ryp\Utils\RypException;

trait RypCheckTokenDatabase
{
    
    public function addData($table, $data)
    {
        return DB::table('user.'.$table)->insert($data);
    }
    
    public function findData($table, $data)
    {
        return DB::table('user.'.$table)->where($data)->first();
    }
    
    public function findDataAll($table, $data)
    {
        return DB::table('user.'.$table)->where($data)->get();
    }

    public function updateData($table, $data, $update)
    {
        return DB::table('user.'.$table)->where($data)->update($update);
    }

    public function deleteData($table, $data)
    {
        return DB::table('user.'.$table)->where($data)->delete();
    }
    
    public function joinData($table, $joinTable, $fieldA, $fieldB, $where)
    {
        return DB::table('user.'.$table)
            ->leftJoin('user.'.$joinTable, $fieldA, '=', $fieldB)
            ->where($where)
            ->get();
    }
    
}