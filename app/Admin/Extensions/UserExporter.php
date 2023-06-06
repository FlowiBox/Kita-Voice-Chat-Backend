<?php
namespace App\Admin\Extensions;
use App\Models\User;
use Encore\Admin\Grid\Exporters\ExcelExporter;
use Maatwebsite\Excel\Facades\Excel;


class UserExporter extends ExportExcel
{



    public $agency_id;
    protected $fileName = 'users_list.csv';
    protected $headings = [
        "uuid",
        "name",
        'diamonds',
        'salary',
        'expenses',
        'net salary',
        'agency',
        'month',
        'year'
    ];
    public $month;
    public $year;


    public function __construct ($agency_id = null,$month=null,$year=null)
    {
        $this->agency_id = $agency_id;
        $this->month = $month;
        $this->year = $year;
    }

    /**
     * @inheritDoc
     */
    public function collection ()
    {
        $users = User::query ()
            ->where ('agency_id','!=',0)
            ->where ('agency_id','!=','')
            ->where ('agency_id','!=',null)
        ;
        if ($this->agency_id){
            $users = $users->where ('agency_id',$this->agency_id);
        }
        $users = $users->get ();
        $arr = [];

        foreach ($users as $user){
            $target = @$user->target($this->month,$this->year);
            $item['uuid']=$user->uuid;
            $item['name']=$user->name;
            $item['diamonds']=$user->coins?:'0';
            $item['salary']=@$target->sallary?:'0';
            $item['expenses']=@$target->cut_amount?:'0';
            $item['net_salary']=$user->salary?:'0';
            $item['agency']=@$user->agency->name;
            $item['month']=@$target->month;
            $item['year']=@$target->year;
            array_push ($arr,$item);
        }
        return collect ($arr);
    }




}
