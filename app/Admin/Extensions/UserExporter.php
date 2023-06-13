<?php
namespace App\Admin\Extensions;
use App\Models\User;
use Encore\Admin\Grid\Exporters\ExcelExporter;
use Maatwebsite\Excel\Facades\Excel;


class UserExporter extends ExportExcel
{



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





    /**
     * @inheritDoc
     */
    public function collection ()
    {
        $users = User::query ()
            ->where ('agency_id','!=',0)
            ->where ('agency_id','!=','')
            ->where ('agency_id','!=',null)
//            ->where ('salary','>',0)
        ;
        if (request ('agency_id')){
            $users = $users->where ('agency_id',request ('agency_id'));
        }
        $users = $users->get ();
        $arr = [];

        foreach ($users as $user){
            $target = @$user->target(request ('month'),request ('year'));
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
