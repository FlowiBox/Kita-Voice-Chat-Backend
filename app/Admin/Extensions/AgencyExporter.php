<?php
namespace App\Admin\Extensions;
use App\Models\Agency;
use App\Models\User;
use Encore\Admin\Grid\Exporters\ExcelExporter;
use Maatwebsite\Excel\Facades\Excel;


class AgencyExporter extends ExportExcel
{



    public $agency_id;
    protected $fileName = 'users_list.csv';
    protected $headings = [
        "id",
        "name",
        'salary',
        'expenses',
        'net salary',
        'agent',
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
        $agencies = Agency::query ();
        $agencies = $agencies->get ();
        $arr = [];

        foreach ($agencies as $agency){
            $target = @$agency->target($this->month,$this->year);
            $item['id']=$agency->id;
            $item['name']=$agency->name;
            $item['salary']=@$target->sallary?:'0';
            $item['expenses']=@$target->cut_amount?:'0';
            $item['net_salary']=$agency->salary?:'0';
            $item['agent']=@$agency->owner->name?:@$agency->dashOwner->name;
            $item['month']=@$target->month;
            $item['year']=@$target->year;
            array_push ($arr,$item);
        }
        return collect ($arr);
    }




}
