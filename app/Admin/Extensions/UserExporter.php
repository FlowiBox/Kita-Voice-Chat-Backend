<?php
namespace App\Admin\Extensions;
use App\Models\User;
use Encore\Admin\Grid\Exporters\ExcelExporter;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class UserExporter implements FromCollection,WithHeadings
{

    public $agency_id;
//    protected $fileName = 'users list.xlsx';


    public function __construct ($agency_id = null)
    {
        $this->agency_id = $agency_id;
//        $this->columns = ['id'];
    }

    /**
     * @inheritDoc
     */
    public function collection ()
    {
        $users = User::query ();
        if ($this->agency_id){
            $users = $users->where ('agency_id',$this->agency_id);
        }
        $users = $users->get ();
        $arr = [];
        foreach ($users as $user){
            $item['id']=$user->id;
            $arr[]=$item;
        }
        return collect ([$arr]);
    }

    /**
     * @inheritDoc
     */
    public function headings (): array
    {
        return [
            'id'
        ];
    }
}
