<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pk extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function getT1PerAttribute(){
        if (($this->t1_score + $this->t2_score) > 0){
            $res = $this->t1_score/($this->t1_score + $this->t2_score);
        }else{
            $res = 0;
        }
        return (double)$res;
    }

    public function getT2PerAttribute(){
        if (($this->t1_score + $this->t2_score) > 0){
            $res = $this->t2_score/($this->t1_score + $this->t2_score);
        }else{
            $res = 0;
        }
        return (double)$res;
    }
}
